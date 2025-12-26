<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Notification;
use App\Models\ClientCreditHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can be created with all required fields', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => 'loja',
    ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->role)->toBe('loja');
});

test('user has default ativo as true', function () {
    $user = User::factory()->create();
    
    expect($user->ativo)->toBeTrue();
});

test('user role can be admin, operador or loja', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $operador = User::factory()->create(['role' => 'operador']);
    $loja = User::factory()->create(['role' => 'loja']);

    expect($admin->role)->toBe('admin')
        ->and($operador->role)->toBe('operador')
        ->and($loja->role)->toBe('loja');
});

test('user isAdmin method works correctly', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create(['role' => 'loja']);

    expect($admin->isAdmin())->toBeTrue()
        ->and($loja->isAdmin())->toBeFalse();
});

test('user isLoja method works correctly', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $loja = User::factory()->create(['role' => 'loja']);

    expect($loja->isLoja())->toBeTrue()
        ->and($admin->isLoja())->toBeFalse();
});

test('user credito disponivel is calculated correctly', function () {
    $user = User::factory()->create([
        'credit_limit' => 10000,
        'credit_used' => 3000,
    ]);

    expect($user->credito_disponivel)->toBe(7000.0);
});

test('user password is hidden from array', function () {
    $user = User::factory()->create(['password' => bcrypt('secret')]);
    
    $array = $user->toArray();

    expect($array)->not->toHaveKey('password');
});

test('user remember token is hidden from array', function () {
    $user = User::factory()->create(['remember_token' => 'token123']);
    
    $array = $user->toArray();

    expect($array)->not->toHaveKey('remember_token');
});
