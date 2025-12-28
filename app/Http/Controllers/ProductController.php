<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Product::query();
        
        if ($request->search) {
            $query->search($request->search);
        }
        
        if ($request->categoria) {
            $query->categoria($request->categoria);
        }
        
        $products = $query->orderBy('descricao')->paginate(20);
        $categorias = Product::distinct()->pluck('categoria')->filter();
        
        $can = [
            'create' => $user->isAdmin() || $user->isOperador(),
            'edit' => $user->isAdmin() || $user->isOperador(),
            'delete' => $user->isAdmin(),
        ];
        
        return view('products.index', [
            'products' => $products,
            'categorias' => $categorias,
            'filters' => $request->only(['search', 'categoria']),
            'can' => $can,
        ]);
    }

    public function show(Product $product)
    {
        return view('products.show', [
            'product' => $product,
        ]);
    }
}

