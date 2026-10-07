<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ebook_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ebook_id')->constrained()->restrictOnDelete();
            $table->boolean('old_status');
            $table->boolean('new_status');
            $table->timestamp('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebook_status_history');
    }
};
