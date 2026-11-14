<?php

namespace Tests\Feature\Api;

use Laravel\Sanctum\Sanctum;

class CoursierCommandesTest extends ApiTestCase
{
    public function test_le_coursier_ne_voit_que_ses_commandes(): void
    {
        $paul = $this->coursier();
        $autre = $this->coursier();

        $this->commande(['id_coursier' => $paul->id_coursier]);
        $this->commande(['id_coursier' => $paul->id_coursier, 'statut' => 'supprime']);
        $this->commande(['id_coursier' => $autre->id_coursier]);

        Sanctum::actingAs($paul);

        $this->getJson('/api/v1/coursier/commandes')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.statut', 'attribue')
            ->assertJsonPath('data.0.collecte.lieu', 'Akwa')
            ->assertJsonPath('data.0.collecte.contact', '690000010')
            ->assertJsonPath('data.0.livraison.nom', 'Jean')
            ->assertJsonPath('data.0.livraison.quartier.libelle', 'Bonapriso')
            ->assertJsonPath('data.0.transitions_possibles', ['encours', 'livre', 'annulee', 'echoue'])
            ->assertJsonStructure(['data', 'links', 'meta' => ['current_page', 'total']]);
    }

    public function test_filtre_par_statut(): void
    {
        $paul = $this->coursier();
        $this->commande(['id_coursier' => $paul->id_coursier, 'statut' => 'attribue']);
        $this->commande(['id_coursier' => $paul->id_coursier, 'statut' => 'livre']);

        Sanctum::actingAs($paul);

        $this->getJson('/api/v1/coursier/commandes?statut=livre')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.statut', 'livre');
    }

    public function test_commande_d_un_autre_coursier_introuvable(): void
    {
        $paul = $this->coursier();
        $autre = $this->coursier();
        $commande = $this->commande(['id_coursier' => $autre->id_coursier]);

        Sanctum::actingAs($paul);

        $this->getJson("/api/v1/coursier/commandes/{$commande->id}")
            ->assertNotFound()
            ->assertJsonPath('message', 'Ressource introuvable.');
    }

    public function test_cycle_de_livraison(): void
    {
        $paul = $this->coursier();
        $commande = $this->commande(['id_coursier' => $paul->id_coursier]);

        Sanctum::actingAs($paul);

        $this->patchJson("/api/v1/coursier/commandes/{$commande->id}/statut", ['statut' => 'encours'])
            ->assertOk()
            ->assertJsonPath('data.statut', 'encours')
            ->assertJsonPath('data.transitions_possibles', ['livre', 'annulee', 'echoue']);

        $this->patchJson("/api/v1/coursier/commandes/{$commande->id}/statut", ['statut' => 'livre'])
            ->assertOk()
            ->assertJsonPath('data.statut', 'livre')
            ->assertJsonPath('data.transitions_possibles', []);

        $commande->refresh();
        $this->assertNotNull($commande->date_mise_encours);
        $this->assertNotNull($commande->date_livre);
        $this->assertDatabaseCount('activity', 2);

        $this->patchJson("/api/v1/coursier/commandes/{$commande->id}/statut", ['statut' => 'encours'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('statut');
    }

    public function test_le_coursier_ne_peut_pas_s_attribuer_une_commande(): void
    {
        $paul = $this->coursier();
        $commande = $this->commande(['id_coursier' => $paul->id_coursier, 'statut' => 'attente']);

        Sanctum::actingAs($paul);

        $this->patchJson("/api/v1/coursier/commandes/{$commande->id}/statut", ['statut' => 'attribue'])
            ->assertStatus(422);
    }

    public function test_resume(): void
    {
        $paul = $this->coursier();
        $this->commande(['id_coursier' => $paul->id_coursier, 'statut' => 'attribue']);
        $this->commande(['id_coursier' => $paul->id_coursier, 'statut' => 'livre']);

        Sanctum::actingAs($paul);

        $this->getJson('/api/v1/coursier/commandes/resume')
            ->assertOk()
            ->assertJsonPath('data.par_statut.attribue', 1)
            ->assertJsonPath('data.par_statut.livre', 1)
            ->assertJsonPath('data.aujourdhui.total', 2)
            ->assertJsonPath('data.aujourdhui.a_livrer', 1);
    }

    public function test_un_client_n_a_pas_acces_a_l_espace_coursier(): void
    {
        Sanctum::actingAs($this->client());

        $this->getJson('/api/v1/coursier/commandes')->assertForbidden()->assertJsonPath('code', 'role_non_autorise');
    }

    public function test_coursier_sans_profil_lie(): void
    {
        Sanctum::actingAs($this->utilisateur(3));

        $this->getJson('/api/v1/coursier/commandes')->assertForbidden()->assertJsonPath('code', 'profil_non_lie');
    }
}
