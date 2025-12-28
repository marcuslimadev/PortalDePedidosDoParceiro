@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
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
                <h1 class="h3 fw-bold">Catálogo de Produtos</h1>
                <p class="text-muted">Navegue pelos produtos disponíveis</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('products.import') }}" class="btn btn-outline-primary">
                    <i class="bi bi-upload me-1"></i>
                    Importar Excel/CSV
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Buscar</label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Código ou descrição do produto..."
                            value="{{ $filters['search'] ?? '' }}"
                        >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select name="categoria" class="form-select">
                            <option value="">Todas as categorias</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria }}" {{ ($filters['categoria'] ?? '') == $categoria ? 'selected' : '' }}>
                                    {{ $categoria }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Buscar
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4">
            @forelse($products as $product)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary">{{ $product->codigo }}</span>
                            @if($product->estoque > 0)
                                <span class="badge bg-success">Em estoque</span>
                            @else
                                <span class="badge bg-danger">Esgotado</span>
                            @endif
                        </div>
                        
                        <h5 class="card-title mb-2">{{ $product->descricao }}</h5>
                        
                        @if($product->categoria)
                            <p class="text-muted small mb-2">
                                <i class="bi bi-tag me-1"></i>
                                {{ $product->categoria }}
                            </p>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="mb-0 text-muted small">Preço</p>
                                <h4 class="mb-0 text-primary">R$ {{ number_format($product->preco, 2, ',', '.') }}</h4>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 text-muted small">Estoque</p>
                                <p class="mb-0 fw-bold">{{ number_format($product->estoque, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-eye me-1"></i>
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">Nenhum produto encontrado</h4>
                        <p class="text-muted">Tente ajustar os filtros de busca</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,.15)!important;
}
</style>
@endsection
