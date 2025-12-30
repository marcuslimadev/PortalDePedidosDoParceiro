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
                <p class="text-muted">Baixe o modelo, preencha e envie para importar produtos em lote.</p>
                
                <div class="alert alert-primary mb-3">
                    <h6 class="alert-heading"><i class="bi bi-download me-2"></i>Baixar Modelos de Importação</h6>
                    <p class="mb-2 small">Preencha todos os campos, especialmente o <strong>preço</strong>!</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.import.downloadExcel') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-excel me-1"></i>
                            Baixar Modelo Excel (.xlsx)
                        </a>
                        <a href="{{ route('products.import.downloadCsv') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-text me-1"></i>
                            Baixar Modelo CSV (.csv)
                        </a>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Campos do Modelo</h6>
                    <ul class="mb-0 small">
                        <li><strong>codigo:</strong> Código único do produto (obrigatório)</li>
                        <li><strong>descricao:</strong> Nome/descrição do produto (obrigatório)</li>
                        <li><strong>preco:</strong> Preço de venda (obrigatório) - Ex: 150.00</li>
                        <li><strong>unidade:</strong> Unidade de medida - Ex: UN, CX, KG</li>
                        <li><strong>tributacao:</strong> Código de tributação - Ex: T01</li>
                        <li><strong>estoque:</strong> Quantidade em estoque - Ex: 100</li>
                        <li><strong>categoria:</strong> Categoria do produto - Ex: Premium</li>
                        <li><strong>marca:</strong> Marca do produto (opcional)</li>
                        <li><strong>embalagem:</strong> Tipo de embalagem (opcional)</li>
                        <li><strong>peso_liquido:</strong> Peso líquido em kg (opcional)</li>
                        <li><strong>peso_bruto:</strong> Peso bruto em kg (opcional)</li>
                        <li><strong>nbm:</strong> Nomenclatura Brasileira de Mercadorias (opcional)</li>
                        <li><strong>ean_produto:</strong> Código de barras do produto (opcional)</li>
                        <li><strong>ean_embalagem:</strong> Código de barras da embalagem (opcional)</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('products.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Arquivo Preenchido</label>
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