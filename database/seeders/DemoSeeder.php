<?php

namespace Database\Seeders;

use App\Models\Agents\Agents;
use App\Models\Clients\Clients;
use App\Models\Coursiers\Coursiers;
use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Client as LicenceClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Jeu de données de démonstration (local et tests) : une licence active,
 * un administrateur, un agent, un coursier et un client avec leurs comptes.
 * Idempotent : repose sur les adresses e-mail et numéros de téléphone.
 */
class DemoSeeder extends Seeder
{
    public const MOT_DE_PASSE = '11111111';

    public const ADMIN_EMAIL = 'test@example.com';

    public const AGENT_EMAIL = 'agent@example.com';

    public const COURSIER_EMAIL = 'coursier@example.com';

    public const CLIENT_EMAIL = 'client@example.com';

    public function run(): void
    {
        $this->licence();

        User::firstOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'noms' => 'Admin Speedex',
                'password' => self::MOT_DE_PASSE,
                'telephone' => '699000001',
                'id_type_utilisateur' => 1,
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
    }

    /**
     * Licence d'instance active : sans elle, le middleware Check_Sa_Client_Error bloque l'accès.
     */
    private function licence(): void
    {
        $abonnement = Abonnement::firstOrCreate(
            ['titre' => 'Mensuel'],
            ['accumulateur' => 1, 'type_periode' => 'mois', 'montant' => 15000, 'periode_grace' => 5, 'statut' => 1]
        );

        LicenceClient::firstOrCreate(
            ['telephone' => '699000000'],
            [
                'name' => 'Speedex',
                'adresse' => 'Douala',
                'cni' => 'DEMO000000',
                'id_abonnement' => $abonnement->id,
                'date_fin' => now()->addYear(),
                'date_dernier_paiement' => now(),
                'statut' => 1,
            ]
        );
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
