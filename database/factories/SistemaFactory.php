<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sistema>
 */
class SistemaFactory extends Factory
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
            'nombre' => 'Túnel ' . fake()->randomNumber(),
            'direccion' => fake()->address(),
            'sucursal_id' => \App\Models\Sucursal::factory(),
            'created_at' => now(),
        ];
    }
}
