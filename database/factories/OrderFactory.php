<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'loja_id' => User::factory(),
            'status' => 'pendente',
            'payment_terms' => fake()->randomElement(['Antecipado', '7 dias', '14 dias', '30 dias']),
            'observations' => fake()->optional()->sentence(),
            'subtotal' => $subtotal = fake()->randomFloat(2, 100, 10000),
            'discount_percentage' => $discount_pct = fake()->randomFloat(2, 0, 10),
            'discount' => $subtotal * ($discount_pct / 100),
            'total' => $subtotal - ($subtotal * ($discount_pct / 100)),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aprovado',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelado',
            'cancellation_reason' => fake()->sentence(),
            'cancelled_at' => now(),
        ]);
    }
}
