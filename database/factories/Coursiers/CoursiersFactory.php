<?php

namespace Database\Factories\Coursiers;

use App\Models\Coursiers\Coursiers;
use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coursiers>
 */
class CoursiersFactory extends Factory
{
    protected $model = Coursiers::class;

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
