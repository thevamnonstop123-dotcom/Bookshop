<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Store book authors with their bio and photo.
     * Linked to books via the book_author pivot table (many-to-many).
     * Soft deletable to preserve historical book-author relationships.
     */
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            $table->string('name', 100);   // Author full name
            $table->text('bio');           // Author biography/description
            $table->string('image', 255);  // Author photo path (storage/app/public/authors)
            
            $table->enum('status', ['active', 'inactive'])->default('active'); // Hide inactive authors from display
            
            // Audit trail — which staff created/updated this author
            // SET NULL: If staff member is deleted, we keep the author record
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('staff')
                  ->nullOnDelete();
            
            $table->timestamps();        // created_at, updated_at
            $table->softDeletes();       // deleted_at — authors can be soft-deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};