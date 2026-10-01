<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Parcours de fumée de l'espace administrateur : chaque écran principal doit
 * se rendre sans erreur avec les données de démonstration.
 */
class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_connexion_redirige_l_administrateur_vers_son_tableau_de_bord(): void
    {
        $admin = $this->utilisateurDemo();

        $this->actingAs($admin)->get('/home')->assertRedirect(route('home.admin'));
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    #[DataProvider('pagesAdmin')]
    public function test_les_pages_de_l_espace_admin_se_rendent(string $uri): void
    {
        $admin = $this->utilisateurDemo();

        $this->actingAs($admin)->get($uri)->assertOk();
    }

    public static function pagesAdmin(): array
    {
        return [
            'utilisateurs' => ['/admin/users'],
            'nouvel utilisateur' => ['/admin/users/create'],
            'clients' => ['/admin/clients'],
            'nouveau client' => ['/admin/clients/create'],
            'agents' => ['/admin/agents'],
            'commandes' => ['/admin/commandes'],
            'nouvelle commande' => ['/admin/commandes/create'],
            'produits' => ['/admin/produits'],
            'quartiers' => ['/admin/quartier'],
            'villes' => ['/admin/ville'],
            'rapport livraison' => ['/admin/details_commande'],
            'grille tarifaire' => ['/admin/montant_livraison'],
            'coursiers' => ['/multi/coursiers'],
            'nouveau coursier' => ['/multi/coursiers/create'],
            'zones' => ['/multi/zone'],
            'nouvelle zone' => ['/multi/zone/create'],
            'véhicules' => ['/multi/vehicule'],
            'types de véhicule' => ['/multi/type_vehicule'],
        ];
    }

    public function test_un_compte_desactive_est_renvoye_vers_la_page_d_erreur(): void
    {
        $admin = $this->utilisateurDemo();
        $admin->forceFill(['statut' => 0])->save();

        $this->actingAs($admin)->get('/home')->assertRedirect(route('home.error'));
    }
}
