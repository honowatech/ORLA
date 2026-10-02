<?php

namespace Database\Seeders;

use App\Models\Agents\Agents;
use App\Models\Clients\Clients;
use App\Models\Coursiers\Coursiers;
use App\Models\Entreprise;
use App\Models\Quartier\Quartier;
use App\Models\SuperAdmin\Abonnement;
use App\Models\User;
use App\Models\Ville\Ville;
use App\Tenancy\CurrentEntreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Jeu de données de démonstration (local et tests) : une entreprise abonnée,
 * un administrateur, un agent, un coursier et un client avec leurs comptes.
 * Idempotent : repose sur le slug de l'entreprise, les e-mails et les téléphones.
 */
class DemoSeeder extends Seeder
{
    public const MOT_DE_PASSE = '11111111';

    public const ENTREPRISE_SLUG = 'speedex';

    public const ADMIN_EMAIL = 'test@example.com';

    public const AGENT_EMAIL = 'agent@example.com';

    public const COURSIER_EMAIL = 'coursier@example.com';

    public const CLIENT_EMAIL = 'client@example.com';

    public function run(): void
    {
        $entreprise = $this->entreprise();

        app(CurrentEntreprise::class)->runAs($entreprise, function () use ($entreprise) {
            $this->quartierSiege($entreprise);

            User::firstOrCreate(
                ['email' => self::ADMIN_EMAIL],
                [
                    'noms' => 'Admin Speedex',
                    'password' => self::MOT_DE_PASSE,
                    'telephone' => '699000001',
                    'id_type_utilisateur' => User::TYPE_ADMINISTRATEUR,
                    'statut' => 1,
                ]
            );

            $this->compteLie(
                Agents::class,
                ['noms' => 'Agent', 'prenoms' => 'Démo', 'telephone' => '699000002', 'statut' => 1],
                'id_agent',
                ['email' => self::AGENT_EMAIL, 'noms' => 'Agent Démo', 'telephone' => '699000002', 'id_type_utilisateur' => 2]
            );

            $this->compteLie(
                Coursiers::class,
                ['noms' => 'Coursier', 'prenoms' => 'Démo', 'telephone' => '699000003', 'statut' => 1],
                'id_coursier',
                ['email' => self::COURSIER_EMAIL, 'noms' => 'Coursier Démo', 'telephone' => '699000003', 'id_type_utilisateur' => 3]
            );

            $this->compteLie(
                Clients::class,
                ['noms' => 'Client', 'Prenoms' => 'Démo', 'telephone' => '699000004', 'type_client' => 2, 'statut' => 1],
                'id_client',
                ['email' => self::CLIENT_EMAIL, 'noms' => 'Client Démo', 'telephone' => '699000004', 'id_type_utilisateur' => 4]
            );
        });
    }

    /**
     * Entreprise de démonstration, abonnée pour un an.
     */
    private function entreprise(): Entreprise
    {
        $abonnement = Abonnement::firstOrCreate(
            ['titre' => 'Mensuel'],
            ['accumulateur' => 1, 'type_periode' => 'mois', 'montant' => 15000, 'periode_grace' => 5, 'statut' => 1]
        );

        $entreprise = Entreprise::withTrashed()->firstOrCreate(
            ['slug' => self::ENTREPRISE_SLUG],
            [
                'name' => 'Speedex',
                'email' => 'contact@speedex.example',
                'telephone' => '699000000',
                'adresse' => 'Douala',
                'id_abonnement' => $abonnement->id,
                'date_fin' => now()->addYear(),
                'date_dernier_paiement' => now(),
            ]
        );

        if ($entreprise->trashed()) {
            $entreprise->restore();
        }

        return $entreprise;
    }

    /**
     * Le quartier « Speedex » matérialise le siège : les contrôleurs le recherchent encore par libellé.
     */
    private function quartierSiege(Entreprise $entreprise): void
    {
        if ($entreprise->id_quartier_siege !== null) {
            return;
        }

        $ville = Ville::query()->orderBy('id')->firstOrFail();

        $quartier = Quartier::query()->whereRaw('UPPER(libelle) = ?', ['SPEEDEX'])->first()
            ?? Quartier::query()->forceCreate(['libelle' => 'Speedex', 'id_ville' => $ville->id]);

        $entreprise->forceFill(['id_quartier_siege' => $quartier->id])->save();
    }

    /**
     * Crée un profil métier (agent, coursier ou client) et le compte utilisateur lié,
     * en renseignant le lien dans les deux sens.
     *
     * @param  class-string<Model>  $modele
     */
    private function compteLie(string $modele, array $profil, string $cle, array $compte): void
    {
        $existant = $modele::query()->where('telephone', $profil['telephone'])->first();
        $instance = $existant ?? $modele::query()->forceCreate($profil);

        $utilisateur = User::firstOrCreate(
            ['email' => $compte['email']],
            $compte + ['password' => self::MOT_DE_PASSE, 'statut' => 1, $cle => $instance->id]
        );

        if ($instance->id_utilisateur !== $utilisateur->id) {
            $instance->forceFill(['id_utilisateur' => $utilisateur->id])->save();
        }
    }
}
