<?php

namespace Database\Factories\Ville;

use App\Models\Ville\Ville;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ville>
 */
class VilleFactory extends Factory
{
    protected $model = Ville::class;

    public function definition(): array
    {
        return [
            'libelle' => fake()->unique()->city(),
            'code' => strtoupper(fake()->unique()->lexify('???')),
        ];
    }
}
