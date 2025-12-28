@extends('layouts.app')

@section('title', 'Login - Portal de Pedidos')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Logo/Header -->
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                            </div>
                            <h1 class="h3 fw-bold text-dark">Portal de Pedidos</h1>
                            <p class="text-muted">Faça login para continuar</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input 
                                        id="email" 
                                        type="email" 
                                        class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autofocus 
                                        autocomplete="username"
                                        placeholder="seu@email.com"
                                    >
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input 
                                        id="password" 
                                        type="password" 
                                        class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                        name="password" 
                                        required 
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                    >
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        id="remember_me" 
                                        name="remember"
                                    >
                                    <label class="form-check-label" for="remember_me">
                                        Lembrar-me
                                    </label>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Entrar
                                </button>
                                
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="btn btn-link text-decoration-none">
                                        Esqueceu sua senha?
                                    </a>
                                @endif
                            </div>
                        </form>

                        <!-- Footer -->
                        <div class="text-center mt-4 pt-4 border-top">
                            <p class="text-muted small mb-0">
                                Não tem uma conta? 
                                <a href="/contato" class="text-primary text-decoration-none fw-semibold">Entre em contato</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
