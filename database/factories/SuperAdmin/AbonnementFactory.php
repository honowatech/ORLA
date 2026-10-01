<?php

namespace Database\Factories\SuperAdmin;

use App\Models\SuperAdmin\Abonnement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Abonnement>
 */
class AbonnementFactory extends Factory
{
    protected $model = Abonnement::class;

    public function definition(): array
    {
        return [
            'titre' => 'Forfait '.fake()->unique()->word(),
            'accumulateur' => 1,
            'type_periode' => 'mois',
            'montant' => 15000,
            'periode_grace' => 5,
            'statut' => 1,
        ];
    }

    public function inactif(): static
    {
        return $this->state(fn () => ['statut' => 0]);
    }
}
