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
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete'])
        ->middleware(CheckRole::class . ':admin')
        ->name('products.bulkDelete');
    Route::get('/products-import', [ProductImportController::class, 'create'])->name('products.import');
    Route::post('/products-import', [ProductImportController::class, 'store'])->name('products.import.store');
    Route::get('/products-import/download-modelo-excel', function() {
        return response()->download(storage_path('app/public/modelo_importacao_produtos.xlsx'));
    })->name('products.import.downloadExcel');
    Route::get('/products-import/download-modelo-csv', function() {
        return response()->download(storage_path('app/public/modelo_importacao_produtos.csv'));
    })->name('products.import.downloadCsv');

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

    // Usuários (Admin only)
    Route::middleware(CheckRole::class . ':admin')->group(function () {
        Route::get('/users', [App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [App\Http\Controllers\UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    // Clientes (Admin only) - CRUD completo
    Route::middleware(CheckRole::class . ':admin')->group(function () {
        Route::get('/clients', [App\Http\Controllers\ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [App\Http\Controllers\ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [App\Http\Controllers\ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [App\Http\Controllers\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [App\Http\Controllers\ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [App\Http\Controllers\ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Relatórios (Admin/Operador)
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])
        ->middleware(CheckRole::class . ':admin,operador')
        ->name('reports.index');

    // Configurações (Admin only)
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])
        ->middleware(CheckRole::class . ':admin')
        ->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])
        ->middleware(CheckRole::class . ':admin')
        ->name('settings.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

