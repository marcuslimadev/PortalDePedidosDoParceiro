<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('order index displays orders for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create(['role' => 'loja']);
    
    Order::factory()->count(3)->create(['loja_id' => $loja->id]);

    $response = $this->actingAs($admin)->get('/orders');

    $response->assertStatus(200);
});

test('order index displays only own orders for loja', function () {
    $loja1 = User::factory()->create(['role' => 'loja']);
    $loja2 = User::factory()->create(['role' => 'loja']);
    
    Order::factory()->count(2)->create(['loja_id' => $loja1->id]);
    Order::factory()->count(3)->create(['loja_id' => $loja2->id]);

    $response = $this->actingAs($loja1)->get('/orders');

    $response->assertStatus(200);
});

test('order can be created with valid data', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 0,
    ]);
    
    $product = Product::factory()->create(['preco' => 100]);

    $response = $this->actingAs($loja)->post('/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantidade' => 5],
        ],
        'payment_terms' => 'Antecipado',
        'observations' => 'Test order',
    ]);

    $response->assertRedirect();
    expect(Order::count())->toBe(1);
});

test('order creation validates required items', function () {
    $loja = User::factory()->create(['role' => 'loja']);

    $response = $this->actingAs($loja)->post('/orders', [
        'items' => [],
        'payment_terms' => 'Antecipado',
    ]);

    $response->assertSessionHasErrors('items');
});

test('order creation validates product exists', function () {
    $loja = User::factory()->create(['role' => 'loja']);

    $response = $this->actingAs($loja)->post('/orders', [
        'items' => [
            ['product_id' => 99999, 'quantidade' => 1],
        ],
        'payment_terms' => 'Antecipado',
    ]);

    $response->assertSessionHasErrors('items.0.product_id');
});

test('order creation validates quantidade is positive', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $product = Product::factory()->create();

    $response = $this->actingAs($loja)->post('/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantidade' => 0],
        ],
        'payment_terms' => 'Antecipado',
    ]);

    $response->assertSessionHasErrors('items.0.quantidade');
});

test('order creation fails when insufficient credit', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 100,
        'credit_used' => 90,
    ]);
    
    $product = Product::factory()->create(['preco' => 50]);

    $response = $this->actingAs($loja)->post('/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantidade' => 1],
        ],
        'payment_terms' => '30 dias',
    ]);

    $response->assertSessionHasErrors();
    expect(Order::count())->toBe(0);
});

test('order reserves credit on creation', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 0,
    ]);
    
    $product = Product::factory()->create(['preco' => 100]);

    $this->actingAs($loja)->post('/orders', [
        'items' => [
            ['product_id' => $product->id, 'quantidade' => 5],
        ],
        'payment_terms' => 'Antecipado',
    ]);

    expect($loja->fresh()->credit_used)->toBeGreaterThan(0);
});

test('order status can be updated by admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = Order::factory()->create(['status' => 'pendente']);

    $response = $this->actingAs($admin)->patch("/orders/{$order->id}/status", [
        'status' => 'aprovado',
    ]);

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('aprovado');
});

test('order status cannot be updated by loja', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $order = Order::factory()->create(['status' => 'pendente', 'loja_id' => $loja->id]);

    $response = $this->actingAs($loja)->patch("/orders/{$order->id}/status", [
        'status' => 'aprovado',
    ]);

    $response->assertForbidden();
});

test('order can be cancelled with reason', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = Order::factory()->create(['status' => 'pendente']);

    $response = $this->actingAs($admin)->patch("/orders/{$order->id}/cancel", [
        'cancellation_reason' => 'Cliente solicitou',
    ]);

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('cancelado')
        ->and($order->fresh()->cancellation_reason)->toBe('Cliente solicitou');
});

test('cancelled order releases reserved credit', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 500,
    ]);
    
    $admin = User::factory()->create(['role' => 'admin']);
    $order = Order::factory()->create([
        'loja_id' => $loja->id,
        'status' => 'pendente',
        'total' => 500,
    ]);

    $this->actingAs($admin)->patch("/orders/{$order->id}/cancel", [
        'cancellation_reason' => 'Teste',
    ]);

    expect($loja->fresh()->credit_used)->toBeLessThan(500);
});
