@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold">
                Gerenciar Usuários
                <i class="bi bi-info-circle text-primary ms-2" data-bs-toggle="tooltip" 
                   title="Gerencie todos os usuários do sistema com seus perfis e permissões"></i>
            </h4>
            <p class="text-muted small mb-0">Criar, editar e remover usuários do sistema</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="btn btn-primary"
               data-bs-toggle="tooltip" title="Cadastrar novo usuário no sistema">
                <i class="bi bi-plus-circle me-1"></i>
                Novo Usuário
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Cadastrado em</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Administrador</span>
                                @elseif($user->role === 'operador')
                                    <span class="badge bg-info">Operador</span>
                                @else
                                    <span class="badge bg-success">Loja</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary"
                                   data-bs-toggle="tooltip" title="Editar usuário">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== Auth::id())
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser({{ $user->id }})"
                                        data-bs-toggle="tooltip" title="Excluir usuário">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-4 d-block mb-2"></i>
                                Nenhum usuário cadastrado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($users->hasPages())
    <div class="mt-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

<script>
function deleteUser(userId) {
    if (!confirm('Confirma a exclusão deste usuário?')) {
        return;
    }
    
    fetch(`/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erro ao excluir usuário');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar solicitação');
    });
}
</script>
@endsection
