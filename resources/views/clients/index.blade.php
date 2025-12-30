@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold">Gerenciar Clientes (Lojas Parceiras)</h4>
            <p class="text-muted small mb-0">Os clientes são os usuários com perfil "Loja"</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-primary">
            <i class="bi bi-people me-1"></i> Ver Usuários/Lojas
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-shop display-1 text-primary mb-3"></i>
            <h5 class="mb-3">Gestão de Clientes (Lojas)</h5>
            <p class="text-muted mb-4">
                No contexto deste sistema, <strong>os clientes são as lojas parceiras</strong> que fazem pedidos.<br>
                Eles estão cadastrados como <span class="badge bg-success">Usuários com perfil "Loja"</span>.
            </p>
            <div class="alert alert-info d-inline-block">
                <i class="bi bi-info-circle me-2"></i>
                Para gerenciar clientes/lojas, acesse o menu <strong>Usuários</strong>
            </div>
        </div>
    </div>
</div>
@endsection
