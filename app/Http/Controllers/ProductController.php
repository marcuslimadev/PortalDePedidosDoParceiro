<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        
        if ($request->search) {
            $query->search($request->search);
        }
        
        if ($request->categoria) {
            $query->categoria($request->categoria);
        }
        
        $products = $query->orderBy('descricao')->paginate(20);
        $categorias = Product::distinct()->pluck('categoria')->filter();
        
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categorias' => $categorias,
            'filters' => $request->only(['search', 'categoria']),
        ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }
}

