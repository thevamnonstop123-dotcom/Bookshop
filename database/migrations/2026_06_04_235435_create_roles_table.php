<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Define staff roles with specific permissions.
     * Each role controls what a staff member can do in the admin panel.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            $table->string('name', 50)->unique(); // Role display name (e.g., 'Super Admin', 'Order Manager')
            
            // Permission flags — each role has specific access rights
            $table->boolean('can_manage_books')->default(false);   // Add/edit/delete books & authors
            $table->boolean('can_manage_orders')->default(false);  // View/update order statuses
            $table->boolean('can_manage_users')->default(false);   // Manage staff accounts & customers
            $table->boolean('can_view_reports')->default(false);   // Access sales reports & analytics
            
            $table->timestamps(); // created_at, updated_at
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};