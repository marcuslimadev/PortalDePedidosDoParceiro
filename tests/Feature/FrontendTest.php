<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->operador = User::factory()->create(['role' => 'operador']);
    $this->loja = User::factory()->create(['role' => 'loja']);
});

test('admin pode acessar dashboard', function () {
    $response = $this->actingAs($this->admin)->get('/dashboard');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Dashboard/Admin')
            ->has('stats')
            ->has('recentOrders')
            ->has('activeUsers')
    );
});

test('operador pode acessar dashboard', function () {
    $response = $this->actingAs($this->operador)->get('/dashboard');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Dashboard/Operador')
            ->has('stats')
            ->has('pendingOrders')
    );
});

test('loja pode acessar dashboard', function () {
    $response = $this->actingAs($this->loja)->get('/dashboard');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Dashboard/Loja')
            ->has('stats')
            ->has('recentOrders')
            ->has('store')
    );
});

test('usuário pode visualizar lista de produtos', function () {
    Product::factory()->count(5)->create();
    
    $response = $this->actingAs($this->loja)->get('/products');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Products/Index')
            ->has('products')
            ->has('categorias')
            ->has('can')
    );
});

test('loja pode acessar página de criar pedido', function () {
    Product::factory()->count(3)->create();
    
    $response = $this->actingAs($this->loja)->get('/orders/create');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Orders/Create')
            ->has('products')
            ->has('paymentTerms')
    );
});

test('admin não pode acessar página de criar pedido', function () {
    $response = $this->actingAs($this->admin)->get('/orders/create');
    
    $response->assertStatus(403);
});

test('usuário pode visualizar lista de pedidos', function () {
    Order::factory()->count(3)->create(['loja_id' => $this->loja->id]);
    
    $response = $this->actingAs($this->loja)->get('/orders');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Orders/Index')
            ->has('orders')
            ->has('can')
    );
});

test('usuário pode visualizar detalhes do pedido', function () {
    $order = Order::factory()->create(['loja_id' => $this->loja->id]);
    
    $response = $this->actingAs($this->loja)->get("/orders/{$order->id}");
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Orders/Show')
            ->has('order')
            ->has('can')
    );
});

test('operador pode aprovar pedido', function () {
    $order = Order::factory()->create(['status' => 'pendente']);
    
    $response = $this->actingAs($this->operador)
        ->post("/orders/{$order->id}/approve");
    
    $response->assertStatus(302);
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'aprovado',
    ]);
});

test('loja não pode aprovar pedido', function () {
    $order = Order::factory()->create(['status' => 'pendente']);
    
    $response = $this->actingAs($this->loja)
        ->post("/orders/{$order->id}/approve");
    
    $response->assertStatus(403);
});

test('operador pode cancelar pedido', function () {
    $order = Order::factory()->create(['status' => 'pendente']);
    
    $response = $this->actingAs($this->operador)
        ->post("/orders/{$order->id}/cancel", [
            'cancellation_reason' => 'Sem estoque',
        ]);
    
    $response->assertStatus(302);
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'cancelado',
        'cancellation_reason' => 'Sem estoque',
    ]);
});

test('usuário pode visualizar notificações', function () {
    $response = $this->actingAs($this->loja)->get('/notifications');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->component('Notifications/Index')
            ->has('notifications')
    );
});

test('produtos podem ser filtrados por busca', function () {
    // Limpa produtos anteriores e cria novos
    Product::query()->delete();
    
    Product::factory()->create(['descricao' => 'Produto A', 'codigo' => '001']);
    Product::factory()->create(['descricao' => 'Produto B', 'codigo' => '002']);
    
    $response = $this->actingAs($this->loja)
        ->get('/products?search=Produto A');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->has('products.data', 1)
    );
});

test('pedidos podem ser filtrados por status', function () {
    Order::factory()->create(['status' => 'pendente', 'loja_id' => $this->loja->id]);
    Order::factory()->create(['status' => 'aprovado', 'loja_id' => $this->loja->id]);
    
    $response = $this->actingAs($this->loja)
        ->get('/orders?status=pendente');
    
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => 
        $page->has('orders.data', 1)
    );
});
