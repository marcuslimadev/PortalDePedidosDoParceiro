<?php

use App\Models\Notification;
use App\Models\User;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('notification can be created with all required fields', function () {
    $user = User::factory()->create();
    
    $notification = Notification::factory()->create([
        'user_id' => $user->id,
        'type' => 'info',
        'title' => 'Test Notification',
        'message' => 'This is a test notification',
    ]);

    expect($notification)->toBeInstanceOf(Notification::class)
        ->and($notification->user_id)->toBe($user->id)
        ->and($notification->type)->toBe('info')
        ->and($notification->title)->toBe('Test Notification')
        ->and($notification->message)->toBe('This is a test notification');
});

test('notification belongs to a user', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);

    expect($notification->user)->toBeInstanceOf(User::class)
        ->and($notification->user->id)->toBe($user->id);
});

test('notification has default read as false', function () {
    $notification = Notification::factory()->create();

    expect($notification->read)->toBeFalse();
});

test('notification scope unread returns only unread notifications', function () {
    Notification::factory()->create(['read' => false]);
    Notification::factory()->create(['read' => false]);
    Notification::factory()->create(['read' => true]);

    $unread = Notification::unread()->get();

    expect($unread)->toHaveCount(2);
});

test('notification scope byType filters by type', function () {
    Notification::factory()->create(['type' => 'info']);
    Notification::factory()->create(['type' => 'info']);
    Notification::factory()->create(['type' => 'success']);
    Notification::factory()->create(['type' => 'error']);

    $infoNotifications = Notification::byType('info')->get();

    expect($infoNotifications)->toHaveCount(2);
});

test('notification can be marked as read', function () {
    $notification = Notification::factory()->create(['read' => false]);
    
    $notification->update(['read' => true]);

    expect($notification->fresh()->read)->toBeTrue();
});

test('notification supports different types', function () {
    $info = Notification::factory()->create(['type' => 'info']);
    $success = Notification::factory()->create(['type' => 'success']);
    $warning = Notification::factory()->create(['type' => 'warning']);
    $error = Notification::factory()->create(['type' => 'error']);

    expect($info->type)->toBe('info')
        ->and($success->type)->toBe('success')
        ->and($warning->type)->toBe('warning')
        ->and($error->type)->toBe('error');
});

test('notification read_at is cast to datetime', function () {
    $notification = Notification::factory()->create(['read_at' => now()]);

    expect($notification->read_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
