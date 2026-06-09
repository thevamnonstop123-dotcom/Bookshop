<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: The core product table — stores all book listings.
     * slug is used for SEO-friendly URLs (e.g., /books/atomic-habits).
     * isbn is the unique book identifier (International Standard Book Number).
     * price is in the base currency (MMK expected based on your payment methods).
     * stock_quantity tracks available inventory.
     * RESTRICT on category delete: Cannot delete a category that still has books.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // Foreign key to categories — RESTRICT prevents deleting category with existing books
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->restrictOnDelete();
            
            $table->string('title', 100);          // Book title
            $table->string('slug', 255)->unique(); // URL-friendly title (e.g., 'atomic-habits')
            $table->string('isbn', 50)->unique();  // ISBN number, unique per book edition
            $table->decimal('price', 10, 2);       // Selling price (e.g., 15000.00)
            $table->integer('stock_quantity')->default(0); // Current stock count
            $table->string('language', 50);        // Book language (e.g., 'English', 'Myanmar')
            $table->date('published_date');        // Publication date
            $table->text('description');           // Full book description
            $table->string('image', 255);          // Book cover image path (storage/app/public/books)
            
            $table->enum('status', ['active', 'inactive'])->default('active'); // Active books are visible to customers
            
            // Audit trail
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();
            
            $table->timestamps();        // created_at, updated_at
            $table->softDeletes();       // deleted_at — books can be soft-deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};