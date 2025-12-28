<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ativo',
        'cnpj',
        'inscricao_estadual',
        'rota',
        'segmentacao',
        'credit_limit',
        'credit_used',
        'payment_terms',
        'cliente_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
            'credit_limit' => 'decimal:2',
            'credit_used' => 'decimal:2',
        ];
    }

    /**
     * Relacionamento: Pedidos da loja
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'loja_id');
    }

    /**
     * Relacionamento: Notificações do usuário
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relacionamento: Histórico de crédito (quando é cliente)
     */
    public function creditHistory()
    {
        return $this->hasMany(ClientCreditHistory::class, 'client_id');
    }

    /**
     * Relacionamento: Alterações de crédito realizadas (quando é admin/operador)
     */
    public function creditChanges()
    {
        return $this->hasMany(ClientCreditHistory::class, 'changed_by');
    }

    /**
     * Scope: Filtrar por role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope: Apenas usuários ativos
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verifica se o usuário é operador
     */
    public function isOperador(): bool
    {
        return $this->role === 'operador';
    }

    /**
     * Verifica se o usuário é loja
     */
    public function isLoja(): bool
    {
        return $this->role === 'loja';
    }

    /**
     * Retorna o crédito disponível
     */
    public function getCreditoDisponivelAttribute(): float
    {
        return max(0, ($this->credit_limit ?? 0) - ($this->credit_used ?? 0));
    }

    /**
     * Accessor para available_credit (alias de credito_disponivel)
     */
    public function getAvailableCreditAttribute(): float
    {
        return $this->credito_disponivel;
    }
}
