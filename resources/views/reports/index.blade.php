@extends('layouts.app')

@section('title', 'Relatórios')

@section('content')
<div class="mb-4">
    <div class="mb-3">
        <h4 class="mb-1 fw-bold">Relatórios Gerenciais</h4>
        <p class="text-muted small mb-0">Análise de dados e estatísticas do sistema</p>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Total de Pedidos</h6>
                            <h3 class="mb-0">{{ $stats['total_pedidos'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-cart3 fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Produtos Cadastrados</h6>
                            <h3 class="mb-0">{{ $stats['total_produtos'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-box fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Valor Total</h6>
                            <h3 class="mb-0">R$ {{ number_format($stats['valor_total'], 2, ',', '.') }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="bi bi-currency-dollar fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Produtos -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Top 10 Produtos Mais Vendidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th class="text-end">Quantidade Vendida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $product)
                        <tr>
                            <td>{{ $product->descricao }}</td>
                            <td class="text-end">{{ number_format($product->total_vendido, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pedidos por Status -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-bold">Distribuição de Pedidos por Status</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($ordersByStatus as $status)
                <div class="col-md-4 mb-3">
                    <div class="text-center">
                        @if($status->status === 'pendente')
                            <i class="bi bi-clock-history text-warning fs-1"></i>
                            <h5 class="mt-2">Pendentes</h5>
                        @elseif($status->status === 'aprovado')
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <h5 class="mt-2">Aprovados</h5>
                        @else
                            <i class="bi bi-x-circle text-danger fs-1"></i>
                            <h5 class="mt-2">Cancelados</h5>
                        @endif
                        <h3 class="mb-0">{{ $status->total }}</h3>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
