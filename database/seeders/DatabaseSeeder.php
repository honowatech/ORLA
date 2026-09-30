<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Base de développement : référentiels, abonnement actif et un compte par rôle.
 *
 * php artisan migrate:fresh --seed
 *
 * Le mot de passe des comptes vient de SEED_PASSWORD, sinon il est généré
 * et affiché. Refuse de s'exécuter en production.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('Seeder de développement : exécution refusée en production.');

            return;
        }

        // Seeder de développement, jamais exécuté avec la configuration en cache.
        $motDePasse = env('SEED_PASSWORD'); // @phpstan-ignore larastan.noEnvCallsOutsideOfConfig
        $motDePasse = $motDePasse ?: Str::password(12, symbols: false);
        $hash = Hash::make($motDePasse);
        $now = now();

        // Types d'utilisateur : les contrôles d'accès comparent ces libellés.
        DB::table('type_utilisateur')->insert([
            ['id' => 1, 'libelle' => 'Super Admin', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'libelle' => 'Agent', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'libelle' => 'Coursier', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'libelle' => 'Client', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('type_client')->insert([
            ['id' => 1, 'libelle' => 'Entreprise', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'libelle' => 'Simple', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Géographie minimale.
        DB::table('ville')->insert(['id' => 1, 'libelle' => 'Douala', 'code' => 'Dla', 'created_at' => $now, 'updated_at' => $now]);
        DB::table('zone')->insert(['id' => 1, 'libelle' => 'Centre', 'id_ville' => 1, 'statut' => 1, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('quartier')->insert([
            ['libelle' => 'Speedex', 'id_ville' => 1, 'id_zone' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['libelle' => 'Akwa', 'id_ville' => 1, 'id_zone' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Plateforme SaaS : compte Super Admin, API de paiement et abonnement actif.
        DB::table('super_admin')->insert([
            'name' => 'Super Admin', 'email' => 'superadmin@example.com', 'password' => $hash, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('super_admin_api')->insert([
            'name' => 'Monetbill', 'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('super_admin_contact')->insert([
            ['name' => 'phone', 'value' => '600000000', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'email', 'value' => 'contact@example.com', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('super_admin_abonnement')->insert([
            'id' => 1, 'titre' => 'Mensuel', 'accumulateur' => 1, 'type_periode' => 'mois',
            'montant' => 10000, 'periode_grace' => 5, 'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('super_admin_client')->insert([
            'name' => 'Speedex (dev)', 'id_abonnement' => 1, 'date_fin' => $now->copy()->addYear(),
            'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);

        // Un compte par rôle de l'application de livraison.
        $idCoursier = DB::table('coursiers')->insertGetId([
            'noms' => 'Coursier', 'prenoms' => 'Test', 'telephone' => '690000003', 'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $idClient = DB::table('clients')->insertGetId([
            'noms' => 'Client', 'Prenoms' => 'Test', 'telephone' => '690000004', 'type_client' => 2, 'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $idAgent = DB::table('agents')->insertGetId([
            'noms' => 'Agent', 'prenoms' => 'Test', 'telephone' => '690000002', 'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $comptes = [
            ['admin@example.com', 'Admin Speedex', 1, [], '690000001'],
            ['agent@example.com', 'Agent Speedex', 2, ['id_agent' => $idAgent], '690000002'],
            ['coursier@example.com', 'Coursier Test', 3, ['id_coursier' => $idCoursier], '690000003'],
            ['client@example.com', 'Client Test', 4, ['id_client' => $idClient], '690000004'],
        ];
        foreach ($comptes as [$email, $noms, $type, $lien, $telephone]) {
            $idUser = DB::table('users')->insertGetId([
                'noms' => $noms, 'email' => $email, 'password' => $hash, 'telephone' => $telephone,
                'id_type_utilisateur' => $type, 'id_ville' => 1, 'statut' => 1, 'created_at' => $now, 'updated_at' => $now,
            ] + $lien);
            if (isset($lien['id_agent'])) {
                DB::table('agents')->where('id', $idAgent)->update(['id_utilisateur' => $idUser]);
            }
            if (isset($lien['id_coursier'])) {
                DB::table('coursiers')->where('id', $idCoursier)->update(['id_utilisateur' => $idUser]);
            }
            if (isset($lien['id_client'])) {
                DB::table('clients')->where('id', $idClient)->update(['id_utilisateur' => $idUser]);
            }
        }

        // Quelques commandes du client de test, à différentes étapes.
        $akwa = DB::table('quartier')->where('libelle', 'Akwa')->value('id');
        $idSaver = DB::table('users')->where('email', 'admin@example.com')->value('id');
        foreach (['attente' => null, 'attribue' => $idCoursier, 'encours' => $idCoursier, 'livre' => $idCoursier] as $statut => $coursier) {
            DB::table('commandes')->insert([
                'id_client' => $idClient, 'nom_client' => 'Client Test', 'telephone' => '690000004',
                'id_coursier' => $coursier, 'id_saver' => $idSaver, 'type_commande' => 'simple',
                'date_commande' => $now, 'date_livraison' => $now,
                'date_mise_encours' => in_array($statut, ['encours', 'livre']) ? $now : null,
                'date_livre' => $statut === 'livre' ? $now : null,
                'adresse_colis' => '690000004*/*Akwa*/*Devant la pharmacie',
                'adresse_livraison' => '690000005*/*Akwa*/*Immeuble bleu*/*Destinataire Test',
                'id_quartier_colis' => $akwa, 'id_quartier_livraison' => $akwa,
                'montant_livraison' => 1000, 'description' => 'Commande de démonstration ('.$statut.')',
                'mode_de_paiement' => 'coursier', 'statut' => $statut,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $this->command->info('Comptes créés : admin@, agent@, coursier@, client@example.com (application)');
        $this->command->info('               superadmin@example.com (espace /superadmin)');
        $this->command->warn('Mot de passe : '.$motDePasse);
    }
}
