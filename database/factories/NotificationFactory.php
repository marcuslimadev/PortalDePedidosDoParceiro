<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['order_created', 'order_approved', 'order_rejected', 'credit_limit_changed', 'general', 'info']),
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'read' => false,
            'read_at' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read' => true,
            'read_at' => now(),
        ]);
    }
}
