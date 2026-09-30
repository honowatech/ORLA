<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Données de base et comptes de test pour les tests Feature.
 * Le mot de passe de chaque compte créé est « ancien-mdp ».
 */
trait CreeDesDonnees
{
    protected int $typeClient;

    protected int $typeCoursier;

    /** Référentiels minimaux et abonnement actif (sinon Check_Sa_Client_Error bloque tout). */
    protected function setUpCreeDesDonnees(): void
    {

        // Types d'utilisateur : les contrôleurs comparent le libellé.
        DB::table('type_utilisateur')->insert([
            ['id' => 1, 'libelle' => 'Super Admin'],
            ['id' => 2, 'libelle' => 'agent'],
            ['id' => 3, 'libelle' => 'coursier'],
            ['id' => 4, 'libelle' => 'client'],
        ]);
        $this->typeCoursier = 3;
        $this->typeClient = 4;

        // Abonnement actif, sinon Check_Sa_Client_Error redirige toutes les pages.
        DB::table('super_admin_abonnement')->insert([
            'id' => 1, 'titre' => 'Mensuel', 'type_periode' => 'mois', 'montant' => 10000, 'statut' => 1,
        ]);
        DB::table('super_admin_client')->insert([
            'id' => 1, 'name' => 'Speedex', 'id_abonnement' => 1, 'date_fin' => now()->addMonth(), 'statut' => 1,
        ]);
        DB::table('type_client')->insert(['id' => 2, 'libelle' => 'Simple']);
        DB::table('ville')->insert(['id' => 1, 'libelle' => 'Douala', 'code' => 'Dla']);
    }

    protected function client(string $email): User
    {
        $idClient = DB::table('clients')->insertGetId([
            'noms' => $email, 'Prenoms' => 'Test', 'type_client' => 2, 'statut' => 1, 'telephone' => '6'.random_int(10000000, 99999999),
        ]);
        $idUser = DB::table('users')->insertGetId([
            'noms' => $email, 'email' => $email, 'password' => Hash::make('ancien-mdp'),
            'id_type_utilisateur' => $this->typeClient, 'id_client' => $idClient, 'statut' => 1,
        ]);
        DB::table('clients')->where('id', $idClient)->update(['id_utilisateur' => $idUser]);

        return User::findOrFail($idUser);
    }

    protected function coursier(string $email): User
    {
        $idCoursier = DB::table('coursiers')->insertGetId([
            'noms' => $email, 'prenoms' => 'Test', 'statut' => 1, 'telephone' => '6'.random_int(10000000, 99999999),
        ]);
        $idUser = DB::table('users')->insertGetId([
            'noms' => $email, 'email' => $email, 'password' => Hash::make('ancien-mdp'),
            'id_type_utilisateur' => $this->typeCoursier, 'id_coursier' => $idCoursier, 'statut' => 1,
        ]);

        return User::findOrFail($idUser);
    }

    protected function staff(string $email, int $type): User
    {
        $idUser = DB::table('users')->insertGetId([
            'noms' => $email, 'email' => $email, 'password' => Hash::make('ancien-mdp'),
            'id_type_utilisateur' => $type, 'statut' => 1,
        ]);

        return User::findOrFail($idUser);
    }

    protected function commande(User $saver, ?int $idClient = null, ?int $idCoursier = null, string $statut = 'attente'): int
    {
        return DB::table('commandes')->insertGetId([
            'id_client' => $idClient, 'id_coursier' => $idCoursier, 'id_saver' => $saver->id,
            'date_commande' => now(), 'date_livraison' => now(), 'type_commande' => 'simple',
            'mode_de_paiement' => 'coursier', 'statut' => $statut, 'montant_livraison' => 1000,
        ]);
    }
}
