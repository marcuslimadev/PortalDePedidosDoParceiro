@extends('layouts.app')

@section('title', 'Verificar Email')

@section('content')
<div class="container py-5">
    @if($status)
        <div class="alert alert-success">{{ $status }}</div>
    @endif
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Verifique seu email</h5>
            <p class="text-muted">Um link de verificação foi enviado para seu email.</p>
        </div>
    </div>
</div>
@endsection