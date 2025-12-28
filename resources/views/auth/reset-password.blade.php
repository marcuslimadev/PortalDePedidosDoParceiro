@extends('layouts.app')

@section('title', 'Redefinir Senha')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Redefinir senha</h5>
            <form method="POST" action="{{ route('password.store') }}" class="row g-3">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $email }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nova senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirmar senha</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Redefinir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection