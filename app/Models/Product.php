<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descricao',
        'preco',
        'unidade',
        'tributacao',
        'estoque',
        'categoria',
        'codprod_winthor',
        'embalagem',
        'marca',
        'peso_liquido',
        'peso_bruto',
        'ncm',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'peso_liquido' => 'decimal:3',
        'peso_bruto' => 'decimal:3',
        'estoque' => 'integer',
    ];

    public function priceHistory()
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('descricao', 'LIKE', "%{$search}%")
                     ->orWhere('codigo', 'LIKE', "%{$search}%");
    }

    public function scopeByCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeEmEstoque($query)
    {
        return $query->where('estoque', '>', 0);
    }
}
