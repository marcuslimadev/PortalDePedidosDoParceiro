@extends('layouts.app')

@section('title', 'Registrar')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Criar conta</h5>
            <form method="POST" action="{{ route('register') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmar senha</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection