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
                
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Campos Importados do Excel Winthor</h6>
                    <ul class="mb-0 small">
                        <li><strong>Código:</strong> CODPROD</li>
                        <li><strong>Descrição:</strong> DESCRICAO</li>
                        <li><strong>Unidade:</strong> UNIDADE</li>
                        <li><strong>Estoque:</strong> QTUNIT</li>
                        <li><strong>Categoria:</strong> J11_CATEGORIA / J8_DESCRICAO</li>
                        <li><strong>Marca:</strong> J9_MARCA</li>
                        <li><strong>Embalagem:</strong> EMBALAGEM / EMBALAGEMMASTER</li>
                        <li><strong>Peso Líquido:</strong> PESOLIQ</li>
                        <li><strong>Peso Bruto:</strong> PESOBRUTO</li>
                        <li><strong>Tributação:</strong> NBM</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Atenção:</strong> O arquivo Excel não contém preços. Após a importação, você deverá definir os preços manualmente ou importar uma tabela de preços separada.
                </div>

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