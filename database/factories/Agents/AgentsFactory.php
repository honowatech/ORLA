<?php

namespace Database\Factories\Agents;

use App\Models\Agents\Agents;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agents>
 */
class AgentsFactory extends Factory
{
    protected $model = Agents::class;

    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'noms' => fake()->lastName(),
            'prenoms' => fake()->firstName(),
            'telephone' => '6'.fake()->unique()->numerify('########'),
            'statut' => 1,
        ];
    }
}
