<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Book categories for organizing products (e.g., Fiction, Science, History).
     * Uses soft deletes so old book-category links remain intact.
     * created_by / updated_by track which staff member made changes (SET NULL on staff delete).
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            $table->string('name', 100)->unique(); // Category name, must be unique (e.g., 'Fiction')
            $table->text('description');            // Category description for display
            
            $table->enum('status', ['active', 'inactive'])->default('active'); // Hide inactive categories from customers
            
            // Audit trail — which staff created/updated this category
            // SET NULL: If staff member is deleted, we keep the category but lose the reference
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();
            
            $table->timestamps();        // created_at, updated_at
            $table->softDeletes();       // deleted_at — categories can be soft-deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};