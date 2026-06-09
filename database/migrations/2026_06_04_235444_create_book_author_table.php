<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Many-to-many relationship between books and authors.
     * One book can have multiple authors, one author can write multiple books.
     * Composite primary key on (book_id, author_id) prevents duplicate entries.
     * CASCADE: Deleting a book or author removes the link record.
     */
    public function up(): void
    {
        Schema::create('book_author', function (Blueprint $table) {
            // Composite primary key — same book-author pair cannot appear twice
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->cascadeOnDelete(); // If book is deleted, remove all its author links
            
            $table->foreignId('author_id')
                  ->constrained('authors')
                  ->cascadeOnDelete(); // If author is deleted, remove all their book links
            
            $table->primary(['book_id', 'author_id']); // Composite key = UNIQUE(book_id, author_id)
        });
        // Note: No timestamps needed for pure pivot tables
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_author');
    }
};