<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Customer orders — created after successful checkout.
     * order_number is a human-readable unique identifier (e.g., 'ORD-20240001').
     * total_amount is the snapshot of the total at order time.
     * Status tracks fulfillment: pending → processing → shipped → delivered (or cancelled).
     * RESTRICT on customer delete: Cannot delete customer with existing orders (preserves history).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // RESTRICT: Orders are historical records — cannot delete customer who placed orders
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->restrictOnDelete();
            
            $table->string('order_number', 50)->unique(); // Human-readable order reference (e.g., 'ORD-20240001')
            $table->decimal('total_amount', 10, 2);       // Order total at time of placement (snapshot)
            
            // Order fulfillment status
            $table->enum('status', [
                'pending',     // Order placed, awaiting processing
                'processing',  // Being prepared for shipping
                'shipped',     // On its way to customer
                'delivered',   // Successfully received by customer
                'cancelled'    // Order cancelled (by customer or admin)
            ])->default('pending');
            
            $table->timestamps(); // created_at = order date, updated_at = last status change
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};