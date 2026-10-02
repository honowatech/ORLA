<?php

namespace Database\Factories\Zone;

use App\Models\Entreprise;
use App\Models\Ville\Ville;
use App\Models\Zone\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Zone>
 */
class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition(): array
    {
        return [
            'entreprise_id' => Entreprise::factory(),
            'libelle' => 'Zone '.fake()->unique()->word(),
            'id_ville' => Ville::factory(),
            'statut' => 1,
        ];
    }
}
