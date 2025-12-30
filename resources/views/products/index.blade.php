@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col">
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-box-seam me-2"></i>Catálogo de Produtos</h1>
            <p class="text-muted small mb-0">Tabela interativa com busca, filtros e ordenação</p>
        </div>
        <div class="col-auto">
            <button id="deleteSelectedBtn" class="btn btn-danger me-2" style="display: none;" onclick="deleteSelected()">
                <i class="bi bi-trash me-1"></i>
                Excluir Selecionados (<span id="selectedCount">0</span>)
            </button>
            <a href="{{ route('products.import') }}" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i>
                Importar Excel/CSV
            </a>
        </div>
    </div>

        <!-- Products DataTable -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="productsTable" class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 3%;">
                                    <input type="checkbox" id="selectAll" class="form-check-input" title="Selecionar todos">
                                </th>
                                <th style="width: 8%;">Código</th>
                                <th style="width: 25%;">Descrição</th>
                                <th style="width: 12%;">Categoria</th>
                                <th style="width: 10%;">Preço</th>
                                <th style="width: 8%;">Estoque</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 10%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-select" value="{{ $product->id }}" data-codigo="{{ $product->codigo }}">
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $product->codigo }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $product->descricao }}">
                                        {{ $product->descricao }}
                                    </div>
                                </td>
                                <td>
                                    @if($product->categoria)
                                        <span class="badge bg-info">{{ $product->categoria }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-primary fw-semibold">R$ {{ number_format($product->preco, 2, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ number_format($product->estoque, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($product->estoque > 0)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Em estoque</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Esgotado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewProduct({{ $product->id }})" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="addToCart({{ $product->id }})" title="Adicionar">
                                            <i class="bi bi-bag-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox display-4 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Nenhum produto encontrado</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>

<script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = new DataTable('#productsTable', {
        responsive: true,
        pageLength: 50,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        },
        columnDefs: [
            { orderable: false, targets: [0, 7] },
        ],
        dom: '<"row mb-2"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-8"f>>' +
              '<"row"<"col-sm-12"tr>>' +
              '<"row mt-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Reattach event listeners após redesenho da tabela
            attachCheckboxListeners();
        }
    });

    function attachCheckboxListeners() {
        // Usar delegação de eventos para checkboxes dinâmicos
        document.querySelectorAll('.row-select').forEach(checkbox => {
            checkbox.removeEventListener('change', updateSelectedCount);
            checkbox.addEventListener('change', updateSelectedCount);
        });
    }

    // Selecionar todos usando delegação
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'selectAll') {
            const checkboxes = document.querySelectorAll('.row-select');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateSelectedCount();
        }
    });

    // Inicializar listeners
    attachCheckboxListeners();

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.row-select:checked');
        const count = selected.length;
        const selectedCountEl = document.getElementById('selectedCount');
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        
        if (selectedCountEl) selectedCountEl.textContent = count;
        if (deleteBtn) deleteBtn.style.display = count > 0 ? 'inline-block' : 'none';
        
        // Atualizar checkbox "selecionar todos"
        const total = document.querySelectorAll('.row-select').length;
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = count === total && count > 0;
    }

    window.updateSelectedCount = updateSelectedCount;
});

function deleteSelected() {
    const selected = Array.from(document.querySelectorAll('.row-select:checked'));
    if (selected.length === 0) {
        alert('Selecione ao menos um produto para excluir');
        return;
    }

    const ids = selected.map(cb => cb.value);
    const codigos = selected.map(cb => cb.dataset.codigo).join(', ');
    
    if (confirm(`Deseja realmente excluir ${selected.length} produto(s)?\n\nCódigos: ${codigos}`)) {
        // TODO: Implementar exclusão em lote via AJAX
        console.log('IDs para excluir:', ids);
        alert('Funcionalidade de exclusão em desenvolvimento');
    }
}

function viewProduct(productId) {
    window.location.href = '/products/' + productId;
}

function addToCart(productId) {
    alert('Funcionalidade em desenvolvimento: Adicionar produto #' + productId + ' ao pedido');
}
</script>

<style>
    /* Estilo compacto tipo Excel */
    #productsTable {
        font-size: 0.875rem;
    }
    
    #productsTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        color: #495057;
        white-space: nowrap;
    }
    
    #productsTable tbody td {
        padding: 0.4rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    #productsTable tbody tr {
        transition: background-color 0.15s ease;
    }
    
    #productsTable tbody tr:hover {
        background-color: #f1f3f5;
    }
    
    #productsTable tbody tr.selected {
        background-color: #e7f3ff;
    }
    
    .dataTables_wrapper {
        font-size: 0.875rem;
    }
    
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }
    
    /* Compactar margens */
    .card-body.p-0 {
        padding: 0 !important;
    }
    
    .table-responsive {
        margin: 0;
    }
    
    /* Estilo dos checkboxes */
    .form-check-input {
        cursor: pointer;
        width: 1.1em;
        height: 1.1em;
    }
    
    /* Scroll horizontal suave */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Paginação compacta */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
@endsection
