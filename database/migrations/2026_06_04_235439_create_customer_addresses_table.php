<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Purpose: Store multiple shipping addresses per customer.
     * One address is marked as default for faster checkout.
     * CASCADE: If a customer is deleted, all their addresses are removed.
     */
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED, auto-increment primary key
            
            // Foreign key to customers — CASCADE deletes addresses when customer is deleted
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->cascadeOnDelete();
            
            $table->string('receiver_name', 100); // Who receives the package (may differ from customer name)
            $table->string('phone_number', 20);   // Contact number for delivery
            $table->text('address_line');         // Full shipping address
            $table->boolean('is_default')->default(false); // Default address used at checkout
            
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};