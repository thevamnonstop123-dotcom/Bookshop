<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('availability_status', [
                'in_stock',
                'low_stock',
                'out_of_stock',
                'coming_soon',
                'pre_order',
                'discontinued'
            ])->default('in_stock')->after('stock_quantity');
            $table->index('availability_status');
        });

        // Migrate existing data based on stock_quantity
        DB::statement("
            UPDATE books SET availability_status = 
                CASE
                    WHEN stock_quantity > 10 THEN 'in_stock'
                    WHEN stock_quantity BETWEEN 1 AND 10 THEN 'low_stock'
                    WHEN stock_quantity <= 0 THEN 'out_of_stock'
                    ELSE 'in_stock'
                END
            WHERE availability_status = 'in_stock'
        ");
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('availability_status');
        });
    }
};
