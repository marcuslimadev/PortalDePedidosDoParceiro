<?php

use App\Services\AuditService;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('log creates audit log entry', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    AuditService::log(
        'order.created',
        'Order',
        1,
        'Order was created',
    );

    expect(AuditLog::count())->toBe(1)
        ->and(AuditLog::first()->user_id)->toBe($user->id)
        ->and(AuditLog::first()->action)->toBe('order.created')
        ->and(AuditLog::first()->resource_type)->toBe('Order')
        ->and(AuditLog::first()->resource_id)->toBe(1);
});

test('log stores ip address and user agent', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    AuditService::log(
        'test.action',
        'Test',
        1,
    );

    $log = AuditLog::first();

    expect($log->ip_address)->not->toBeNull()
        ->and($log->user_agent)->not->toBeNull();
});

test('log stores changes data as json', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $oldValues = ['status' => 'pending'];
    $newValues = ['status' => 'approved'];

    AuditService::log(
        'test.update',
        'Test',
        1,
        'Test updated',
        $oldValues,
        $newValues
    );

    $log = AuditLog::first();

    expect($log->old_values)->not->toBeNull()
        ->and($log->new_values)->not->toBeNull()
        ->and($log->old_values)->toBeArray()
        ->and($log->new_values)->toBeArray()
        ->and($log->old_values['status'])->toBe('pending')
        ->and($log->new_values['status'])->toBe('approved');
});

test('log accepts null user_id for system actions', function () {
    // Não está autenticado
    AuditService::log('system.action', 'System', 1, 'System action');

    expect(AuditLog::count())->toBe(1)
        ->and(AuditLog::first()->user_id)->toBeNull();
});

test('getByUser returns only user logs', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);
    AuditService::log('action1', 'Test', 1);
    AuditService::log('action2', 'Test', 2);
    
    $this->actingAs($user2);
    AuditService::log('action3', 'Test', 3);

    $user1Logs = AuditService::getByUser($user1->id);

    expect($user1Logs)->toHaveCount(2);
});

test('getByAction returns logs filtered by action', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    AuditService::log('order.created', 'Order', 1);
    AuditService::log('order.created', 'Order', 2);
    AuditService::log('order.updated', 'Order', 1);

    $createdLogs = AuditService::getByAction('order.created');

    expect($createdLogs)->toHaveCount(2);
});

