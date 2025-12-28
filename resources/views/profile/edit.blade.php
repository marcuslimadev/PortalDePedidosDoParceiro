@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
<div class="min-vh-100 bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-person-circle me-2"></i>
                Perfil
            </a>
        </div>
    </nav>

    <div class="container py-4">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if($mustVerifyEmail)
            <div class="alert alert-warning">Seu email ainda não foi verificado.</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
                <hr>
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Tem certeza que deseja excluir sua conta?');">
                    @csrf
                    @method('DELETE')
                    <div class="mb-2">
                        <input type="password" name="password" class="form-control" placeholder="Confirme sua senha" required>
                    </div>
                    <button class="btn btn-danger">Excluir Conta</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection