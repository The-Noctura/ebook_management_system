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
    Schema::table('ebooks', function (Blueprint $table) {
      $table->index('user_id', 'ebooks_user_id_index');
      $table->unique(['user_id', 'file_hash'], 'ebooks_user_id_file_hash_unique');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('ebooks', function (Blueprint $table) {
      $table->dropUnique('ebooks_user_id_file_hash_unique');
      $table->dropIndex('ebooks_user_id_index');
    });
  }
};
