# Panduan Deploy ke InfinityFree

Panduan ini untuk menjalankan EMS (Ebook Management System) di hosting gratis InfinityFree, yang **menonaktifkan stored procedure, trigger, dan foreign key** serta membatasi **file maksimal 10 MB** dan **tidak punya SSH, `composer`, atau `artisan`**. Kode yang sama dipakai untuk Railway dan lokal; bedanya hanya isi `.env`.

Status: perubahan kode ini **belum pernah dijalankan** (tidak ada PHP di tempat perubahan dibuat). Jalankan `php artisan test` dulu (langkah A4) sebelum mengunggah.

---

## 0. Cara kerjanya

Satu sakelar di `.env` menentukan di mana logika database dijalankan.

| Pengaturan `.env` | Nilai bawaan | Di InfinityFree | Pengaruh |
|---|---|---|---|
| `EBOOK_SQL_OBJECTS` | `true` | `false` | `true`: ubah status baca lewat procedure `sp_mark_ebook_read`/`sp_mark_ebook_unread`, riwayat lewat trigger, hitung lewat function `fn_count_read_ebooks` (dokumen 03 bagian 7). `false`: pekerjaan yang sama dilakukan di PHP (`EbookService`) dalam satu transaksi `DB::transaction` |
| `EBOOK_MAX_UPLOAD_KB` | `20480` (20 MB) | `9216` (9 MB) | Aturan validasi `max`, teks di layar (form upload, Empty State, landing), pesan error, dan pemeriksaan di JavaScript ikut berubah otomatis |

Bila `EBOOK_SQL_OBJECTS=false`:

- Riwayat status dicatat oleh kode, bukan trigger, dan dihapus oleh kode saat ebook dihapus (tanpa foreign key `ON DELETE CASCADE`).
- Kepemilikan ebook tetap dijaga kode (ebook milik user lain dijawab 404, dokumen 04 bagian 3.6).
- Index unik `(user_id, file_hash)` tetap dipakai sebagai pengaman duplikat. Itu bukan foreign key, jadi seharusnya diizinkan (belum terbukti sampai skema diimpor).

---

## A. Persiapan di komputer sendiri

### A1. Buat cabang Git

```bash
git checkout -b infinityfree
```

Salin isi paket perubahan ini ke root proyek (timpa file yang sama), lalu commit.

### A2. Pastikan versi PHP cocok (penting)

`composer.lock` proyek saat ini mengunci paket Symfony 8.1 yang **membutuhkan PHP 8.4.1 ke atas**, sedangkan `laravel/framework` sendiri hanya meminta PHP 8.3. Sumber yang saya baca menyebut InfinityFree menyediakan **PHP 8.3** (per April 2026). Jadi:

1. Unggah `phpinfo.php` berisi `<?php phpinfo();` ke htdocs, buka di browser, catat versi PHP, lalu **hapus file itu**.
2. Bila PHP di InfinityFree **8.4.1 atau lebih**: lewati langkah ini.
3. Bila PHP-nya **8.3**: jalankan di cabang `infinityfree`:

```bash
composer config platform.php 8.3.0
composer update
```

Composer akan memilih versi paket yang cocok dengan PHP 8.3 (Symfony 7.x). Bila Composer menolak karena Laravel 13 tidak bisa memakai paket itu, versi Laravel ini tidak bisa berjalan di InfinityFree. Pada kasus itu pakai Railway, jangan memaksa.

Catatan: `config/database.php` sudah diperbaiki agar tidak memakai kelas `Pdo\Mysql` yang baru ada di PHP 8.4.

### A3. Uji mode tanpa objek SQL di lokal

Di `.env` lokal (MySQL lokal Anda) tambahkan dua baris, jalankan aplikasi, lalu coba ubah status baca, hapus ebook, dan unggah file:

```env
EBOOK_SQL_OBJECTS=false
EBOOK_MAX_UPLOAD_KB=9216
```

Ini membuktikan logika PHP bekerja sebelum menyentuh hosting. Hapus kedua baris itu bila ingin kembali ke mode procedure.

### A4. Jalankan test

```bash
php artisan test
```

`tests/Feature/EbookServiceNoSqlObjectsTest.php` (baru) menguji mode tanpa objek SQL. Test lain tidak berubah karena nilai bawaan `EBOOK_SQL_OBJECTS` adalah `true`.

### A5. Bangun untuk produksi

```bash
php artisan config:clear
composer install --no-dev --optimize-autoloader
npm run build
```

`npm run build` wajib diulang karena `resources/js/app.js` berubah (batas ukuran file kini dibaca dari form).

Lalu susun folder unggahan (Git Bash atau WSL di Windows):

```bash
bash deploy/infinityfree/assemble.sh
```

Hasilnya `deploy_out/htdocs`. Skrip ini menyalin hanya yang dibutuhkan server dan **sengaja tidak membawa** `tests`, `docs`, `node_modules`, `.git`, `database.sqlite`, file `uji_*.php`, PDF uji di `storage/app/private/ebooks`, serta cache `bootstrap/cache/*.php` dari komputer dev (cache itu menyebut paket dev dan jalur lokal, dan bisa membuat aplikasi error di server). Skrip juga memperingatkan bila ada file di atas 9 MB.

### A6. Isi `.env` produksi

Buka `deploy_out/htdocs/app_core/.env` (berasal dari `deploy/infinityfree/env.infinityfree.example`) dan isi semua nilai bertanda `ISI`:

