<?php

use App\Models\Product;
use App\Models\ProductPriceHistory;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('product can be created with all required fields', function () {
    $product = Product::factory()->create([
        'codigo' => 'PROD001',
        'descricao' => 'Produto Teste',
        'preco' => 99.99,
        'unidade' => 'UN',
    ]);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->codigo)->toBe('PROD001')
        ->and($product->descricao)->toBe('Produto Teste')
        ->and((float)$product->preco)->toBe(99.99)
        ->and($product->unidade)->toBe('UN');
});

test('product price is cast to decimal', function () {
    $product = Product::factory()->create(['preco' => 199.99]);

    expect($product->preco)->toBeString()
        ->and((float)$product->preco)->toBe(199.99);
});

test('product peso liquido is cast to decimal', function () {
    $product = Product::factory()->create(['peso_liquido' => 5.250]);

    expect($product->peso_liquido)->toBeString();
});

test('product peso bruto is cast to decimal', function () {
    $product = Product::factory()->create(['peso_bruto' => 6.500]);

    expect($product->peso_bruto)->toBeString();
});

// test('product can have price history', function () {
//     $product = Product::factory()->create();
    
//     ProductPriceHistory::factory()->count(3)->create(['product_id' => $product->id]);

//     expect($product->priceHistory)->toHaveCount(3);
// })->skip('ProductPriceHistory factory não implementada');

// test('product scope ativos returns only active products', function () {
//     Product::factory()->create(['ativo' => true]);
//     Product::factory()->create(['ativo' => true]);
//     Product::factory()->create(['ativo' => false]);

//     $ativos = Product::ativos()->get();

//     expect($ativos)->toHaveCount(2);
// })->skip('Campo ativo não existe na tabela products');

test('product scope byCategoria filters by category', function () {
    Product::factory()->create(['categoria' => 'Alimentos']);
    Product::factory()->create(['categoria' => 'Alimentos']);
    Product::factory()->create(['categoria' => 'Bebidas']);

    $alimentos = Product::byCategoria('Alimentos')->get();

    expect($alimentos)->toHaveCount(2);
});

test('product has codigo winthor field', function () {
    $product = Product::factory()->create(['codprod_winthor' => 'WINT12345']);

    expect($product->codprod_winthor)->toBe('WINT12345');
});

test('product has embalagem field', function () {
    $product = Product::factory()->create(['embalagem' => 'Caixa 12un']);

    expect($product->embalagem)->toBe('Caixa 12un');
});

test('product has ncm field', function () {
    $product = Product::factory()->create(['ncm' => '12345678']);

    expect($product->ncm)->toBe('12345678');
});
