<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Store admin/staff accounts who manage the system.
     * Each staff belongs to one role that determines their permissions.
     * Staff members are NOT customers — they are internal employees.
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // Foreign key to roles table — RESTRICT prevents deleting a role that has staff assigned
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->restrictOnDelete(); // Cannot delete role while staff members still use it
            
            $table->string('name', 100);               // Staff full name
            $table->string('email', 100)->unique();     // Login email, must be unique
            $table->string('phone', 20)->unique();      // Contact phone, must be unique
            $table->text('address');                    // Physical address
            $table->enum('gender', ['male', 'female']); // Gender selection
            $table->date('dob');                        // Date of birth
            $table->string('password', 255);            // Hashed password (bcrypt)
            $table->string('image', 255);               // Profile photo path (stored in storage/app/public/staff)
            
            // Account status — inactive staff cannot log in
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};