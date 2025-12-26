<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'codigo' => fake()->unique()->bothify('PROD###??'),
            'descricao' => fake()->words(3, true),
            'unidade' => fake()->randomElement(['UN', 'CX', 'KG', 'LT', 'PC']),
            'preco' => fake()->randomFloat(2, 10, 1000),
            'tributacao' => fake()->randomElement(['T', 'F', 'I']),
            'estoque' => fake()->numberBetween(0, 1000),
            'categoria' => fake()->randomElement(['Alimentos', 'Bebidas', 'Limpeza', 'Higiene', 'Diversos']),
        ];
    }


}
