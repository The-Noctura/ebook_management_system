<?php

/*
 * Pengaturan khusus aplikasi ebook.
 *
 * Semua nilai punya bawaan yang sama dengan rancangan awal (dokumen 03 dan 04),
 * jadi tanpa mengubah .env perilaku aplikasi tidak berubah. Hosting gratis seperti
 * InfinityFree mengubahnya lewat .env (lihat docs/PANDUAN-INFINITYFREE.md).
 */

$maxKb = (int) env('EBOOK_MAX_UPLOAD_KB', 20480);

// 20480 KB -> "20 MB"; 9216 KB -> "9 MB"; 9000 KB -> "8,8 MB"
$mb = $maxKb / 1024;
$label = ($maxKb % 1024 === 0 ? (string) intdiv($maxKb, 1024) : str_replace('.', ',', rtrim(rtrim(number_format($mb, 1, '.', ''), '0'), '.'))) . ' MB';

return [

  /*
   | true  : status baca lewat stored procedure, riwayat lewat trigger, hitung lewat
   |         function MySQL (rancangan dokumen 03 bagian 7).
   | false : logika yang sama dikerjakan di PHP (EbookService). Untuk hosting yang
   |         menonaktifkan procedure, trigger, dan foreign key.
   */
  'sql_objects' => filter_var(env('EBOOK_SQL_OBJECTS', true), FILTER_VALIDATE_BOOL),

  // Batas ukuran satu PDF dalam KB (aturan validasi `max`) dan label untuk teks di layar.
  'max_upload_kb' => $maxKb,
  'max_upload_label' => $label,

];
