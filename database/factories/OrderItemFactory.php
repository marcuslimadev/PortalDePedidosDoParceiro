<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantidade = fake()->numberBetween(1, 100);
        $preco_unitario = fake()->randomFloat(2, 5, 500);
        
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantidade' => $quantidade,
            'preco_unitario' => $preco_unitario,
            'subtotal' => $quantidade * $preco_unitario,
        ];
    }
}
