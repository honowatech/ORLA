<?php

namespace Tests\Feature\Api;

use App\Models\clients\Clients;
use App\Models\coursiers\Coursiers;
use App\Models\commandes\Commandes;
use App\Models\quartier\Quartier;
use App\Models\User;
use App\Models\ville\Ville;
use App\Models\zone\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected Ville $ville;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Super Admin', 'Agent', 'Coursier', 'Client'] as $i => $libelle) {
            DB::table('type_utilisateur')->insert(['id' => $i + 1, 'libelle' => $libelle, 'created_at' => now(), 'updated_at' => now()]);
        }

        foreach (['Entreprise', 'Particulier'] as $i => $libelle) {
            DB::table('type_client')->insert(['id' => $i + 1, 'libelle' => $libelle, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Agent enregistreur par défaut des commandes de test (id_saver = 1).
        $this->utilisateur(2, ['email' => 'agent@example.com']);

        $this->abonnementValide();

        $this->ville = new Ville;
        $this->ville->libelle = 'Douala';
        $this->ville->code = 'Dla';
        $this->ville->save();
    }

    protected function abonnementValide(): void
    {
        $idAbonnement = DB::table('super_admin_abonnement')->insertGetId([
            'titre' => 'Mensuel',
            'type_periode' => 'mois',
            'statut' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('super_admin_client')->insert([
            'name' => 'Client SaaS',
            'statut' => 1,
            'id_abonnement' => $idAbonnement,
            'date_fin' => now()->addMonth(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function utilisateur(int $type, array $attributs = []): User
    {
        $user = new User;
        $user->noms = $attributs['noms'] ?? 'Utilisateur';
        $user->email = $attributs['email'] ?? uniqid('u').'@example.com';
        $user->password = Hash::make($attributs['password'] ?? 'secret123');
        $user->id_type_utilisateur = $type;
        $user->statut = $attributs['statut'] ?? 1;
        $user->id_coursier = $attributs['id_coursier'] ?? null;
        $user->id_client = $attributs['id_client'] ?? null;
        $user->save();

        return $user;
    }

    protected function coursier(array $attributs = []): User
    {
        $coursier = new Coursiers;
        $coursier->noms = 'Mbappe';
        $coursier->prenoms = 'Paul';
        $coursier->telephone = '690000001';
        $coursier->statut = 1;
        $coursier->save();

        return $this->utilisateur(3, ['id_coursier' => $coursier->id, ...$attributs]);
    }

    protected function client(array $attributs = []): User
    {
        $client = new Clients;
        $client->noms = 'Ngo';
        $client->Prenoms = 'Marie';
        $client->type_client = 2;
        $client->telephone = '670000002';
        $client->statut = 1;
        $client->save();

        return $this->utilisateur(4, ['id_client' => $client->id, ...$attributs]);
    }

    protected function quartier(string $libelle, ?int $idZone = null): Quartier
    {
        $quartier = new Quartier;
        $quartier->libelle = $libelle;
        $quartier->id_ville = $this->ville->id;
        $quartier->id_zone = $idZone;
        $quartier->save();

        return $quartier;
    }

    protected function zone(string $libelle): Zone
    {
        $zone = new Zone;
        $zone->libelle = $libelle;
        $zone->id_ville = $this->ville->id;
        $zone->statut = 1;
        $zone->save();

        return $zone;
    }

    protected function commande(array $attributs = []): Commandes
    {
        $depart = $attributs['quartier_colis'] ?? $this->quartier('Akwa');
        $arrivee = $attributs['quartier_livraison'] ?? $this->quartier('Bonapriso');
        unset($attributs['quartier_colis'], $attributs['quartier_livraison']);

        $commande = new Commandes;
        foreach ([
            'id_saver' => 1,
            'date_commande' => now(),
            'date_livraison' => now(),
            'type_commande' => 'simple',
            'nom_client' => 'Ngo Marie',
            'telephone' => '670000002',
            'adresse_colis' => '690000010*/*Akwa*/*Face pharmacie',
            'id_quartier_colis' => $depart->id,
            'adresse_livraison' => '690000020*/*Bonapriso*/*Immeuble bleu*/*Jean',
            'id_quartier_livraison' => $arrivee->id,
            'montant_livraison' => 1500,
            'mode_de_paiement' => 'coursier',
            'description' => 'Colis fragile',
            'statut' => 'attribue',
            ...$attributs,
        ] as $cle => $valeur) {
            $commande->{$cle} = $valeur;
        }
        $commande->save();

        return $commande;
    }
}
