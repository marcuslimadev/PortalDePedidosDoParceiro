@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)

@section('content')
<div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-box-seam me-2"></i>
                Portal de Pedidos
            </a>
            <div class="ms-auto">
                <a class="btn btn-outline-light" href="{{ route('orders.index') }}">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="fw-bold mb-3">Pedido #{{ $order->id }}</h3>
                        <p class="text-muted mb-1">Loja: {{ $order->loja->name ?? 'N/A' }}</p>
                        <p class="text-muted mb-3">Criado em: {{ $order->created_at->format('d/m/Y H:i') }}</p>

                        <h5 class="fw-bold">Itens</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-end">Qtd</th>
                                        <th class="text-end">Preço</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->descricao }}</td>
                                        <td class="text-end">{{ $item->quantidade }}</td>
                                        <td class="text-end">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                        <td class="text-end">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Condição de pagamento</p>
                                <p class="fw-semibold">{{ $order->payment_terms }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="mb-1 text-muted">Subtotal</p>
                                <p>R$ {{ number_format($order->subtotal, 2, ',', '.') }}</p>
                                <p class="mb-1 text-muted">Desconto ({{ $order->discount_percentage }}%)</p>
                                <p>R$ {{ number_format($order->discount, 2, ',', '.') }}</p>
                                <h4>Total: R$ {{ number_format($order->total, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Ações</h5>
                        @if($can['approve'])
                        <form method="POST" action="{{ route('orders.approve', $order) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check2-circle me-1"></i> Aprovar
                            </button>
                        </form>
                        @endif
                        @if($can['cancel'])
                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-2">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="cancellation_reason" class="form-control" placeholder="Motivo do cancelamento">
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle me-1"></i> Cancelar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection