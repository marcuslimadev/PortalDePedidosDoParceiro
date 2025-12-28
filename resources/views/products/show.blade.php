@extends('layouts.app')

@section('title', 'Detalhes do Produto')

@section('content')
<div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-box-seam me-2"></i>
                Portal de Pedidos
            </a>
            <div class="ms-auto">
                <a class="btn btn-outline-light" href="{{ route('products.index') }}">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary">{{ $product->codigo }}</span>
                            @if($product->estoque > 0)
                                <span class="badge bg-success">Em estoque</span>
                            @else
                                <span class="badge bg-danger">Esgotado</span>
                            @endif
                        </div>
                        <h3 class="fw-bold mb-2">{{ $product->descricao }}</h3>
                        @if($product->categoria)
                        <p class="text-muted">
                            <i class="bi bi-tag me-1"></i>
                            {{ $product->categoria }}
                        </p>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 text-muted">Preço</p>
                                <h2 class="text-primary">R$ {{ number_format($product->preco, 2, ',', '.') }}</h2>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 text-muted">Estoque</p>
                                <h5 class="fw-bold">{{ number_format($product->estoque, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">Ações</h5>
                        <p class="text-muted">Adicione aos pedidos a partir da tela de criação.</p>
                        <a href="{{ route('orders.create') }}" class="btn btn-primary w-100">
                            <i class="bi bi-bag-plus me-1"></i>
                            Criar Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection