@extends('layouts.app')

@section('title', 'Esqueci minha senha')

@section('content')
<div class="container py-5">
    @if($status)
        <div class="alert alert-success">{{ $status }}</div>
    @endif
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Recuperar senha</h5>
            <form method="POST" action="{{ route('password.email') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Enviar link</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection