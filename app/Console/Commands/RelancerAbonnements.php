<?php

namespace App\Console\Commands;

use App\Models\Entreprise;
use App\Notifications\AbonnementARenouveler;
use Illuminate\Console\Command;

/**
 * Relance quotidienne des entreprises dont l'essai ou l'abonnement arrive à
 * échéance. Chaque relance n'est envoyée qu'une fois (colonne derniere_relance).
 */
class RelancerAbonnements extends Command
{
    protected $signature = 'abonnements:relancer';

    protected $description = "Relance par email les entreprises dont l'essai ou l'abonnement arrive à échéance";

    /** Jours avant l'échéance où une relance est envoyée. */
    public const JOURS_ESSAI = [3, 1];
    public const JOURS_ABONNEMENT = [7, 3, 1, 0];

    public function handle(): int
    {
        $envoyees = 0;

        Entreprise::where('statut', 1)->whereNotNull('id_proprietaire')->with(['proprietaire', 'abonnement'])
            ->chunkById(100, function ($entreprises) use (&$envoyees) {
                foreach ($entreprises as $entreprise) {
                    $relance = $this->relance($entreprise);
                    if ($relance === null || $entreprise->proprietaire === null) {
                        continue;
                    }

                    [$motif, $echeance, $jours] = $relance;
                    $cle = $motif.':'.$echeance->toDateString().':'.$jours;
                    if ($entreprise->derniere_relance === $cle) {
                        continue;
                    }

                    try {
                        $entreprise->proprietaire->notify(new AbonnementARenouveler($motif, $echeance, $jours));
                    } catch (\Throwable $e) {
                        report($e);
                        continue;
                    }

                    $entreprise->derniere_relance = $cle;
                    $entreprise->save();
                    $envoyees++;
                }
            });

        $this->info("$envoyees relance(s) envoyée(s).");

        return self::SUCCESS;
    }

    /** @return array{0: string, 1: \Illuminate\Support\Carbon, 2: int}|null */
    protected function relance(Entreprise $entreprise): ?array
    {
        if ($entreprise->enEssai()) {
            $jours = (int) now()->startOfDay()->diffInDays($entreprise->essai_jusqu_au->copy()->startOfDay());

            return in_array($jours, self::JOURS_ESSAI, true) ? ['essai', $entreprise->essai_jusqu_au, $jours] : null;
        }

        if ($entreprise->date_fin === null || $entreprise->id_abonnement === null) {
            return null;
        }

        if ($entreprise->abonnementValide()) {
            $jours = (int) now()->startOfDay()->diffInDays($entreprise->date_fin->copy()->startOfDay(), false);

            return in_array($jours, self::JOURS_ABONNEMENT, true) ? ['abonnement', $entreprise->date_fin, $jours] : null;
        }

        return ['expire', $entreprise->finAcces(), 0];
    }
}
