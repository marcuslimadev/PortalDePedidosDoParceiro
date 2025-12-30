<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CreditService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        
        $query = Order::with(['loja', 'items.product']);
        
        if ($user->isLoja()) {
            $query->where('loja_id', $user->id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->search) {
            $query->where('id', 'like', '%' . $request->search . '%');
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $can = [
            'create' => $user->isLoja(),
            'approve' => $user->isAdmin() || $user->isOperador(),
            'cancel' => $user->isAdmin() || $user->isOperador(),
        ];
        
        return view('orders.index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'date_from']),
            'can' => $can,
        ]);
    }

    public function create(Request $request): View
    {
        $products = Product::orderBy('descricao')->get();
        $paymentTerms = PaymentService::getAvailableTerms();
        
        // Produto pré-selecionado via query string
        $preselectedProduct = null;
        if ($request->has('product')) {
            $preselectedProduct = Product::find($request->product);
        }
        
        return view('orders.create', [
            'products' => $products,
            'paymentTerms' => $paymentTerms,
            'preselectedProduct' => $preselectedProduct,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantidade' => 'required|integer|min:1',
            'payment_terms' => 'required|string',
            'observations' => 'nullable|string',
        ]);

        $user = $request->user();

        return DB::transaction(function () use ($validated, $user) {
            // Calcular totais
            $subtotal = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $product->preco * $item['quantidade'];
                $subtotal += $itemSubtotal;
                
                $items[] = [
                    'product' => $product,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $product->preco,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Aplicar descontos
            $totals = PaymentService::calculateDiscount($validated['payment_terms'], $subtotal);

            // Validar crédito
            if (!CreditService::validateCredit($user, $totals['total'])) {
                return back()->withErrors(['credit' => 'Crédito insuficiente para este pedido.']);
            }

            // Criar pedido
            $order = Order::create([
                'loja_id' => $user->id,
                'status' => 'pendente',
                'payment_terms' => $validated['payment_terms'],
                'observations' => $validated['observations'] ?? null,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'discount_percentage' => $totals['discount_percentage'],
                'total' => $totals['total'],
            ]);

            // Criar itens
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Reservar crédito
            CreditService::reserveCredit($user, $totals['total']);

            // Notificações
            NotificationService::notifyOrderCreated($order);

            // Auditoria
            AuditService::logCreate('order', $order->id, $order->toArray());

            return redirect()->route('orders.show', $order)->with('success', 'Pedido criado com sucesso!');
        });
    }

    public function show(Order $order): View
    {
        $user = auth()->user();
        $order->load(['loja', 'items.product']);
        
        $can = [
            'approve' => $user->isAdmin() || $user->isOperador(),
            'cancel' => $user->isAdmin() || $user->isOperador() || ($user->isLoja() && $order->loja_id === $user->id),
        ];
        
        return view('orders.show', [
            'order' => $order,
            'can' => $can,
        ]);
    }

    public function approve(Order $order)
    {
        if ($order->status !== 'pendente') {
            return back()->withErrors(['status' => 'Apenas pedidos pendentes podem ser aprovados.']);
        }

        $order->update(['status' => 'aprovado']);
        
        NotificationService::notifyOrderApproved($order);
        AuditService::log('update', 'order', $order->id, 'Pedido aprovado');

        return back()->with('success', 'Pedido aprovado com sucesso!');
    }

    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($order->status === 'cancelado') {
            return back()->withErrors(['status' => 'Este pedido já foi cancelado.']);
        }

        $order->update([
            'status' => 'cancelado',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        // Liberar crédito
        CreditService::releaseCredit($order);
        NotificationService::notifyOrderCancelled($order, $validated['cancellation_reason']);
        AuditService::log('update', 'order', $order->id, 'Pedido cancelado: ' . $validated['cancellation_reason']);

        return back()->with('success', 'Pedido cancelado com sucesso!');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:aprovado,cancelado',
            'cancellation_reason' => 'required_if:status,cancelado|string',
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $validated['status'],
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
            'cancelled_at' => $validated['status'] === 'cancelado' ? now() : null,
        ]);

        // Liberar crédito se cancelado
        if ($validated['status'] === 'cancelado') {
            CreditService::releaseCredit($order);
            NotificationService::notifyOrderCancelled($order, $validated['cancellation_reason']);
        }

        NotificationService::notifyOrderStatusChanged($order, $oldStatus);
        AuditService::log('update', 'order', $order->id, "Status alterado de {$oldStatus} para {$validated['status']}");

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}

