<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Order;

class NotificationService
{
    /**
     * Cria uma notificação genérica
     */
    public static function create(int $userId, string $title, string $message, string $type = 'general'): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'read' => false,
        ]);
    }

    /**
     * Marca notificação como lida
     */
    public static function markAsRead(int $notificationId): void
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    /**
     * Marca todas as notificações de um usuário como lidas
     */
    public static function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Retorna contagem de notificações não lidas
     */
    public static function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Cria notificação para pedido criado
     */
    public static function notifyOrderCreated(Order $order): void
    {
        // Notificar admins e operadores
        $adminsOperadores = User::whereIn('role', ['admin', 'operador'])->get();
        
        foreach ($adminsOperadores as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'order.created',
                'title' => 'Novo Pedido',
                'message' => "Pedido #{$order->id} criado por {$order->loja->name} - Total: R$ " . number_format($order->total, 2, ',', '.'),
            ]);
        }

        // Notificar a loja
        Notification::create([
            'user_id' => $order->loja_id,
            'type' => 'order.created',
            'title' => 'Pedido Criado',
            'message' => "Seu pedido #{$order->id} foi criado com sucesso! Total: R$ " . number_format($order->total, 2, ',', '.'),
        ]);
    }

    /**
     * Notifica mudança de status do pedido
     */
    public static function notifyOrderStatusChanged(Order $order, string $oldStatus): void
    {
        $statusText = match ($order->status) {
            'aprovado' => 'aprovado',
            'cancelado' => 'cancelado',
            default => $order->status,
        };

        Notification::create([
            'user_id' => $order->loja_id,
            'type' => 'order.status_changed',
            'title' => 'Status do Pedido Alterado',
            'message' => "Pedido #{$order->id} foi {$statusText}.",
        ]);
    }

    /**
     * Notifica cancelamento de pedido
     */
    public static function notifyOrderCancelled(Order $order, string $reason): void
    {
        Notification::create([
            'user_id' => $order->loja_id,
            'type' => 'order.cancelled',
            'title' => 'Pedido Cancelado',
            'message' => "Pedido #{$order->id} foi cancelado. Motivo: {$reason}",
        ]);
    }
}
