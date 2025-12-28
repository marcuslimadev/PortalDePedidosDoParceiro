@extends('layouts.app')

@section('title', 'Importar Produtos')

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
                    <i class="bi bi-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4" style="max-width: 720px;">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h4 class="fw-bold mb-3">Importar Excel / CSV</h4>
                <p class="text-muted">Selecione o arquivo (ex: CADASTRO 500 ITENS.xlsx) e importe o catálogo.</p>
                <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Arquivo</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.csv,.txt" required>
                        <small class="text-muted">Formatos suportados: .xlsx, .csv</small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>
                        Importar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection