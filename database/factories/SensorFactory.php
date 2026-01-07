<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorFactory extends Factory
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
            'codigo_serie' => fake()->unique()->bothify('SN-########'),
            'sistema_id' => \App\Models\Sistema::factory(),
            'ubicacion' => fake()->randomElement(['Fondo derecha', 'Fondo izquierda', 'Frente central', 'Lateral derecha', 'Lateral izquierda']),
            'valor_actual' => fake()->randomFloat(2, -20, 50),
            'created_at' => now(),
        ];
    }
}
