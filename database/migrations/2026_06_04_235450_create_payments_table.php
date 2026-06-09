<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Payment records for orders.
     * Supports multiple payment methods:
     *   - stripe: Credit/debit cards via Stripe
     *   - kpay: KBZ Pay mobile wallet
     *   - wave: Wave Pay mobile wallet
     *   - cod: Cash on delivery (no online payment)
     * transaction_reference stores the external payment ID:
     *   - Stripe → Session ID
     *   - KPay/Wave → Transaction number
     *   - COD → NULL (no online transaction)
     * amount may differ from order total in case of partial refunds.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // CASCADE: Delete payment record when order is deleted
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();
            
            // Payment method selection
            $table->enum('payment_method', [
                'stripe',  // Stripe card payment
                'kpay',    // KBZ Pay
                'wave',    // Wave Pay
                'cod'      // Cash on delivery
            ]);
            
            $table->string('transaction_reference', 100)->nullable(); // External transaction ID (NULL for COD)
            $table->decimal('amount', 10, 2);                        // Amount paid
            
            // Payment status tracking
            $table->enum('status', [
                'pending',    // Awaiting payment
                'completed',  // Payment successful
                'failed',     // Payment attempt failed
                'refunded'    // Payment refunded to customer
            ])->default('pending');
            
            $table->timestamp('paid_at')->nullable(); // When payment was completed (NULL if pending/failed)
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};