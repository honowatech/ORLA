<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreeDesDonnees;
use Tests\TestCase;

/**
 * Changements de statut de commande depuis chaque espace
 * (service ChangerStatutCommande).
 */
class StatutCommandeTest extends TestCase
{
    use CreeDesDonnees;
    use RefreshDatabase;

    private function statut(int $commande): string
    {
        return DB::table('commandes')->where('id', $commande)->value('statut');
    }

    public function test_l_admin_attribue_puis_livre_une_commande(): void
    {
        $admin = $this->staff('admin@example.com', 1);
        $coursier = $this->coursier('coursier@example.com');
        $commande = $this->commande($admin);

        $this->actingAs($admin)->delete(route('commandes.destroy', $commande), [
            'statut' => 'attribue', 'id_coursier' => $coursier->id_coursier,
        ]);
        $this->assertSame('attribue', $this->statut($commande));
        $this->assertEquals($coursier->id_coursier, DB::table('commandes')->where('id', $commande)->value('id_coursier'));

        $this->actingAs($admin)->delete(route('commandes.destroy', $commande), ['statut' => 'livre']);
        $ligne = DB::table('commandes')->where('id', $commande)->first();
        $this->assertSame('livre', $ligne->statut);
        $this->assertNotNull($ligne->date_livre);
        $this->assertNotNull($ligne->date_mise_encours);
    }

    public function test_une_commande_terminee_ne_change_plus_de_statut(): void
    {
        $admin = $this->staff('admin@example.com', 1);
        $commande = $this->commande($admin, null, null, 'livre');

        // Plantait auparavant (variable $depart non définie).
        $this->actingAs($admin)->delete(route('commandes.destroy', $commande), ['statut' => 'annulee'])
            ->assertRedirect();
        $this->assertSame('livre', $this->statut($commande));
    }

    public function test_le_coursier_signale_un_echec_de_livraison(): void
    {
        $coursier = $this->coursier('coursier@example.com');
        $commande = $this->commande($coursier, null, $coursier->id_coursier, 'encours');

        // Plantait auparavant (variable $reponse non définie pour « echoue »).
        $this->actingAs($coursier)->delete(route('Coursiercommandes.destroy', $commande), ['statut' => 'echoue'])
            ->assertRedirect();
        $this->assertSame('echoue', $this->statut($commande));
        $this->assertSame('Livraison échouée', DB::table('activity')->value('title'));
    }

    public function test_le_coursier_met_en_cours_une_commande_attribuee(): void
    {
        $coursier = $this->coursier('coursier@example.com');
        $commande = $this->commande($coursier, null, $coursier->id_coursier, 'attribue');

        $this->actingAs($coursier)->delete(route('Coursiercommandes.destroy', $commande), ['statut' => 'encours']);

        $this->assertSame('encours', $this->statut($commande));
        $this->assertNotNull(DB::table('commandes')->where('id', $commande)->value('date_mise_encours'));
    }
}
