<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Line items within an order — what books were purchased.
     * price is a SNAPSHOT: Stores the book price at purchase time.
     *   Even if the book price changes later, this order record stays correct.
     * UNIQUE(order_id, book_id): Same book cannot appear twice in one order.
     * RESTRICT on book delete: Cannot delete a book that exists in historical orders.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // Link to parent order — CASCADE removes items when order is deleted
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();
            
            // RESTRICT: Historical orders preserve book reference even if book later deleted
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->restrictOnDelete();
            
            $table->integer('quantity');          // Quantity of this book purchased
            $table->decimal('price', 10, 2);      // SNAPSHOT: Book price at time of purchase (preserves history)
            
            $table->timestamps(); // created_at, updated_at
            
            // Unique constraint: Cannot have duplicate book in same order
            $table->unique(['order_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};