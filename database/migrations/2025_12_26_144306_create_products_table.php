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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->text('descricao');
            $table->decimal('preco', 12, 2);
            $table->string('unidade', 10);
            $table->string('tributacao', 50);
            $table->integer('estoque')->default(0);
            $table->string('categoria', 100)->nullable();
            
            // Campos Winthor
            $table->string('codprod_winthor', 20)->nullable();
            $table->string('embalagem', 20)->nullable();
            $table->string('marca', 100)->nullable();
            $table->decimal('peso_liquido', 10, 3)->nullable();
            $table->decimal('peso_bruto', 10, 3)->nullable();
            $table->string('ncm', 20)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('categoria');
            $table->index('codprod_winthor');
            $table->fullText('descricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
