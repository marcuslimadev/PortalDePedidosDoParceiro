@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        transition: all 0.2s;
    }
    .cart-item {
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 0;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .qty-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .qty-input {
        width: 60px;
        text-align: center;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 4px;
    }
    .empty-cart {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 fw-bold">Novo Pedido</h1>
            <p class="text-muted">Adicione produtos ao carrinho e finalize seu pedido</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro ao processar pedido:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Catálogo de Produtos -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-search me-2"></i>Buscar Produto
                    </h5>
                    <select id="productSearch" class="form-select" style="width: 100%">
                        <option value="">Digite para buscar produtos...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-price="{{ $product->preco }}"
                                    data-codigo="{{ $product->codigo }}"
                                    data-desc="{{ $product->descricao }}">
                                {{ $product->codigo }} - {{ $product->descricao }} - R$ {{ number_format($product->preco, 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Produtos Disponíveis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="productsGrid">
                        @forelse($products->take(12) as $product)
                            <div class="col-md-4 col-sm-6">
                                <div class="card product-card h-100 border shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary">{{ $product->codigo }}</span>
                                            @if($product->estoque > 0)
                                                <span class="badge bg-success">Em estoque</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Sob encomenda</span>
                                            @endif
                                        </div>
                                        <h6 class="card-title mb-2" style="min-height: 40px; font-size: 14px;">
                                            {{ Str::limit($product->descricao, 50) }}
                                        </h6>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="h5 mb-0 text-primary">
                                                R$ {{ number_format($product->preco, 2, ',', '.') }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="addToCart({{ $product->id }}, '{{ $product->codigo }}', '{{ addslashes($product->descricao) }}', {{ $product->preco }})">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p>Nenhum produto disponível</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrinho -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold d-flex align-items-center justify-content-between">
                        <span>
                            <i class="bi bi-cart3 me-2"></i>Carrinho
                        </span>
                        <span class="badge bg-white text-primary" id="cartCount">0</span>
                    </h5>
                </div>
                <div class="card-body" id="cartItems" style="max-height: 400px; overflow-y: auto;">
                    <div class="empty-cart">
                        <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                        <p>Seu carrinho está vazio</p>
                        <small>Adicione produtos para começar</small>
                    </div>
                </div>
                <div class="card-footer bg-light border-0" id="cartFooter" style="display: none;">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong id="cartSubtotal">R$ 0,00</strong>
                    </div>
                    <button type="button" class="btn btn-success w-100 mt-2" onclick="proceedToCheckout()">
                        <i class="bi bi-check-circle me-2"></i>Finalizar Pedido
                    </button>
                    <button type="button" class="btn btn-outline-danger w-100 mt-2" onclick="clearCart()">
                        <i class="bi bi-trash me-2"></i>Limpar Carrinho
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Checkout -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('orders.store') }}" id="checkoutForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-check-circle me-2"></i>Finalizar Pedido
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Itens do Pedido -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Resumo do Pedido</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produto</th>
                                            <th width="80">Qtd</th>
                                            <th width="120" class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="checkoutItems"></tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th class="text-end" id="checkoutTotal">R$ 0,00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Condição de Pagamento -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Condição de Pagamento *</label>
                            <select name="payment_terms" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($paymentTerms as $term)
                                    <option value="{{ $term }}">{{ $term }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea name="observations" class="form-control" rows="3" placeholder="Informações adicionais (opcional)"></textarea>
                        </div>

                        <div id="cartItemsInputs"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let cart = [];

$(document).ready(function() {
    // Inicializar Select2
    $('#productSearch').select2({
        theme: 'bootstrap-5',
        placeholder: 'Digite para buscar produtos...',
        allowClear: true,
        language: {
            noResults: function() { return "Nenhum produto encontrado"; },
            searching: function() { return "Buscando..."; }
        }
    }).on('select2:select', function(e) {
        const data = e.params.data;
        const option = $(data.element);
        addToCart(
            data.id,
            option.data('codigo'),
            option.data('desc'),
            option.data('price')
        );
        $(this).val(null).trigger('change');
    });
});

function addToCart(id, codigo, descricao, preco) {
    const existing = cart.find(item => item.id == id);
    
    if (existing) {
        existing.quantidade++;
    } else {
        cart.push({
            id: id,
            codigo: codigo,
            descricao: descricao,
            preco: parseFloat(preco),
            quantidade: 1
        });
    }
    
    updateCart();
    
    // Feedback visual
    const toast = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong class="me-auto">Adicionado!</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${descricao} adicionado ao carrinho</div>
            </div>
        </div>
    `;
    $('body').append(toast);
    setTimeout(() => $('.toast').fadeOut(() => $('.toast').parent().remove()), 3000);
}

function updateCart() {
    const cartItemsDiv = $('#cartItems');
    const cartCount = $('#cartCount');
    const cartFooter = $('#cartFooter');
    const cartSubtotal = $('#cartSubtotal');
    
    if (cart.length === 0) {
        cartItemsDiv.html(`
            <div class="empty-cart">
                <i class="bi bi-cart-x fs-1 d-block mb-3"></i>
                <p>Seu carrinho está vazio</p>
                <small>Adicione produtos para começar</small>
            </div>
        `);
        cartFooter.hide();
        cartCount.text('0');
        return;
    }
    
    let html = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        const subtotal = item.preco * item.quantidade;
        total += subtotal;
        
        html += `
            <div class="cart-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h6 class="mb-1" style="font-size: 13px;">${item.descricao}</h6>
                        <small class="text-muted">${item.codigo}</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="removeFromCart(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary qty-btn" onclick="updateQuantity(${index}, -1)">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="text" class="qty-input" value="${item.quantidade}" readonly>
                        <button type="button" class="btn btn-outline-secondary qty-btn" onclick="updateQuantity(${index}, 1)">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <strong>R$ ${subtotal.toFixed(2).replace('.', ',')}</strong>
                </div>
            </div>
        `;
    });
    
    cartItemsDiv.html(html);
    cartCount.text(cart.length);
    cartSubtotal.text('R$ ' + total.toFixed(2).replace('.', ','));
    cartFooter.show();
}

function updateQuantity(index, delta) {
    cart[index].quantidade += delta;
    if (cart[index].quantidade <= 0) {
        cart.splice(index, 1);
    }
    updateCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

function clearCart() {
    if (confirm('Deseja realmente limpar o carrinho?')) {
        cart = [];
        updateCart();
    }
}

function proceedToCheckout() {
    if (cart.length === 0) {
        alert('Adicione produtos ao carrinho primeiro');
        return;
    }
    
    // Preencher resumo do checkout
    let checkoutHtml = '';
    let total = 0;
    
    cart.forEach(item => {
        const subtotal = item.preco * item.quantidade;
        total += subtotal;
        checkoutHtml += `
            <tr>
                <td><small>${item.codigo} - ${item.descricao}</small></td>
                <td>${item.quantidade}</td>
                <td class="text-end">R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
            </tr>
        `;
    });
    
    $('#checkoutItems').html(checkoutHtml);
    $('#checkoutTotal').text('R$ ' + total.toFixed(2).replace('.', ','));
    
    // Criar inputs hidden para envio
    let inputsHtml = '';
    cart.forEach((item, index) => {
        inputsHtml += `
            <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
            <input type="hidden" name="items[${index}][quantidade]" value="${item.quantidade}">
        `;
    });
    $('#cartItemsInputs').html(inputsHtml);
    
    // Abrir modal
    new bootstrap.Modal(document.getElementById('checkoutModal')).show();
}
</script>
@endsection