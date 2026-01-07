<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tarjeta>
 */
class TarjetaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->randomNumber(),
            'numero_tarjeta' => fake()->unique()->creditCardNumber(),
            'nombre_titular' => fake()->name(),
            'fecha_expiracion' => fake()->dateTimeBetween('now', '+5 years')->format('Y-m-d'),
            'created_at' => now(),
        ];
    }
}
