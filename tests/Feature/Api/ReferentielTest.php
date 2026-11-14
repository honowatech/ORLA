<?php

namespace Tests\Feature\Api;

use Laravel\Sanctum\Sanctum;

class ReferentielTest extends ApiTestCase
{
    public function test_villes_et_quartiers(): void
    {
        $this->quartier('Akwa');
        $this->quartier('Bonapriso');
        Sanctum::actingAs($this->client());

        $this->getJson('/api/v1/villes')->assertOk()->assertJsonPath('data.0.libelle', 'Douala');

        $this->getJson("/api/v1/villes/{$this->ville->id}/quartiers?q=akw")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.libelle', 'Akwa');

        $this->getJson('/api/v1/villes/999/quartiers')->assertNotFound();
    }

    public function test_estimation_tarif(): void
    {
        $a = $this->quartier('Akwa');
        $b = $this->quartier('Bonapriso');
        Sanctum::actingAs($this->coursier());

        $this->postJson('/api/v1/tarifs/estimation', [
            'id_quartier_collecte' => $a->id,
            'id_quartier_livraison' => $b->id,
        ])->assertOk()->assertJson(['data' => ['montant' => 1000, 'devise' => 'XAF', 'tarif_par_defaut' => true]]);
    }
}
