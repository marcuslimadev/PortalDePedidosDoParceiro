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
        Schema::table('products', function (Blueprint $table) {
            $table->string('nbm', 20)->nullable()->after('ncm');
            $table->string('ean_produto', 50)->nullable()->after('nbm');
            $table->string('ean_embalagem', 50)->nullable()->after('ean_produto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['nbm', 'ean_produto', 'ean_embalagem']);
        });
    }
};
