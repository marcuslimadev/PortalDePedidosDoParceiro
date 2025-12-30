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
                        @if($can['approve'] && $order->status === 'pendente')
                        <button type="button" class="btn btn-success w-100 mb-2" onclick="approveOrder({{ $order->id }})"
                                data-bs-toggle="tooltip" title="Aprovar pedido e liberar para processamento">
                            <i class="bi bi-check2-circle me-1"></i> Aprovar
                        </button>
                        @endif
                        @if($can['cancel'] && $order->status !== 'cancelado')
                        <div class="mb-2">
                            <input type="text" id="cancellationReason" class="form-control mb-2" 
                                   placeholder="Motivo do cancelamento"
                                   data-bs-toggle="tooltip" title="Descreva o motivo do cancelamento do pedido">
                        </div>
                        <button type="button" class="btn btn-danger w-100" onclick="cancelOrder({{ $order->id }})"
                                data-bs-toggle="tooltip" title="Cancelar pedido e liberar crédito">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Mostrar toast se houver mensagem de sucesso
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if($errors->any())
        showToast('error', '{{ $errors->first() }}');
    @endif
});

function showToast(type, message) {
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'check-circle' : 'x-circle';
    
    const toastHtml = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header ${bgColor} text-white">
                    <i class="bi bi-${icon} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Sucesso' : 'Erro'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHtml);
    
    setTimeout(() => {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            toast.classList.remove('show');
            setTimeout(() => toast.parentElement.remove(), 300);
        });
    }, 4000);
}

function approveOrder(orderId) {
    if (!confirm('Confirma a aprovação deste pedido?')) {
        return;
    }
    
    fetch(`/orders/${orderId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message || 'Pedido aprovado com sucesso!');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Erro ao aprovar pedido');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('error', 'Erro ao processar solicitação');
    });
}

function cancelOrder(orderId) {
    const reason = document.getElementById('cancellationReason').value.trim();
    
    if (!reason) {
        showToast('error', 'Por favor, informe o motivo do cancelamento');
        return;
    }
    
    if (!confirm('Confirma o cancelamento deste pedido?')) {
        return;
    }
    
    fetch(`/orders/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cancellation_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message || 'Pedido cancelado com sucesso!');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Erro ao cancelar pedido');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('error', 'Erro ao processar solicitação');
    });
}
</script>
@endsection