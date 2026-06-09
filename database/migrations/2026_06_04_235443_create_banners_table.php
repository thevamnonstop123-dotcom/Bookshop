<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Homepage/slider banners for promotions and announcements.
     * display_order controls the order banners appear on the customer frontend.
     * start_date/end_date allows scheduling banners for specific periods.
     * Soft deletable for easy recovery.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            $table->string('title', 100);        // Banner title/heading
            $table->text('description');          // Banner description text
            $table->string('image', 255);         // Banner image path (storage/app/public/banners)
            $table->integer('display_order')->default(0); // Sort order for displaying multiple banners
            $table->date('start_date');           // Banner starts showing from this date
            $table->date('end_date');             // Banner stops showing after this date
            
            $table->enum('status', ['active', 'inactive'])->default('active'); // Enable/disable banner manually
            
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
            $table->softDeletes();       // deleted_at — banners can be soft-deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};