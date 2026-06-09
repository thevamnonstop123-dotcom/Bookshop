<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_emails', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('message');
            $table->foreignId('sent_by')->constrained('staff')->onDelete('cascade');
            $table->integer('recipients_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_emails');
    }
};