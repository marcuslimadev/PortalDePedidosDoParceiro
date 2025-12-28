@extends('layouts.app')

@section('title', 'Confirmar Senha')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Confirmar senha</h5>
            <form method="POST" action="{{ route('password.confirm') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection