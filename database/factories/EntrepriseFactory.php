<?php

namespace Database\Factories;

use App\Models\Entreprise;
use App\Models\SuperAdmin\Abonnement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entreprise>
 */
class EntrepriseFactory extends Factory
{
    protected $model = Entreprise::class;

    /**
     * Par défaut : entreprise active, en période d'essai, sans abonnement payé.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'slug' => null, // généré par le modèle
            'email' => fake()->unique()->companyEmail(),
            'telephone' => '6'.fake()->unique()->numerify('########'),
            'adresse' => fake()->city(),
            'pays' => 'CM',
            'devise' => 'XAF',
            'prefixe_telephone' => '+237',
            'tarif_defaut' => 1000,
            'statut' => true,
            'essai_fin' => now()->addDays(14),
            'id_abonnement' => null,
            'date_fin' => null,
        ];
    }

    public function abonnee(): static
    {
        return $this->state(fn () => [
            'id_abonnement' => Abonnement::factory(),
            'date_fin' => now()->addMonth(),
            'date_dernier_paiement' => now(),
            'essai_fin' => null,
        ]);
    }

    public function enGrace(): static
    {
        return $this->state(fn () => [
            'id_abonnement' => Abonnement::factory()->state(['periode_grace' => 5]),
            'date_fin' => now()->subDays(2),
            'date_dernier_paiement' => now()->subMonth(),
            'essai_fin' => null,
        ]);
    }

    public function expiree(): static
    {
        return $this->state(fn () => [
            'id_abonnement' => Abonnement::factory()->state(['periode_grace' => 5]),
            'date_fin' => now()->subMonth(),
            'date_dernier_paiement' => now()->subMonths(2),
            'essai_fin' => null,
        ]);
    }

    public function essaiExpire(): static
    {
        return $this->state(fn () => [
            'essai_fin' => now()->subDay(),
            'id_abonnement' => null,
            'date_fin' => null,
        ]);
    }

    public function suspendue(): static
    {
        return $this->state(fn () => ['statut' => false]);
    }
}
