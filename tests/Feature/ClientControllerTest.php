<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('client index displays all lojas for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    User::factory()->count(5)->create(['role' => 'loja']);
    User::factory()->count(2)->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/clients');

    $response->assertStatus(200);
});

test('client index requires admin or operador role', function () {
    $loja = User::factory()->create(['role' => 'loja']);

    $response = $this->actingAs($loja)->get('/clients');

    $response->assertForbidden();
});

test('client details shows credit information', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 3000,
    ]);

    $response = $this->actingAs($admin)->get("/clients/{$loja->id}");

    $response->assertStatus(200);
});

test('client credit can be updated by admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create(['role' => 'loja', 'credit_limit' => 5000]);

    $response = $this->actingAs($admin)->patch("/clients/{$loja->id}/credit", [
        'credit_limit' => 10000,
    ]);

    $response->assertRedirect();
    expect($loja->fresh()->credit_limit)->toBe(10000.0);
});

test('client credit update requires admin role', function () {
    $operador = User::factory()->create(['role' => 'operador']);
    $loja = User::factory()->create(['role' => 'loja', 'credit_limit' => 5000]);

    $response = $this->actingAs($operador)->patch("/clients/{$loja->id}/credit", [
        'credit_limit' => 10000,
    ]);

    $response->assertForbidden();
});

test('client credit update validates positive value', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create(['role' => 'loja']);

    $response = $this->actingAs($admin)->patch("/clients/{$loja->id}/credit", [
        'credit_limit' => -1000,
    ]);

    $response->assertSessionHasErrors('credit_limit');
});

test('client can be activated or deactivated', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create(['role' => 'loja', 'ativo' => true]);

    $response = $this->actingAs($admin)->patch("/clients/{$loja->id}/status", [
        'ativo' => false,
    ]);

    $response->assertRedirect();
    expect($loja->fresh()->ativo)->toBeFalse();
});

test('inactive clients cannot create orders', function () {
    $loja = User::factory()->create(['role' => 'loja', 'ativo' => false]);

    $response = $this->actingAs($loja)->get('/orders/create');

    $response->assertForbidden();
});

test('client search filters by name or cnpj', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    
    User::factory()->create(['role' => 'loja', 'name' => 'Loja ABC']);
    User::factory()->create(['role' => 'loja', 'name' => 'Loja XYZ']);
    User::factory()->create(['role' => 'loja', 'name' => 'Store 123']);

    $response = $this->actingAs($admin)->get('/clients?search=Loja');

    $response->assertStatus(200);
});
