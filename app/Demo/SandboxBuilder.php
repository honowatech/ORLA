<?php

namespace App\Demo;

use App\Demo\Generator\ActivitySimulator;
use App\Demo\Generator\DemoAgency;
use App\Demo\Generator\SandboxCheck;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Construit un bac : fichier SQLite au schéma courant, agence mise en place,
 * activité rejouée, contrôle de cohérence. Le bac n'est inscrit comme prêt
 * qu'une fois entier et cohérent ; sinon son fichier est supprimé.
 */
class SandboxBuilder
{
    public function __construct(
        private ActivitySimulator $simulator,
        private SandboxCheck $check,
    ) {}

    /**
     * @param  array{seed?:int, history_days?:int, ahead_days?:int}  $options
     */
    public function build(array $options = []): Sandbox
    {
        File::ensureDirectoryExists(Demo::directory());

        $sandbox = new Sandbox;
        $sandbox->name = Str::lower(Str::random(40));
        $sandbox->seed = $options['seed'] ?? random_int(1, 2_000_000_000);
        $sandbox->save();

        $file = $sandbox->path();
        $building = $file.'.building';

        try {
            File::copy($this->schemaTemplate(), $building);
            $this->populate($building, $sandbox->seed, $options);
            File::move($building, $file);
        } catch (Throwable $e) {
            File::delete($building);
            $sandbox->delete();
            throw $e;
        }

        $sandbox->built_at = now();
        $sandbox->built_on = now(config('demo.timezone'))->toDateString();
        $sandbox->save();

        return $sandbox;
    }

    /**
     * Remplit un fichier : mise en place, rejeu, contrôle, dans une seule
     * transaction. L'horloge est toujours rétablie.
     */
    public function populate(string $file, int $seed, array $options = []): void
    {
        $now = now();
        Demo::useFile($file);
        try {
            DB::transaction(function () use ($seed, $options, $now) {
                $agency = DemoAgency::setUp($seed, $now, $options['history_days'] ?? config('demo.history_days'));
                $this->simulator->run($agency, $now, $options['ahead_days'] ?? config('demo.ahead_days'));
                $this->check->assert($agency, $now);
            });
        } finally {
            DemoClock::restore();
            Demo::leave();
        }
    }

    /**
     * Base vide au schéma courant, migrée une fois puis copiée pour chaque bac.
     * Le nom dépend du contenu des migrations : une nouvelle migration donne
     * un nouveau modèle.
     */
    public function schemaTemplate(): string
    {
        $migrations = collect(File::files(database_path('migrations')))
            ->sortBy(fn ($f) => $f->getFilename())
            ->map(fn ($f) => $f->getFilename().md5_file($f->getPathname()))
            ->implode('|');
        $template = Demo::directory().DIRECTORY_SEPARATOR.'schema-'.substr(md5($migrations), 0, 12).'.sqlite';

        if (File::exists($template)) {
            return $template;
        }

        $tmp = $template.'.'.Str::random(8);
        File::put($tmp, '');
        Demo::useFile($tmp);
        try {
            $code = Artisan::call('migrate', ['--database' => Demo::CONNECTION, '--force' => true]);
            if ($code !== 0) {
                throw new RuntimeException('Migration du modèle de bac impossible : '.Artisan::output());
            }
        } catch (Throwable $e) {
            Demo::leave();
            File::delete($tmp);
            throw $e;
        }
        Demo::leave();

        foreach (File::glob(Demo::directory().DIRECTORY_SEPARATOR.'schema-*.sqlite') as $old) {
            File::delete($old);
        }
        File::move($tmp, $template);

        return $template;
    }
}
