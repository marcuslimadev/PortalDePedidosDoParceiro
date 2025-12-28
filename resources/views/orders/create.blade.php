@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<div class="min-vh-100 bg-light" x-data="orderForm()">
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
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Itens do Pedido</h5>
                    <template x-for="(item, idx) in items" :key="idx">
                        <div class="row g-3 align-items-end mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Produto</label>
                                <select class="form-select" :name="`items[${idx}][product_id]`" x-model="item.product_id" required>
                                    <option value="">Selecione...</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->descricao }} (R$ {{ number_format($p->preco, 2, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade</label>
                                <input type="number" min="1" class="form-control" :name="`items[${idx}][quantidade]`" x-model.number="item.quantidade" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger w-100" @click="removeItem(idx)"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </template>
                    <button type="button" class="btn btn-outline-primary" @click="addItem()"><i class="bi bi-plus-lg me-1"></i> Adicionar Item</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Condição de Pagamento</h5>
                    <select name="payment_terms" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($paymentTerms as $term)
                            <option value="{{ $term }}">{{ $term }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Observações</h5>
                    <textarea name="observations" class="form-control" rows="3" placeholder="Opcional"></textarea>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check2-circle me-1"></i> Finalizar Pedido</button>
            </div>
        </form>
    </div>
</div>

<script>
function orderForm() {
    return {
        items: [{ product_id: '', quantidade: 1 }],
        addItem() { this.items.push({ product_id: '', quantidade: 1 }); },
        removeItem(i) { this.items.splice(i, 1); }
    }
}
</script>
@endsection