<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['loja_id', 'status', 'payment_terms', 'observations', 'subtotal', 'discount', 'discount_percentage', 'total', 'cancellation_reason', 'cancelled_at'];
    protected $casts = ['subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'discount_percentage' => 'decimal:2', 'total' => 'decimal:2', 'cancelled_at' => 'datetime'];

    public function loja() { return $this->belongsTo(User::class, 'loja_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function scopeStatus($query, $status) { return $query->where('status', $status); }
    public function scopePendentes($query) { return $query->where('status', 'pendente'); }
    public function isPendente(): bool { return $this->status === 'pendente'; }
}
