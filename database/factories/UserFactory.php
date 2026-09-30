<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'noms' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => '6'.fake()->unique()->numerify('########'),
            'password' => Hash::make('password'),
            'id_type_utilisateur' => 1,
            'statut' => 1,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Compte désactivé (statut 0).
     */
    public function desactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 0,
        ]);
    }
}
