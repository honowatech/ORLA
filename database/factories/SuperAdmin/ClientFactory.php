<?php

namespace Database\Factories\SuperAdmin;

use App\Models\SuperAdmin\Abonnement;
use App\Models\SuperAdmin\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Licence d'instance (table super_admin_client).
 *
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'adresse' => fake()->city(),
            'telephone' => '6'.fake()->unique()->numerify('########'),
            'cni' => fake()->numerify('##########'),
            'id_abonnement' => Abonnement::factory(),
            'date_fin' => now()->addMonth(),
            'date_dernier_paiement' => now(),
            'statut' => 1,
        ];
    }

    public function expiree(): static
    {
        return $this->state(fn () => ['date_fin' => now()->subMonth()]);
    }

    public function sansAbonnement(): static
    {
        return $this->state(fn () => ['id_abonnement' => null, 'date_fin' => null]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['statut' => 0]);
    }
}
