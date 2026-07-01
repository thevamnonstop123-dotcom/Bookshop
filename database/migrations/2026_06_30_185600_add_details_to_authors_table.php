<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('bio')->constrained('countries')->nullOnDelete();
            $table->string('website', 255)->nullable()->after('country_id');
            $table->date('joined_date')->nullable()->after('website');
            $table->unsignedInteger('sales_count')->default(0)->after('joined_date');
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id', 'website', 'joined_date', 'sales_count']);
        });
    }
};