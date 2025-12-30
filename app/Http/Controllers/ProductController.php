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

    public function bulkDelete(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir produtos'
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id'
        ]);

        $deleted = Product::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => "$deleted produto(s) excluído(s) com sucesso!"
        ]);
    }
}

