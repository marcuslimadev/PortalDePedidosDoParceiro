@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold">Gerenciar Clientes</h4>
            <p class="text-muted small mb-0">Lista completa de clientes cadastrados</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Cadastrar novo cliente">
            <i class="bi bi-plus-circle me-1"></i> Novo Cliente
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>CPF/CNPJ</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Cidade/UF</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                        <tr>
                            <td class="fw-semibold">{{ $client->name }}</td>
                            <td><code class="small">{{ $client->cpf_cnpj ?? '-' }}</code></td>
                            <td>{{ $client->email ?? '-' }}</td>
                            <td>{{ $client->phone ?? '-' }}</td>
                            <td>{{ $client->city ? $client->city . '/' . $client->state : '-' }}</td>
                            <td>
                                @if($client->status === 'ativo')
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('clients.edit', $client) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" 
                                       title="Editar cliente">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="tooltip" 
                                            title="Excluir cliente"
                                            onclick="deleteClient({{ $client->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                Nenhum cliente cadastrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clients->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $clients->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function deleteClient(clientId) {
    if (!confirm('Tem certeza que deseja excluir este cliente?')) {
        return;
    }
    
    fetch(`/clients/${clientId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Erro ao excluir cliente');
    });
}
</script>
@endsection