- `APP_KEY`: jalankan `php artisan key:generate --show` di komputer sendiri, tempel hasilnya.
- `APP_URL`: alamat situs, berawalan `https`.
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: dari panel InfinityFree bagian MySQL Databases. **Host bukan `localhost`**, melainkan nama seperti `sqlXXX.infinityfree.com`.

---

## B. Database

1. Di panel InfinityFree buat satu database MySQL.
2. Buka phpMyAdmin, pilih database itu, tab **Import**, unggah `database/infinityfree/schema.sql`.
3. Pastikan empat tabel terbentuk: `users`, `ebooks`, `ebook_status_history`, `personal_access_tokens`.

Jika impor gagal, catat pesan errornya persis. Itu petunjuk terpenting (misalnya engine InnoDB tidak ada, atau index unik ditolak).

---

## C. Unggah lewat FTP

Pakai FileZilla. Aktifkan "Tampilkan file tersembunyi" supaya `.htaccess` dan `.env` ikut terlihat.

Unggah **isi** `deploy_out/htdocs` ke folder `htdocs` di server. Hasilnya di server:

```text
htdocs/
├── index.php          (dari deploy/infinityfree/index.php)
├── .htaccess          (aturan rewrite + penguncian app_core)
├── build/             (CSS dan JS hasil npm run build)
├── favicon.ico, robots.txt
└── app_core/          (seluruh aplikasi Laravel, dikunci)
    ├── .env, .htaccess
    ├── app, bootstrap, config, resources/views, routes, vendor, storage
    └── artisan, composer.json
```

- Satu file maksimal 10 MB. Forum InfinityFree menyatakan arsip tidak bisa diunggah lalu diekstrak di server, jadi semua file diunggah satu per satu oleh FileZilla. `vendor` berisi ribuan file, jadi ini bisa memakan waktu. Berapa batas jumlah file di akun gratis belum saya ketahui; bila unggahan berhenti, catat pesannya.
- Pastikan `app_core/storage` (dan isinya) bisa ditulis oleh PHP. Biasanya sudah bisa; bila muncul error izin, atur ke `755` atau `775` lewat FileZilla.

---

## D. Uji di alamat publik

1. Buka `/`: harus tampil landing page.
2. Buka `/app_core/.env` dan `/app_core/composer.json`: **harus ditolak (403 atau 404)**. Bila isinya terbaca, hentikan dan hapus situs segera, karena `.env` berisi kunci dan kata sandi.
3. Daftar akun, login.
4. Unggah PDF kecil (di bawah 9 MB). Unggah file yang sama lagi: harus ditolak sebagai duplikat.
5. Buka PDF (harus terbuka di penampil browser).
6. Tandai sudah dibaca lalu belum dibaca. Angka ringkasan di bilah atas harus ikut berubah.
7. Cek di phpMyAdmin: tabel `ebook_status_history` bertambah satu baris tiap perubahan status.
8. Hapus ebook: barisnya hilang dari `ebooks` dan riwayatnya hilang dari `ebook_status_history`.
9. Unggah PDF di atas 9 MB: harus muncul pesan "Ukuran file maksimal 9 MB."

---

## E. Bila ada masalah

| Gejala | Kemungkinan penyebab dan langkah |
|---|---|
| Halaman 500 polos | Lihat `app_core/storage/logs/laravel.log` lewat FTP. Sementara waktu boleh `APP_DEBUG=true` untuk melihat pesan, lalu kembalikan ke `false` |
| Error `Class "Pdo\Mysql" not found` | `config/database.php` lama masih terunggah. Pastikan versi dari paket ini |
| Error terkait versi PHP atau paket Symfony | Langkah A2: versi PHP server lebih rendah dari yang dikunci `composer.lock` |
| Halaman tampil tanpa gaya | Folder `build/` tidak ada di `htdocs`, atau `index.php` tidak memakai `usePublicPath` |
| `No application encryption key` | `APP_KEY` di `.env` kosong |
| `SQLSTATE ... Connection refused` | `DB_HOST` masih `localhost` atau salah |
| Error saat impor `schema.sql` | Catat pesan errornya persis dan kirim ke saya |
| 419 Page Expired saat unggah | File melebihi `post_max_size` server sehingga token CSRF ikut terbuang (wireframe Upload bagian 6.4). Turunkan `EBOOK_MAX_UPLOAD_KB`, misalnya `5120` |
| Redirect berulang atau tautan `http://` | Proxy hosting tidak terbaca Laravel. Tambahkan `$middleware->trustProxies(at: '*');` di `bootstrap/app.php`, bagian `withMiddleware` |
| Login sering minta ulang | `storage/framework/sessions` tidak bisa ditulis |

---

## F. Catatan keputusan

1. **Dokumen 00 bagian 13.1** mewajibkan procedure, trigger, dan function di MySQL. Versi InfinityFree menggantinya dengan logika PHP. Kode untuk mode procedure **tetap utuh** dan aktif bila `EBOOK_SQL_OBJECTS=true`, jadi versi yang dinilai terhadap persyaratan itu tidak hilang. Catat penyimpangan ini di Log Perubahan Rancangan (dokumen 05 bagian 9).
2. **Batas ukuran file** di versi InfinityFree adalah 9 MB, bukan 20 MB (dokumen 04 bagian 5.5), karena InfinityFree menolak file di atas 10 MB.
3. **Syarat penggunaan InfinityFree** soal menyimpan file milik pengunjung belum saya periksa. Baca sebelum mengundang banyak pengguna.
4. **Android dan API** tidak ikut diuji di InfinityFree. Aplikasi Android belum ada; endpoint `/api/*` tetap ada di kode tetapi bukan target deploy ini.
