@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-box-seam me-2"></i>
                Portal de Pedidos
            </a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Suas notificações</h5>
                <div class="list-group list-group-flush">
                    @forelse($notifications as $n)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="mb-1 fw-semibold">{{ $n->title ?? 'Notificação' }}</p>
                                <p class="mb-0 text-muted">{{ $n->message ?? $n->data }}</p>
                            </div>
                            <small class="text-muted">{{ $n->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Nenhuma notificação.</p>
                    @endforelse
                </div>
                @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $notifications->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection