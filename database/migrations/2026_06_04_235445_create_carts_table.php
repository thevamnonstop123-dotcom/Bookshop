<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Shopping cart — one cart per customer.
     * UNIQUE on customer_id ensures each customer has only one cart.
     * Cart items are stored in the cart_items table.
     * CASCADE: If customer is deleted, their cart is removed.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // UNIQUE: One customer = one cart
            $table->foreignId('customer_id')
                  ->unique()                    // Ensures one cart per customer
                  ->constrained('customers')
                  ->cascadeOnDelete();          // Delete cart when customer is deleted
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};