<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('home');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard baseado em role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produtos
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products-import', [ProductImportController::class, 'create'])->name('products.import');
    Route::post('/products-import', [ProductImportController::class, 'store'])->name('products.import.store');

    // Pedidos
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])
        ->middleware(CheckRole::class . ':loja')
        ->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware(CheckRole::class . ':loja')
        ->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])
        ->middleware(CheckRole::class . ':admin,operador')
        ->name('orders.approve');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
        ->middleware(CheckRole::class . ':admin,operador,loja')
        ->name('orders.cancel');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->middleware(CheckRole::class . ':admin,operador')
        ->name('orders.updateStatus');

    // Notificações
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

