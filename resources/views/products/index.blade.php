@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css" rel="stylesheet">

<div class="min-vh-100 bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-box-seam me-2"></i>
                Portal de Pedidos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/products">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/orders">Pedidos</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile">Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Sair</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 fw-bold"><i class="bi bi-collection me-2"></i>Catálogo de Produtos</h1>
                <p class="text-muted">Tabela interativa com busca, filtros e ordenação</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('products.import') }}" class="btn btn-primary">
                    <i class="bi bi-upload me-1"></i>
                    Importar Excel/CSV
                </a>
            </div>
        </div>

        <!-- Products DataTable -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="productsTable" class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">Código</th>
                                <th style="width: 28%;">Descrição</th>
                                <th style="width: 15%;">Categoria</th>
                                <th style="width: 12%;">Preço</th>
                                <th style="width: 12%;">Estoque</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="align-middle">
                                <td>
                                    <span class="badge bg-primary text-light">{{ $product->codigo }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $product->descricao }}</div>
                                    <small class="text-muted">{{ Str::limit($product->descricao, 40) }}</small>
                                </td>
                                <td>
                                    @if($product->categoria)
                                        <span class="badge bg-info">{{ $product->categoria }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">R$ {{ number_format($product->preco, 2, ',', '.') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 20px; width: 100px;" role="progressbar">
                                            @php
                                                $percentage = min(($product->estoque / 1000) * 100, 100);
                                                $barColor = $product->estoque > 500 ? 'success' : ($product->estoque > 100 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress-bar bg-{{ $barColor }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="fw-bold" style="min-width: 60px;">{{ number_format($product->estoque, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($product->estoque > 0)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Em estoque</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Esgotado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary" title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-success" onclick="addToCart({{ $product->id }})" title="Adicionar ao pedido">
                                            <i class="bi bi-bag-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted d-block mb-3"></i>
                                    <h5 class="text-muted">Nenhum produto encontrado</h5>
                                    <p class="text-muted">Tente ajustar os filtros de busca ou importe um novo catálogo</p>
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

<script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = new DataTable('#productsTable', {
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        columnDefs: [
            { orderable: false, targets: [6] }, // Desabilita ordenação na coluna de ações
        ],
        dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
              '<"row"<"col-sm-12"tr>>' +
              '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    });

    // Estilo das caixas de busca e quantidade
    const style = document.createElement('style');
    style.textContent = `
        #productsTable_filter input {
            border-radius: 0.375rem;
        }
        #productsTable_length select {
            border-radius: 0.375rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.375rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #0d6efd;
            color: white !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #0d6efd;
            color: white !important;
        }
    `;
    document.head.appendChild(style);
});

function addToCart(productId) {
    alert('Funcionalidade em desenvolvimento: Adicionar produto #' + productId + ' ao pedido');
    // Será implementado com HTMX
}
</script>

<style>
    #productsTable thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    #productsTable tbody tr {
        transition: background-color 0.2s ease;
    }
    
    #productsTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .dataTables_wrapper {
        font-size: 0.95rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.3rem 0.5rem;
        font-size: 0.85rem;
    }
</style>
@endsection
