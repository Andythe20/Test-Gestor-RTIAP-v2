<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
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
            // insertar nombres de productos comunes
            'nombre' => fake()->randomElement([
                'Laptop',
                'Smartphone',
                'Tablet',
                'Monitor',
                'Teclado',
                'Ratón',
                'Impresora',
                'Cámara',
                'Auriculares',
                'Altavoces',
                'Disco Duro',
                'Memoria USB',
                'Router',
                'Smartwatch',
                'Proyector'
            ]),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->numberBetween(1000, 100000),
            'created_at' => now(),
        ];
    }
}
