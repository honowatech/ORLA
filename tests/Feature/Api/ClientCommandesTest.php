<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

class ClientCommandesTest extends ApiTestCase
{
    private function payload(array $surcharges = []): array
    {
        return array_replace_recursive([
            'collecte' => ['contact' => '690000010', 'quartier' => 'Akwa', 'id_ville' => $this->ville->id, 'description' => 'Face pharmacie'],
            'livraison' => ['nom' => 'Jean', 'contact' => '690000020', 'quartier' => 'Bonapriso', 'id_ville' => $this->ville->id],
            'date_livraison' => now()->toDateString(),
            'heure_livraison' => '15:30',
            'montant_a_recuperer' => 5000,
            'mode_de_paiement' => 'coursier',
            'description' => 'Un carton de livres',
        ], $surcharges);
    }

    public function test_creation_de_commande_avec_tarif_calcule_par_le_serveur(): void
    {
        $zoneA = $this->zone('Zone A');
        $zoneB = $this->zone('Zone B');
        $akwa = $this->quartier('Akwa', $zoneA->id);
        $bonapriso = $this->quartier('Bonapriso', $zoneB->id);
        DB::table('montant_livraison')->insert([
            'id_zone_colis' => $zoneA->id, 'id_zone_livraison' => $zoneB->id, 'montant' => 2000,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $user = $this->client();
        Sanctum::actingAs($user);

        $reponse = $this->postJson('/api/v1/client/commandes', $this->payload([
            'collecte' => ['id_quartier' => $akwa->id, 'quartier' => null, 'id_ville' => null],
            'livraison' => ['id_quartier' => $bonapriso->id, 'quartier' => null, 'id_ville' => null],
            'montant_livraison' => 1, // ignoré : calculé côté serveur
        ]));

        $reponse->assertCreated()
            ->assertJsonPath('data.statut', 'attente')
            ->assertJsonPath('data.montant_livraison', 2000)
            ->assertJsonPath('data.montant_a_recuperer', 5000)
            ->assertJsonPath('data.client.nom', 'Ngo Marie')
            ->assertJsonPath('data.livraison.nom', 'Jean')
            ->assertJsonPath('data.transitions_possibles', ['annulee']);

        $this->assertDatabaseHas('commandes', [
            'id_client' => $user->id_client,
            'id_saver' => $user->id,
            'type_commande' => 'simple',
            'adresse_livraison' => '690000020*/*Bonapriso*/**/*Jean',
        ]);
    }

    public function test_quartier_libre_cree_le_quartier_et_tarif_par_defaut(): void
    {
        Sanctum::actingAs($this->client());

        $this->postJson('/api/v1/client/commandes', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.montant_livraison', 1000)
            ->assertJsonPath('data.collecte.quartier.libelle', 'Akwa');

        $this->assertDatabaseHas('quartier', ['libelle' => 'Bonapriso', 'id_ville' => $this->ville->id]);
    }

    public function test_validation(): void
    {
        Sanctum::actingAs($this->client());

        $this->postJson('/api/v1/client/commandes', $this->payload([
            'collecte' => ['contact' => '123'],
            'date_livraison' => now()->subDay()->toDateString(),
            'mode_de_paiement' => 'cash',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['collecte.contact', 'date_livraison', 'mode_de_paiement']);
    }

    public function test_liste_et_annulation(): void
    {
        $user = $this->client();
        $autre = $this->client();
        $commande = $this->commande(['id_client' => $user->id_client, 'statut' => 'attente']);
        $this->commande(['id_client' => $autre->id_client, 'statut' => 'attente']);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/client/commandes')->assertOk()->assertJsonCount(1, 'data');

        $this->postJson("/api/v1/client/commandes/{$commande->id}/annuler")
            ->assertOk()
            ->assertJsonPath('data.statut', 'annulee');

        $this->postJson("/api/v1/client/commandes/{$commande->id}/annuler")->assertStatus(422);
    }

    public function test_impossible_d_annuler_une_commande_attribuee(): void
    {
        $user = $this->client();
        $commande = $this->commande(['id_client' => $user->id_client, 'statut' => 'attribue']);

        Sanctum::actingAs($user);

        $this->postJson("/api/v1/client/commandes/{$commande->id}/annuler")->assertStatus(422);
    }
}
