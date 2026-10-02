<?php

namespace Database\Factories\Clients;

use App\Models\Clients\Clients;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clients>
 */
class ClientsFactory extends Factory
{
    protected $model = Clients::class;

    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'noms' => fake()->lastName(),
            'Prenoms' => fake()->firstName(),
            'type_client' => 2,
            'telephone' => '6'.fake()->unique()->numerify('########'),
            'statut' => 1,
        ];
    }

    public function entreprise(): static
    {
        return $this->state(fn () => ['type_client' => 1]);
    }
}
