<?php

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('product index displays products for authenticated users', function () {
    $user = User::factory()->create();
    Product::factory()->count(5)->create();

    $response = $this->actingAs($user)->get('/products');

    $response->assertStatus(200);
});

test('product can be created by admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/products', [
        'codigo' => 'PROD001',
        'descricao' => 'Produto Teste',
        'preco' => 99.99,
        'unidade' => 'UN',
        'estoque' => 100,
        'categoria' => 'Alimentos',
    ]);

    $response->assertRedirect();
    expect(Product::count())->toBe(1)
        ->and(Product::first()->codigo)->toBe('PROD001');
});

test('product creation requires admin role', function () {
    $loja = User::factory()->create(['role' => 'loja']);

    $response = $this->actingAs($loja)->post('/products', [
        'codigo' => 'PROD001',
        'descricao' => 'Produto Teste',
        'preco' => 99.99,
        'unidade' => 'UN',
    ]);

    $response->assertForbidden();
});

test('product creation validates required fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->post('/products', [
        'codigo' => '',
        'descricao' => '',
    ]);

    $response->assertSessionHasErrors(['codigo', 'descricao', 'preco', 'unidade']);
});

test('product can be updated by admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create(['preco' => 100]);

    $response = $this->actingAs($admin)->put("/products/{$product->id}", [
        'codigo' => $product->codigo,
        'descricao' => $product->descricao,
        'preco' => 150,
        'unidade' => $product->unidade,
        'estoque' => $product->estoque,
        'categoria' => $product->categoria,
    ]);

    $response->assertRedirect();
    expect($product->fresh()->preco)->toBe(150.0);
});

test('product update requires admin role', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $product = Product::factory()->create();

    $response = $this->actingAs($loja)->put("/products/{$product->id}", [
        'preco' => 200,
    ]);

    $response->assertForbidden();
});

test('product can be deleted by admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->delete("/products/{$product->id}");

    $response->assertRedirect();
    expect(Product::count())->toBe(0);
});

test('product deletion requires admin role', function () {
    $loja = User::factory()->create(['role' => 'loja']);
    $product = Product::factory()->create();

    $response = $this->actingAs($loja)->delete("/products/{$product->id}");

    $response->assertForbidden();
});

test('product search filters by codigo', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Product::factory()->create(['codigo' => 'PROD001', 'descricao' => 'Test']);
    Product::factory()->create(['codigo' => 'PROD002', 'descricao' => 'Test']);
    Product::factory()->create(['codigo' => 'ITEM001', 'descricao' => 'Test']);

    $response = $this->actingAs($admin)->get('/products?search=PROD');

    $response->assertStatus(200);
});

test('product can be filtered by categoria', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    Product::factory()->count(2)->create(['categoria' => 'Alimentos']);
    Product::factory()->count(3)->create(['categoria' => 'Bebidas']);

    $response = $this->actingAs($admin)->get('/products?categoria=Alimentos');

    $response->assertStatus(200);
});
