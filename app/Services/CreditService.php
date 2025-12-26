<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * Valida se a loja tem crédito disponível
     */
    public static function validateCredit(User $loja, float $orderTotal): bool
    {
        $creditAvailable = ($loja->credit_limit ?? 0) - ($loja->credit_used ?? 0);
        return $creditAvailable >= $orderTotal;
    }

    /**
     * Reserva crédito para um pedido (usa FOR UPDATE)
     */
    public static function reserveCredit(User $loja, float $amount): void
    {
        DB::transaction(function () use ($loja, $amount) {
            // Lock da linha para evitar race conditions
            $lojaLocked = User::where('id', $loja->id)->lockForUpdate()->first();
            
            $lojaLocked->credit_used = ($lojaLocked->credit_used ?? 0) + $amount;
            $lojaLocked->save();
        });
    }

    /**
     * Libera crédito de um pedido cancelado
     */
    public static function releaseCredit(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $loja = User::where('id', $order->loja_id)->lockForUpdate()->first();
            
            $loja->credit_used = max(0, ($loja->credit_used ?? 0) - $order->total);
            $loja->save();
        });
    }

    /**
     * Retorna crédito disponível
     */
    public static function getAvailableCredit(User $loja): float
    {
        return max(0, ($loja->credit_limit ?? 0) - ($loja->credit_used ?? 0));
    }
}
