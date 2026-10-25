<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Riwayat status ikut terhapus bersama ebook-nya (fitur hapus ebook).
   */
  public function up(): void
  {
    Schema::table('ebook_status_history', function (Blueprint $table) {
      $table->dropForeign(['ebook_id']);
      $table->foreign('ebook_id')->references('id')->on('ebooks')->cascadeOnDelete();
    });
  }

  public function down(): void
  {
    Schema::table('ebook_status_history', function (Blueprint $table) {
      $table->dropForeign(['ebook_id']);
      $table->foreign('ebook_id')->references('id')->on('ebooks')->restrictOnDelete();
    });
  }
};
