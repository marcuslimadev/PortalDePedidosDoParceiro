<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        return match ($user->role) {
            'admin' => $this->adminDashboard($user),
            'operador' => $this->operadorDashboard($user),
            'loja' => $this->lojaDashboard($user),
            default => view('dashboard.index'),
        };
    }

    private function adminDashboard(User $user)
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pendente')->count(),
            'approved_orders' => Order::where('status', 'aprovado')->count(),
            'total_value' => Order::where('status', 'aprovado')->sum('total'),
        ];

        $recentOrders = Order::with('loja')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeUsers = User::where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        return view('dashboard.admin', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'activeUsers' => $activeUsers,
        ]);
    }

    private function operadorDashboard(User $user)
    {
        $stats = [
            'pending_orders' => Order::where('status', 'pendente')->count(),
            'approved_today' => Order::where('status', 'aprovado')
                ->whereDate('updated_at', today())
                ->count(),
            'cancelled_today' => Order::where('status', 'cancelado')
                ->whereDate('updated_at', today())
                ->count(),
            'pending_value' => Order::where('status', 'pendente')->sum('total'),
        ];

        $pendingOrders = Order::with(['loja' => function ($query) {
                $query->select('id', 'name', 'cnpj', 'credit_limit', 'credit_used');
            }])
            ->where('status', 'pendente')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('dashboard.operador', [
            'stats' => $stats,
            'pendingOrders' => $pendingOrders,
        ]);
    }

    private function lojaDashboard(User $user)
    {
        $stats = [
            'pending_orders' => Order::where('loja_id', $user->id)
                ->where('status', 'pendente')
                ->count(),
            'approved_orders' => Order::where('loja_id', $user->id)
                ->where('status', 'aprovado')
                ->count(),
            'month_total' => Order::where('loja_id', $user->id)
                ->where('status', 'aprovado')
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];

        $recentOrders = Order::where('loja_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $store = User::select('id', 'name', 'cnpj', 'credit_limit', 'credit_used')
            ->find($user->id);

        return view('dashboard.loja', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'store' => $store,
        ]);
    }
}
