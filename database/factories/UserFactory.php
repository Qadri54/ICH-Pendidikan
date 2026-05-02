<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'email'    => fake()->unique()->safeEmail(),
            'no_hp'    => '08' . fake()->numerify('#########'),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    /** User dengan role Admin */
    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->role()->create(['role_name' => 'Admin']);
        });
    }

    /** User dengan role Guru */
    public function guru(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->role()->create(['role_name' => 'Guru']);
        });
    }

    /** User dengan role Orang Tua */
    public function orangTua(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->role()->create(['role_name' => 'Orang Tua']);
        });
    }
}
