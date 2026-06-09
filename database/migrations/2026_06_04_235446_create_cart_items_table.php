<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Individual items inside a customer's cart.
     * UNIQUE(cart_id, book_id): A book can only appear once in a cart (update quantity instead).
     * CASCADE: Removing cart removes its items. Removing book removes it from all carts.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // Link to parent cart
            $table->foreignId('cart_id')
                  ->constrained('carts')
                  ->cascadeOnDelete(); // Delete cart item when cart is deleted
            
            // Link to book being purchased
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->cascadeOnDelete(); // Remove from carts if book is deleted
            
            $table->integer('quantity'); // Number of copies of this book in cart
            
            $table->timestamps(); // created_at, updated_at
            
            // Unique constraint: Cannot add same book twice — update quantity instead
            $table->unique(['cart_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};