@extends('layouts.app')

@section('title', 'Dashboard Loja')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard da Loja</h1>
            <p class="text-muted">Bem-vindo(a), {{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pedidos Pendentes</p>
                            <h3 class="mb-0">{{ $stats['pending_orders'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pedidos Aprovados</p>
                            <h3 class="mb-0">{{ $stats['approved_orders'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total do Mês</p>
                            <h3 class="mb-0">R$ {{ number_format($stats['month_total'], 2, ',', '.') }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-dollar text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Crédito Disponível</p>
                            <h3 class="mb-0">R$ {{ number_format($store->credit_limit - $store->credit_used, 2, ',', '.') }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-wallet2 text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        @php
                            $percentage = $store->credit_limit > 0 ? ($store->credit_used / $store->credit_limit) * 100 : 0;
                        @endphp
                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($percentage, 1) }}% utilizado</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="bi bi-plus-circle fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold mb-2">Novo Pedido</h5>
                    <p class="text-muted mb-3">Crie um novo pedido de produtos</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-cart-plus me-2"></i>Fazer Pedido
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="bi bi-box-seam fs-1 text-success mb-3"></i>
                    <h5 class="fw-bold mb-2">Catálogo de Produtos</h5>
                    <p class="text-muted mb-3">Navegue pelos produtos disponíveis</p>
                    <a href="{{ route('products.index') }}" class="btn btn-success">
                        <i class="bi bi-search me-2"></i>Ver Produtos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Pedidos Recentes</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver Todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Itens</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->items_count }} {{ $order->items_count == 1 ? 'item' : 'itens' }}</td>
                                    <td class="fw-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                    <td>
                                        @if($order->status === 'pendente')
                                            <span class="badge bg-warning text-dark">Pendente</span>
                                        @elseif($order->status === 'aprovado')
                                            <span class="badge bg-success">Aprovado</span>
                                        @elseif($order->status === 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Nenhum pedido encontrado
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
