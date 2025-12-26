<?php

use App\Services\PaymentService;

test('calculateDiscount returns correct values for Antecipado', function () {
    $result = PaymentService::calculateDiscount('Antecipado', 1000);

    expect($result)->toHaveKey('subtotal')
        ->and($result)->toHaveKey('discount')
        ->and($result)->toHaveKey('discount_percentage')
        ->and($result)->toHaveKey('total')
        ->and($result['subtotal'])->toBe(1000.0)
        ->and($result['discount'])->toBe(50.0)
        ->and($result['discount_percentage'])->toBe(5.0)
        ->and($result['total'])->toBe(950.0);
});

test('calculateDiscount returns correct values for 30 dias', function () {
    $result = PaymentService::calculateDiscount('30 dias', 1000);

    expect($result['subtotal'])->toBe(1000.0)
        ->and($result['discount'])->toBe(20.0)
        ->and($result['discount_percentage'])->toBe(2.0)
        ->and($result['total'])->toBe(980.0);
});

test('calculateDiscount returns zero discount for other payment terms', function () {
    $result = PaymentService::calculateDiscount('60 dias', 1000);

    expect($result['subtotal'])->toBe(1000.0)
        ->and($result['discount'])->toBe(0.0)
        ->and($result['discount_percentage'])->toBe(0.0)
        ->and($result['total'])->toBe(1000.0);
});

test('calculateDiscount is case insensitive', function () {
    $result1 = PaymentService::calculateDiscount('ANTECIPADO', 1000);
    $result2 = PaymentService::calculateDiscount('antecipado', 1000);

    expect($result1['discount_percentage'])->toBe(5.0)
        ->and($result2['discount_percentage'])->toBe(5.0);
});

test('calculateDiscount rounds values to 2 decimals', function () {
    $result = PaymentService::calculateDiscount('Antecipado', 1234.567);

    expect($result['subtotal'])->toBe(1234.57)
        ->and($result['discount'])->toBe(61.73)
        ->and($result['total'])->toBe(1172.84);
});

test('calculateDiscount handles small amounts correctly', function () {
    $result = PaymentService::calculateDiscount('Antecipado', 10);

    expect($result['subtotal'])->toBe(10.0)
        ->and($result['discount'])->toBe(0.5)
        ->and($result['total'])->toBe(9.5);
});

test('calculateDiscount handles large amounts correctly', function () {
    $result = PaymentService::calculateDiscount('30 dias', 1000000);

    expect($result['subtotal'])->toBe(1000000.0)
        ->and($result['discount'])->toBe(20000.0)
        ->and($result['total'])->toBe(980000.0);
});
