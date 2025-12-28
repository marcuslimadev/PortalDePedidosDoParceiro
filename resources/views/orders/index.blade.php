@extends('layouts.app')

@section('title', 'Pedidos')

@section('content')
<div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-box-seam me-2"></i>
                Portal de Pedidos
            </a>
            <div class="ms-auto d-flex gap-2">
                @if($can['create'])
                <a class="btn btn-light" href="{{ route('orders.create') }}">
                    <i class="bi bi-bag-plus me-1"></i> Novo Pedido
                </a>
                @endif
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar por ID</label>
                        <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            @foreach(['pendente','aprovado','cancelado'] as $s)
                                <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">A partir de</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Loja</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><span class="badge bg-secondary">#{{ $order->id }}</span></td>
                            <td>{{ $order->loja->name ?? 'N/A' }}</td>
                            <td class="fw-semibold">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td>
                                @if($order->status === 'pendente')
                                    <span class="badge bg-warning">Pendente</span>
                                @elseif($order->status === 'aprovado')
                                    <span class="badge bg-success">Aprovado</span>
                                @elseif($order->status === 'cancelado')
                                    <span class="badge bg-danger">Cancelado</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nenhum pedido encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection