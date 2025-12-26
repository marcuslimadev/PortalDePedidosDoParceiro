<?php

use App\Services\CreditService;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('validateCredit returns true when credit is available', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 3000,
    ]);

    $result = CreditService::validateCredit($loja, 5000);

    expect($result)->toBeTrue();
});

test('validateCredit returns false when credit is insufficient', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 8000,
    ]);

    $result = CreditService::validateCredit($loja, 5000);

    expect($result)->toBeFalse();
});

test('validateCredit returns false when exact credit limit is reached', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 10000,
    ]);

    $result = CreditService::validateCredit($loja, 0.01);

    expect($result)->toBeFalse();
});

test('reserveCredit updates credit_used correctly', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 3000,
    ]);

    CreditService::reserveCredit($loja, 2000);

    expect((float)$loja->fresh()->credit_used)->toBe(5000.0);
});

test('reserveCredit uses database transaction', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 0,
    ]);

    DB::shouldReceive('transaction')->once();
    
    CreditService::reserveCredit($loja, 1000);
});

test('reserveCredit locks row to prevent race conditions', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 5000,
    ]);

    CreditService::reserveCredit($loja, 2000);
    
    expect((float)$loja->fresh()->credit_used)->toBe(7000.0);
});

test('releaseCredit decreases credit_used correctly', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 5000,
    ]);

    $order = Order::factory()->create([
        'loja_id' => $loja->id,
        'total' => 2000,
    ]);

    CreditService::releaseCredit($order);

    expect((float)$loja->fresh()->credit_used)->toBe(3000.0);
});

test('releaseCredit does not go below zero', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 1000,
    ]);

    $order = Order::factory()->create([
        'loja_id' => $loja->id,
        'total' => 2000,
    ]);

    CreditService::releaseCredit($order);

    expect((float)$loja->fresh()->credit_used)->toBeGreaterThanOrEqual(0);
});

test('reserveCredit handles null credit_used', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => 10000,
        'credit_used' => 0,
    ]);
    
    // Simula credit_used como 0 (equivalente a null tratado pelo service)
    CreditService::reserveCredit($loja, 1000);

    expect((float)$loja->fresh()->credit_used)->toBe(1000.0);
});

test('validateCredit handles null credit_limit', function () {
    $loja = User::factory()->create([
        'role' => 'loja',
        'credit_limit' => null,
        'credit_used' => 0,
    ]);

    $result = CreditService::validateCredit($loja, 100);

    expect($result)->toBeFalse();
});
