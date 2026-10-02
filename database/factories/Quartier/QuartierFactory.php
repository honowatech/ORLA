<?php

namespace Database\Factories\Quartier;

use App\Models\Entreprise;
use App\Models\Quartier\Quartier;
use App\Models\Ville\Ville;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quartier>
 */
class QuartierFactory extends Factory
{
    protected $model = Quartier::class;

    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'libelle' => 'Quartier '.fake()->unique()->word(),
            'id_ville' => Ville::factory(),
            'id_zone' => null,
        ];
    }
}
