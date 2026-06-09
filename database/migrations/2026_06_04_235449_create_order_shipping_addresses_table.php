<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Shipping address SNAPSHOT for each order.
     * At checkout, the selected customer_address is COPIED here.
     * This preserves shipping history — even if customer later changes/deletes their address,
     *   the order still shows where it was shipped.
     * UNIQUE(order_id): One shipping address per order.
     */
    public function up(): void
    {
        Schema::create('order_shipping_addresses', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // UNIQUE: One shipping address per order
            $table->foreignId('order_id')
                  ->unique()                    // Each order has exactly one shipping address
                  ->constrained('orders')
                  ->cascadeOnDelete();          // Delete address when order is deleted
            
            $table->string('receiver_name', 100); // Copied from customer_address at checkout
            $table->string('phone_number', 20);   // Copied from customer_address at checkout
            $table->text('address_line');         // Copied from customer_address at checkout
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipping_addresses');
    }
};