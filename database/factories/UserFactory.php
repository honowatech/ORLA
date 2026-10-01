<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'noms' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => '6'.fake()->unique()->numerify('########'),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'id_type_utilisateur' => 1,
            'statut' => 1,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['id_type_utilisateur' => 1]);
    }

    public function agent(): static
    {
        return $this->state(fn () => ['id_type_utilisateur' => 2]);
    }

    public function coursier(): static
    {
        return $this->state(fn () => ['id_type_utilisateur' => 3]);
    }

    public function client(): static
    {
        return $this->state(fn () => ['id_type_utilisateur' => 4]);
    }

    public function desactive(): static
    {
        return $this->state(fn () => ['statut' => 0]);
    }
}
