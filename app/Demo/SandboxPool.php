<?php

namespace App\Demo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Réserve de bacs prêts : remplissage, attribution, purge.
 */
class SandboxPool
{
    public function __construct(private SandboxBuilder $builder) {}

    /**
     * Bacs prêts et non attribués, encore frais : construits aujourd'hui
     * (calendrier du marché) et depuis moins de pool_max_age_hours.
     */
    public function fresh(): Builder
    {
        return Sandbox::query()
            ->whereNull('claimed_at')
            ->whereNotNull('built_at')
            ->whereDate('built_on', now(config('demo.timezone'))->toDateString())
            ->where('built_at', '>', now()->subHours(config('demo.pool_max_age_hours')));
    }

    public function readyCount(): int
    {
        return $this->fresh()->count();
    }

    public function atCapacity(): bool
    {
        return Sandbox::open()->count() >= config('demo.max_active');
    }

    /**
     * Remet un bac prêt à un visiteur. Deux visiteurs simultanés ne reçoivent
     * jamais le même : l'UPDATE ne réussit que si le bac est encore libre
     * (comparer-et-poser, portable sur MySQL, MariaDB et SQLite), puis le bac
     * est relu par son jeton.
     *
     * Réserve vide : construit un bac à la volée si la configuration le permet.
     */
    public function claim(): ?Sandbox
    {
        if ($this->atCapacity()) {
            return null;
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $id = $this->fresh()->orderBy('id')->value('id');
            if ($id === null) {
                break;
            }
            if ($sandbox = $this->take($id)) {
                if (File::exists($sandbox->path())) {
                    return $sandbox;
                }
                // Ligne sans fichier (fichier effacé à la main, disque…) : jamais servie.
                $this->destroy($sandbox);
            }
        }

        if (! config('demo.build_on_demand')) {
            return null;
        }

        return $this->take($this->builder->build()->id);
    }

    private function take(int $id): ?Sandbox
    {
        $token = Str::random(48);
        $now = now();
        $ends = $now->copy()->addHours(config('demo.max_hours'));
        $expires = $now->copy()->addMinutes(config('demo.idle_minutes'));
        if ($expires->greaterThan($ends)) {
            $expires = $ends->copy();
        }

        $taken = Sandbox::query()
            ->whereKey($id)
            ->whereNull('claimed_at')
            ->whereNotNull('built_at')
            ->update([
                'claimed_at' => $now,
                'claim_token' => $token,
                'expires_at' => $expires,
                'ends_at' => $ends,
                'updated_at' => $now,
            ]);

        return $taken === 1 ? Sandbox::where('claim_token', $token)->first() : null;
    }

    /**
     * Construit jusqu'à pool_size bacs prêts. Rien si la démo est désactivée
     * ou au plafond. Un échec arrête la tournée et remonte.
     *
     * @return int le nombre de bacs construits
     */
    public function refill(?callable $onBuilt = null): int
    {
        if (! Demo::enabled() || $this->atCapacity()) {
            return 0;
        }

        $built = 0;
        $missing = config('demo.pool_size') - $this->readyCount();
        for ($i = 0; $i < $missing && ! $this->atCapacity(); $i++) {
            $sandbox = $this->builder->build();
            $built++;
            if ($onBuilt) {
                $onBuilt($sandbox);
            }
        }

        return $built;
    }

    /**
     * Supprime définitivement (fichier et registre) :
     * - les bacs remis dont la session est finie (terminés, inactifs, durée dépassée) ;
     * - les bacs jamais servis et périmés ;
     * - les constructions interrompues depuis plus d'une heure ;
     * - les fichiers orphelins.
     *
     * @return int le nombre de bacs supprimés
     */
    public function prune(): int
    {
        $now = now();
        $today = now(config('demo.timezone'))->toDateString();
        $staleBefore = $now->copy()->subHours(config('demo.pool_max_age_hours'));

        $doomed = Sandbox::query()
            ->where(fn ($q) => $q
                ->where(fn ($q) => $q->whereNotNull('claimed_at')->where(fn ($q) => $q
                    ->whereNotNull('ended_at')
                    ->orWhere('expires_at', '<=', $now)
                    ->orWhere('ends_at', '<=', $now)))
                ->orWhere(fn ($q) => $q->whereNull('claimed_at')->whereNotNull('built_at')->where(fn ($q) => $q
                    ->where('built_at', '<=', $staleBefore)
                    ->orWhereDate('built_on', '!=', $today)))
                ->orWhere(fn ($q) => $q->whereNull('built_at')->where('created_at', '<=', $now->copy()->subHour())))
            ->get();

        // Bacs inscrits prêts dont le fichier a disparu.
        $missing = Sandbox::whereNotNull('built_at')->whereNotIn('id', $doomed->modelKeys())->get()
            ->reject(fn (Sandbox $s) => File::exists($s->path()));
        $doomed = $doomed->concat($missing);

        foreach ($doomed as $sandbox) {
            $this->destroy($sandbox);
        }

        // Fichiers sans ligne au registre (ligne supprimée à la main, crash…).
        $known = Sandbox::pluck('name')->flip();
        foreach (File::glob(Demo::directory().DIRECTORY_SEPARATOR.'*.sqlite*') as $file) {
            $base = basename($file);
            if (str_starts_with($base, 'schema-')) {
                continue;
            }
            $name = Str::before($base, '.sqlite');
            if (! $known->has($name) && filemtime($file) < $now->copy()->subHour()->getTimestamp()) {
                File::delete($file);
            }
        }

        return $doomed->count();
    }

    public function destroy(Sandbox $sandbox): void
    {
        File::delete([$sandbox->path(), $sandbox->path().'.building']);
        $sandbox->delete();
    }
}
