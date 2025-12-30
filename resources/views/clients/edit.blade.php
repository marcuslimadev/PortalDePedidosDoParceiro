@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold">Editar Cliente</h4>
            <p class="text-muted small mb-0">Atualize os dados do cliente</p>
        </div>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Voltar
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('clients.update', $client) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $client->name) }}"
                               data-bs-toggle="tooltip"
                               title="Nome completo ou razão social do cliente"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                        <input type="text" 
                               class="form-control @error('cpf_cnpj') is-invalid @enderror" 
                               id="cpf_cnpj" 
                               name="cpf_cnpj" 
                               value="{{ old('cpf_cnpj', $client->cpf_cnpj) }}"
                               data-bs-toggle="tooltip"
                               title="CPF ou CNPJ do cliente">
                        @error('cpf_cnpj')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $client->email) }}"
                               data-bs-toggle="tooltip"
                               title="Email para contato com o cliente">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $client->phone) }}"
                               data-bs-toggle="tooltip"
                               title="Telefone de contato">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Endereço</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              id="address" 
                              name="address" 
                              rows="2"
                              data-bs-toggle="tooltip"
                              title="Endereço completo do cliente">{{ old('address', $client->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="city" class="form-label">Cidade</label>
                        <input type="text" 
                               class="form-control @error('city') is-invalid @enderror" 
                               id="city" 
                               name="city" 
                               value="{{ old('city', $client->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="state" class="form-label">UF</label>
                        <input type="text" 
                               class="form-control @error('state') is-invalid @enderror" 
                               id="state" 
                               name="state" 
                               value="{{ old('state', $client->state) }}"
                               maxlength="2"
                               style="text-transform: uppercase;">
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="zip_code" class="form-label">CEP</label>
                        <input type="text" 
                               class="form-control @error('zip_code') is-invalid @enderror" 
                               id="zip_code" 
                               name="zip_code" 
                               value="{{ old('zip_code', $client->zip_code) }}">
                        @error('zip_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status"
                                data-bs-toggle="tooltip"
                                title="Define se o cliente está ativo ou inativo"
                                required>
                            <option value="ativo" {{ old('status', $client->status) === 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ old('status', $client->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Atualizar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
