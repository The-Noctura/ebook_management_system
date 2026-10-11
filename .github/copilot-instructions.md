# Aturan untuk Copilot

- Jangan mengubah `vendor/`, `bootstrap/cache/`, `artisan`, `public/index.php`, dan `composer.lock` secara manual.
- Jangan menambah logika ke `bootstrap/app.php`. Letakkan logika baru di kelas khusus di `app/` dan panggil dari sana.
- Ikuti `.editorconfig` (indentasi 4 spasi). Jalankan `php vendor/bin/pint` sebelum commit.
- Jangan mengubah file bawaan Laravel (`config/`, `AppServiceProvider.php`, `TestCase.php`) kecuali diminta secara eksplisit.
