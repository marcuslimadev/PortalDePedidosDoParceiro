<?php

namespace App\Services;

class PaymentService
{
    /**
     * Calcula desconto baseado no prazo de pagamento
     */
    public static function calculateDiscount(string $paymentTerms, float $subtotal): array
    {
        $discountPercentage = match (strtolower($paymentTerms)) {
            'antecipado' => 5.0,
            '30 dias' => 2.0,
            default => 0.0,
        };

        $discount = ($subtotal * $discountPercentage) / 100;
        $total = $subtotal - $discount;

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'discount_percentage' => $discountPercentage,
            'total' => round($total, 2),
        ];
    }

    /**
     * Retorna prazos de pagamento disponíveis
     */
    public static function getAvailableTerms(): array
    {
        return [
            'Antecipado' => 'Antecipado (5% desconto)',
            '30 dias' => '30 dias (2% desconto)',
            '60 dias' => '60 dias',
            '90 dias' => '90 dias',
        ];
    }
}
