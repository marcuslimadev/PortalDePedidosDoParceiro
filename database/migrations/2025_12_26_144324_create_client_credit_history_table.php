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
        Schema::create('client_credit_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('previous_credit_limit', 14, 2)->nullable();
            $table->decimal('new_credit_limit', 14, 2)->nullable();
            $table->string('previous_payment_terms', 50)->nullable();
            $table->string('new_payment_terms', 50)->nullable();
            $table->string('previous_status', 20)->nullable();
            $table->string('new_status', 20)->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('client_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_credit_history');
    }
};
