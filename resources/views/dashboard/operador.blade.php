@extends('layouts.app')

@section('title', 'Dashboard Operador')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Dashboard do Operador</h1>
            <p class="text-muted">Gerencie pedidos e aprovações</p>
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
                            <p class="text-muted mb-1 small">Aprovados Hoje</p>
                            <h3 class="mb-0">{{ $stats['approved_today'] }}</h3>
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
                            <p class="text-muted mb-1 small">Cancelados Hoje</p>
                            <h3 class="mb-0">{{ $stats['cancelled_today'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
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
                            <p class="text-muted mb-1 small">Valor Pendente</p>
                            <h3 class="mb-0">R$ {{ number_format($stats['pending_value'], 2, ',', '.') }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-dollar text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pedidos Pendentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Pedidos Pendentes de Aprovação
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Loja</th>
                                    <th>CNPJ</th>
                                    <th>Crédito Disponível</th>
                                    <th>Total</th>
                                    <th>Data</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingOrders as $order)
                                <tr>
                                    <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
                                    <td>{{ $order->loja->name ?? 'N/A' }}</td>
                                    <td><small class="text-muted">{{ $order->loja->cnpj ?? 'N/A' }}</small></td>
                                    <td>
                                        @php
                                            $available = ($order->loja->credit_limit ?? 0) - ($order->loja->credit_used ?? 0);
                                            $hasCredit = $available >= $order->total;
                                        @endphp
                                        <span class="badge {{ $hasCredit ? 'bg-success' : 'bg-danger' }}">
                                            R$ {{ number_format($available, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                            <br>
                                            <span class="text-primary">{{ $order->created_at->diffForHumans() }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary" title="Ver Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('orders.approve', $order) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Aprovar"
                                                        onclick="return confirm('Confirma a aprovação deste pedido?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('orders.cancel', $order) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger" title="Cancelar"
                                                        onclick="return confirm('Confirma o cancelamento deste pedido?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-3 text-success"></i>
                                        <h5>Nenhum pedido pendente</h5>
                                        <p>Todos os pedidos foram processados</p>
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
