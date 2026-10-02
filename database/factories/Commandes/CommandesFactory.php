<?php

namespace Database\Factories\Commandes;

use App\Models\Commandes\Commandes;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commandes>
 */
class CommandesFactory extends Factory
{
    protected $model = Commandes::class;

    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            // L'enregistreur appartient à la même entreprise que la commande.
            'id_saver' => fn (array $attributs) => User::factory()->create(['entreprise_id' => $attributs['entreprise_id']])->id,
            'nom_client' => fake()->name(),
            'telephone' => '6'.fake()->numerify('########'),
            'date_commande' => now(),
            'date_livraison' => now()->addDay(),
            'type_commande' => 'simple',
            // Format attendu par les vues : « quartier*/*adresse ».
            'adresse_colis' => fake()->streetName().'*/*'.fake()->streetAddress(),
            'adresse_livraison' => fake()->streetName().'*/*'.fake()->streetAddress(),
            'montant_livraison' => 1000,
            'mode_de_paiement' => 'coursier',
            'statut' => 'attente',
            'disponibility' => 1,
        ];
    }
}
