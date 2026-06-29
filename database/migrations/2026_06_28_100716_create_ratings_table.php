<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('review')->nullable();
            $table->unsignedInteger('helpful_count')->default(0);
            $table->string('status')->default('active'); // active, hidden, reported
            $table->unique(['customer_id', 'book_id']);
            $table->timestamps();
        });

        // Helpful votes tracking
        Schema::create('rating_helpfuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained('ratings')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unique(['rating_id', 'customer_id']);
            $table->timestamps();
        });

        // Add computed rating to books
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'rating')) {
                $table->decimal('rating', 2, 1)->default(0)->after('price');
            }
            if (!Schema::hasColumn('books', 'rating_count')) {
                $table->unsignedInteger('rating_count')->default(0)->after('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_helpfuls');
        Schema::dropIfExists('ratings');
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_count']);
        });
    }
};