<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
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
            'empleado_id' => \App\Models\Empleado::factory(),
            'producto_id' => \App\Models\Producto::factory(),
            'tarjeta_id' => \App\Models\Tarjeta::factory(),
            'total' => fake()->numberBetween(1000, 100000),
            'created_at' => now(),

        ];
    }
}
