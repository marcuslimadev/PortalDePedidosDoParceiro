<?php

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

test('notification index displays user notifications', function () {
    $user = User::factory()->create();
    
    Notification::factory()->count(3)->create(['user_id' => $user->id]);
    Notification::factory()->count(2)->create(); // Other user notifications

    $response = $this->actingAs($user)->get('/notifications');

    $response->assertStatus(200);
});

test('notification can be marked as read', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id, 'read' => false]);

    $response = $this->actingAs($user)->patch("/notifications/{$notification->id}/read");

    $response->assertRedirect();
    expect($notification->fresh()->read)->toBeTrue();
});

test('user cannot mark other user notification as read', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $notification = Notification::factory()->create(['user_id' => $user2->id]);

    $response = $this->actingAs($user1)->patch("/notifications/{$notification->id}/read");

    $response->assertForbidden();
});

test('all notifications can be marked as read', function () {
    $user = User::factory()->create();
    
    Notification::factory()->count(5)->create(['user_id' => $user->id, 'read' => false]);

    $response = $this->actingAs($user)->post('/notifications/mark-all-read');

    $response->assertRedirect();
    expect(Notification::where('user_id', $user->id)->where('read', false)->count())->toBe(0);
});

test('notification unread count is correct', function () {
    $user = User::factory()->create();
    
    Notification::factory()->count(3)->create(['user_id' => $user->id, 'read' => false]);
    Notification::factory()->count(2)->create(['user_id' => $user->id, 'read' => true]);

    $response = $this->actingAs($user)->get('/notifications/unread-count');

    $response->assertStatus(200)
        ->assertJson(['count' => 3]);
});

test('notifications are filtered by type', function () {
    $user = User::factory()->create();
    
    Notification::factory()->count(2)->create(['user_id' => $user->id, 'type' => 'info']);
    Notification::factory()->count(3)->create(['user_id' => $user->id, 'type' => 'error']);

    $response = $this->actingAs($user)->get('/notifications?type=error');

    $response->assertStatus(200);
});

test('notification can be deleted by owner', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/notifications/{$notification->id}");

    $response->assertRedirect();
    expect(Notification::count())->toBe(0);
});

test('user cannot delete other user notification', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $notification = Notification::factory()->create(['user_id' => $user2->id]);

    $response = $this->actingAs($user1)->delete("/notifications/{$notification->id}");

    $response->assertForbidden();
});
