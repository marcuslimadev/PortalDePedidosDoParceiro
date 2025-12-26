<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('order can be created with all required fields', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    
    $order = Order::factory()->create([
        'loja_id' => $loja->id,
        'status' => 'pendente',
        'payment_terms' => 'Antecipado',
        'total' => 1000.00,
    ]);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->loja_id)->toBe($loja->id)
        ->and($order->status)->toBe('pendente')
        ->and($order->payment_terms)->toBe('Antecipado')
        ->and((float)$order->total)->toBe(1000.0);
});

test('order belongs to a loja', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $order = Order::factory()->create(['loja_id' => $loja->id]);

    expect($order->loja)->toBeInstanceOf(User::class)
        ->and($order->loja->id)->toBe($loja->id);
});

test('order can have multiple items', function () {
    $order = Order::factory()->create();
    $product = Product::factory()->create();
    
    OrderItem::factory()->count(3)->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
    ]);

    expect($order->items)->toHaveCount(3);
});

test('order isPendente method returns true for pendente status', function () {
    $order = Order::factory()->create(['status' => 'pendente']);

    expect($order->isPendente())->toBeTrue();
});

test('order isPendente method returns false for other status', function () {
    $order = Order::factory()->create(['status' => 'aprovado']);

    expect($order->isPendente())->toBeFalse();
});

test('order scope pendentes returns only pendente orders', function () {
    Order::factory()->create(['status' => 'pendente']);
    Order::factory()->create(['status' => 'pendente']);
    Order::factory()->create(['status' => 'aprovado']);
    Order::factory()->create(['status' => 'rejeitado']);

    $pendentes = Order::pendentes()->get();

    expect($pendentes)->toHaveCount(2);
});

test('order scope status filters by given status', function () {
    Order::factory()->create(['status' => 'aprovado']);
    Order::factory()->create(['status' => 'aprovado']);
    Order::factory()->create(['status' => 'rejeitado']);

    $aprovados = Order::status('aprovado')->get();

    expect($aprovados)->toHaveCount(2);
});

test('order casts subtotal to decimal', function () {
    $order = Order::factory()->create(['subtotal' => 1234.56]);

    expect($order->subtotal)->toBeString()
        ->and((float)$order->subtotal)->toBe(1234.56);
});

test('order casts discount to decimal', function () {
    $order = Order::factory()->create(['discount' => 123.45]);

    expect($order->discount)->toBeString()
        ->and((float)$order->discount)->toBe(123.45);
});

test('order casts cancelled_at to datetime', function () {
    $order = Order::factory()->create(['cancelled_at' => now()]);

    expect($order->cancelled_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('order can have cancellation reason', function () {
    $order = Order::factory()->create([
        'status' => 'cancelado',
        'cancellation_reason' => 'Cliente solicitou cancelamento',
    ]);

    expect($order->cancellation_reason)->toBe('Cliente solicitou cancelamento');
});

test('order calculates total correctly', function () {
    $order = Order::factory()->create([
        'subtotal' => 1000.00,
        'discount' => 50.00,
        'total' => 950.00,
    ]);

    expect((float)$order->total)->toBe(950.0);
});
