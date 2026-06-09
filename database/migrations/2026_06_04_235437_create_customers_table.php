<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Store customer/buyer accounts.
     * Customers browse books, add to cart, and place orders.
     * Status 'banned' prevents login and ordering.
     * Note: Customers have NO role_id — they only have basic shopping access.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            $table->string('name', 100);               // Customer full name
            $table->string('email', 100)->unique();     // Login email, must be unique
            $table->string('phone', 20)->unique();      // Contact phone, must be unique
            $table->enum('gender', ['male', 'female']); // Gender selection
            $table->date('dob');                        // Date of birth
            $table->string('password', 255);            // Hashed password (bcrypt)
            $table->string('image', 255);               // Profile photo path (storage/app/public/customers)
            
            // Account status — 'banned' customers are blocked from system access
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};