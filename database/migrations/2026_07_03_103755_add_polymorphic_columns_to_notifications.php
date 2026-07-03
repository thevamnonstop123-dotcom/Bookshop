<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Step 1: Add new columns (nullable first)
            $table->string('recipient_type')->nullable()->after('id');
            $table->unsignedBigInteger('recipient_id')->nullable()->after('recipient_type');
            $table->string('notifiable_type')->nullable()->after('message');
            $table->unsignedBigInteger('notifiable_id')->nullable()->after('notifiable_type');
            
            $table->index(['recipient_type', 'recipient_id']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });

        // Step 2: Migrate existing data
        DB::statement("
            UPDATE notifications 
            SET recipient_type = 'App\\\\Models\\\\Customer',
                recipient_id = customer_id
            WHERE customer_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['recipient_type', 'recipient_id']);
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['recipient_type', 'recipient_id', 'notifiable_type', 'notifiable_id']);
        });
    }
};
