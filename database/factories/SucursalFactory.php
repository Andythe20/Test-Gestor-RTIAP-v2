<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sucursal>
 */
class SucursalFactory extends Factory
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
            'empresa_id' => \App\Models\Empresa::factory(),
            'nombre' => 'Sucursal ' . fake()->city(),
            'direccion' => fake()->address(),
            'created_at' => now(),
        ];
    }
}
