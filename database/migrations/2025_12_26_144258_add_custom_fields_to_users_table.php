<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Role - usando enum do Laravel
            $table->enum('role', ['admin', 'operador', 'loja'])->default('loja')->after('email');
            $table->boolean('ativo')->default(true)->after('role');
            
            // Campos de cliente/loja
            $table->string('cnpj', 20)->nullable()->after('name');
            $table->string('inscricao_estadual', 20)->nullable()->after('cnpj');
            $table->string('rota', 100)->nullable()->after('inscricao_estadual');
            $table->string('segmentacao', 100)->nullable()->after('rota');
            
            // Limite de crédito
            $table->decimal('credit_limit', 14, 2)->nullable()->after('segmentacao');
            $table->decimal('credit_used', 14, 2)->default(0)->after('credit_limit');
            
            // Condições de pagamento padrão
            $table->string('payment_terms', 50)->nullable()->after('credit_used');
            
            // Status do cliente
            $table->string('cliente_status', 20)->default('ativo')->after('payment_terms');
            
            // Indexes
            $table->index('role');
            $table->index('ativo');
            $table->index('cnpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'ativo', 'cnpj', 'inscricao_estadual', 'rota',
                'segmentacao', 'credit_limit', 'credit_used',
                'payment_terms', 'cliente_status'
            ]);
        });
    }
};
