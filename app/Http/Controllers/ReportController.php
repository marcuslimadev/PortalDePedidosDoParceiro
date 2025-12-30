<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(): View
    {
        // Estatísticas gerais
        $stats = [
            'total_pedidos' => Order::count(),
            'pedidos_pendentes' => Order::where('status', 'pendente')->count(),
            'pedidos_aprovados' => Order::where('status', 'aprovado')->count(),
            'total_produtos' => Product::count(),
            'total_lojas' => User::where('role', 'loja')->count(),
            'valor_total' => Order::sum('total'),
        ];
        
        // Pedidos por status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
        // Top 10 produtos mais pedidos
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.descricao', DB::raw('SUM(order_items.quantidade) as total_vendido'))
            ->groupBy('products.id', 'products.descricao')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();
        
        // Pedidos por mês (últimos 6 meses)
        $ordersByMonth = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(total) as valor')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->get();
        
        return view('reports.index', compact('stats', 'ordersByStatus', 'topProducts', 'ordersByMonth'));
    }
}
