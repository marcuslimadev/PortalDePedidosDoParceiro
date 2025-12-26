<?php

use App\Services\NotificationService;
use App\Models\User;
use App\Models\Notification;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('create notification creates notification for user', function () {
    $user = User::factory()->create();

    NotificationService::create(
        $user->id,
        'Test Title',
        'Test Message',
        'info'
    );

    expect(Notification::count())->toBe(1)
        ->and(Notification::first()->user_id)->toBe($user->id)
        ->and(Notification::first()->title)->toBe('Test Title')
        ->and(Notification::first()->message)->toBe('Test Message')
        ->and(Notification::first()->type)->toBe('info');
});

test('create notification supports different types', function () {
    $user = User::factory()->create();

    NotificationService::create($user->id, 'Info', 'Message', 'info');
    NotificationService::create($user->id, 'Success', 'Message', 'success');
    NotificationService::create($user->id, 'Warning', 'Message', 'warning');
    NotificationService::create($user->id, 'Error', 'Message', 'error');

    $notifications = Notification::all();

    expect($notifications)->toHaveCount(4)
        ->and($notifications->pluck('type')->toArray())->toContain('info', 'success', 'warning', 'error');
});

test('markAsRead marks notification as read', function () {
    $notification = Notification::factory()->create(['read' => false]);

    NotificationService::markAsRead($notification->id);

    expect($notification->fresh()->read)->toBeTrue();
});

test('markAllAsRead marks all user notifications as read', function () {
    $user = User::factory()->create();
    
    Notification::factory()->count(3)->create(['user_id' => $user->id, 'read' => false]);

    NotificationService::markAllAsRead($user->id);

    expect(Notification::where('user_id', $user->id)->where('read', false)->count())->toBe(0);
});

test('getUnreadCount returns correct count', function () {
    $user = User::factory()->create();
    
    Notification::factory()->count(5)->create(['user_id' => $user->id, 'read' => false]);
    Notification::factory()->count(2)->create(['user_id' => $user->id, 'read' => true]);

    $count = NotificationService::getUnreadCount($user->id);

    expect($count)->toBe(5);
});
