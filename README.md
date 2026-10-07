# Ebook Storage & To-Do List

Sistem koleksi ebook pribadi yang terpusat. Pengguna menyimpan file PDF ke satu koleksi, menandai setiap PDF sebagai sudah atau belum dibaca, dan mengakses koleksi yang sama dari **Android** maupun **Web**.

> **Status:** dalam pengembangan oleh satu developer. Target **Core MVP Functional: 7 Oktober 2026**, target **Full MVP: 12 Oktober 2026**.

---

## Daftar Isi

1. [Masalah yang Diselesaikan](#1-masalah-yang-diselesaikan)
2. [Fitur](#2-fitur)
3. [Arsitektur](#3-arsitektur)
4. [Teknologi](#4-teknologi)
5. [Database](#5-database)
6. [REST API](#6-rest-api)
7. [Route Web](#7-route-web)
8. [Menjalankan Backend dan Web (Laravel)](#8-menjalankan-backend-dan-web-laravel)
9. [Menjalankan Android](#9-menjalankan-android)
10. [Pengujian](#10-pengujian)
11. [Batasan dan Di Luar Cakupan](#11-batasan-dan-di-luar-cakupan)
12. [Dokumen Perancangan](#12-dokumen-perancangan)

---

## 1. Masalah yang Diselesaikan

Ebook sering tersebar di berbagai lokasi penyimpanan perangkat. Akibatnya pengguna lupa ebook apa yang dimiliki, sulit mengetahui mana yang sudah dibaca, dan koleksi di satu perangkat tidak otomatis tersedia di perangkat lain.

Sistem ini menyediakan **satu koleksi PDF terpusat** yang dapat dibuka dari Android dan Web, lengkap dengan status baca.

Prinsip produk: aplikasi harus mengurangi pekerjaan pengguna, bukan memindahkan pekerjaan tersebut ke tempat lain.

---

## 2. Fitur

Fase 1 hanya mendukung format **PDF**. Sistem tidak memiliki pembaca PDF sendiri; PDF dibuka lewat aplikasi PDF di Android atau penampil PDF bawaan browser di Web.

| Prioritas | Fitur |
| --------- | ----- |
| **P0 (wajib)** | Register, login, logout; upload PDF saat online; daftar koleksi; buka PDF; ubah status baca (sudah/belum dibaca); penyimpanan file di Cloudflare R2; pengecekan kepemilikan (user hanya mengakses ebook miliknya) |
| **P1 (platform)** | Web Laravel Blade dan Android Kotlin yang memakai koleksi yang sama; status baca tersinkron antar platform |
| **P2 (pengurang langkah manual)** | Local Discovery di Android (menemukan PDF di perangkat) dan pencegahan duplikat otomatis dari sisi Android. Boleh ditunda; pemilih file manual tetap menjadi jalur utama bila discovery tidak stabil |

Perilaku penting:

- **Upload membutuhkan internet.** Bila offline, upload dibatalkan dan pengguna diminta mencoba lagi. Tidak ada antrean upload.
- **PDF duplikat ditolak.** Dua PDF dengan isi yang sama dalam satu akun dideteksi lewat hash SHA-256 isi file dan dijawab dengan kode 409. Dua akun berbeda tetap boleh menyimpan PDF yang sama.
- **Android bersifat local-first.** PDF yang sudah diunduh ke penyimpanan aplikasi dibuka langsung tanpa internet; bila belum ada, PDF diunduh dari server lebih dulu.
- **Batas ukuran file: 20 MB.**

---

## 3. Arsitektur

Laravel adalah pusat logika bisnis dan akses data. Android dan Web tidak pernah mengakses MySQL atau Cloudflare R2 secara langsung.

```text
┌────────────────┐   REST API    ┌──────────────┐   Route Blade   ┌────────────────┐
│ Android Kotlin │──────────────►│   Laravel    │◄────────────────│  Web (Blade)   │
└────────────────┘               │   Backend    │                 └────────────────┘
                                 └──────┬───────┘
                                        │
                          ┌─────────────┴─────────────┐
                          ▼                           ▼
                   ┌─────────────┐            ┌───────────────┐
                   │    MySQL    │            │ Cloudflare R2 │
                   │  Metadata   │            │  File PDF     │
                   │ Status baca │            │ (bucket privat)│
                   └─────────────┘            └───────────────┘
```

- **MySQL** menyimpan akun, metadata ebook, status baca, dan riwayat perubahan status.
- **Cloudflare R2** menyimpan file PDF pada bucket privat. File diakses hanya melalui Laravel.
- **Web tidak memakai `/api/*`.** Web dan API memanggil kelas layanan yang sama, `EbookService`, sehingga logika bisnis tidak ditulis dua kali.

```text
Android ──► ApiEbookController ──┐
                                 ├──► EbookService ──► MySQL + R2
Web ──────► WebEbookController ──┘
```

`EbookService` memegang tiga operasi: `store` (hash file, cek duplikat, simpan file, simpan baris ke database), `setReadStatus` (ubah status baca lewat stored procedure), dan `streamFile` (cek kepemilikan lalu kirim PDF).

---

## 4. Teknologi

| Bagian | Teknologi |
| ------ | --------- |
| Backend dan Web | Laravel, Blade (CSS biasa, tanpa Tailwind, Vite, atau Node.js) |
| Autentikasi Web | Sesi Laravel (dibuat sendiri, tanpa Breeze) |
| Autentikasi API | Laravel Sanctum (token Bearer) |
| Database | MySQL |
| Penyimpanan file | Cloudflare R2 (kompatibel S3, lewat `league/flysystem-aws-s3-v3`) |
| Android | Kotlin, ViewBinding, Retrofit, Gson, OkHttp, RecyclerView |
| Uji API | Postman |

---

## 5. Database

Tiga tabel:

```text
users (1) ──── (N) ebooks (1) ──── (N) ebook_status_history
```

| Tabel | Kolom |
| ----- | ----- |
| `users` | `id`, `name` (maks 100), `email` (unik), `password` (hash), `created_at`, `updated_at` |
| `ebooks` | `id`, `user_id` (FK), `title`, `file_path` (kunci objek di R2), `file_hash` (CHAR 64, SHA-256), `is_read` (bawaan false), `created_at`, `updated_at` |
| `ebook_status_history` | `id`, `ebook_id` (FK), `old_status`, `new_status`, `changed_at` |

Constraint penting: `UNIQUE (user_id, file_hash)` pada `ebooks` (pengaman terakhir terhadap duplikat, termasuk dua upload yang masuk bersamaan) dan index pada `user_id`.

### Pemrograman database MySQL

| Objek | Fungsi |
| ----- | ------ |
| `sp_mark_ebook_read(user_id, ebook_id)` dan `sp_mark_ebook_unread(user_id, ebook_id)` | Stored procedure yang memastikan ebook milik user, lalu mengubah `is_read` dan `updated_at = NOW()` |
| Trigger `AFTER UPDATE` pada `ebooks` | Menambah satu baris ke `ebook_status_history` hanya bila `OLD.is_read <> NEW.is_read` |
| `fn_count_read_ebooks(user_id)` | Stored function yang mengembalikan jumlah ebook yang sudah dibaca milik user |
| `COMMIT` dan `ROLLBACK` | Dipakai pada ubah status baca dan pada penyimpanan metadata saat upload |

Catatan: procedure tidak menulis riwayat; itu tugas trigger. Procedure harus mengisi `updated_at` sendiri karena perubahan lewat procedure tidak melewati Eloquent.

### Transaksi dan Cloudflare R2

`ROLLBACK` MySQL tidak membatalkan file yang sudah terkirim ke R2. Karena itu, bila penyimpanan baris `ebooks` gagal setelah file tersimpan, Laravel menghapus file tersebut sebagai langkah pembalik (*compensating action*). Bila penghapusan itu sendiri gagal, `file_path` dicatat ke log agar dapat dibersihkan manual.

Urutan upload:

```text
1. Validasi user dan file
2. Hitung SHA-256 file
3. Cek duplikat (user_id + file_hash)  ──► ada: 409, berhenti
4. Simpan file ke R2 (kunci: ebooks/user-{user_id}/{uuid}.pdf)
5. START TRANSACTION → INSERT ebooks
6. Berhasil ──► COMMIT ──► 201
   Gagal    ──► ROLLBACK ──► hapus file di R2 ──► 500
```

---

## 6. REST API

REST API dipakai oleh Android. Tujuh endpoint:

| Method | Path | Auth | Fungsi |
| ------ | ---- | ---- | ------ |
| POST | `/api/register` | Tidak | Membuat akun (201, tanpa token) |
| POST | `/api/login` | Tidak | Masuk; mengembalikan token Bearer |
| POST | `/api/logout` | Ya | Mencabut token yang dipakai |
| GET | `/api/ebooks` | Ya | Daftar ebook milik user, urut `created_at` menurun, beserta `meta.total` dan `meta.read_count` |
| POST | `/api/ebooks` | Ya | Upload PDF (`multipart/form-data`: `file`, `title` opsional) |
| GET | `/api/ebooks/{id}/file` | Ya | Mengunduh PDF milik user |
| PATCH | `/api/ebooks/{id}` | Ya | Mengubah status baca dengan body `{"is_read": true}` |

Setiap request wajib menyertakan header `Accept: application/json`; endpoint yang membutuhkan autentikasi juga menyertakan `Authorization: Bearer {token}`.

Format sukses: `{ "data": ... }`. Format error: `{ "message": "...", "code": "kode_mesin" }`, kecuali error validasi 422 yang memakai format standar Laravel.

| Kode | Arti | `code` |
| ---- | ---- | ------ |
| 401 | Token tidak valid, atau login gagal | `unauthenticated` / `invalid_credentials` |
| 404 | Ebook tidak ada atau milik user lain | `ebook_not_found` |
| 404 | Record ada, tetapi file tidak ditemukan di storage | `file_missing` |
| 409 | PDF duplikat dalam koleksi user (body menyertakan `data.existing_id`) | `duplicate_ebook` |
| 413 | Ditolak web server karena melebihi batas ukuran body | - |
| 422 | Validasi gagal (bukan PDF, lebih dari 20 MB, isian salah) | - |
| 429 | Terlalu banyak percobaan login (batas 5 per menit) | `too_many_attempts` |
| 500 | Kegagalan server (MySQL atau R2) | `server_error` |

Ebook milik user lain selalu dijawab sama seperti ebook yang tidak ada (404), agar keberadaannya tidak bocor.

`PATCH` bersifat idempotent: bila status sudah sama dengan yang diminta, procedure tidak dipanggil dan respons 200 berisi keadaan terkini.

Spesifikasi lengkap ada pada `04-API-Specification.md`.

---

## 7. Route Web

Web memakai sesi Laravel, bukan token.

| Method | Path | Fungsi |
| ------ | ---- | ------ |
| GET, POST | `/register`, `/login` | Daftar dan masuk |
| POST | `/logout` | Keluar |
| GET | `/ebooks` | Daftar koleksi |
| POST | `/ebooks` | Upload PDF |
| GET | `/ebooks/{id}/file` | Membuka PDF di penampil bawaan browser |
| PATCH | `/ebooks/{id}` | Mengubah status baca |

---

## 8. Menjalankan Backend dan Web (Laravel)

### Prasyarat

- PHP (versi yang didukung Laravel yang dipakai, minimal Laravel 11 untuk `php artisan install:api`)
- Composer
- MySQL
- Akun Cloudflare R2 (untuk penyimpanan akhir; pengembangan awal dapat memakai disk lokal)

Node.js **tidak** diperlukan.

### Langkah

1. Pasang dependensi:

   ```bash
   composer install
   ```

2. Salin file pengaturan dan buat kunci aplikasi:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Buat database MySQL kosong, lalu isi koneksinya di `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4. Pastikan tiga pengaturan berikut ada di `.env` agar sesi, cache, dan antrean tidak membutuhkan tabel di database:

   ```env
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```

5. Tentukan disk penyimpanan ebook. Untuk pengembangan lokal:

   ```env
   EBOOK_DISK=local
   ```

   Untuk Cloudflare R2, ubah menjadi `EBOOK_DISK=r2` dan isi pengaturan disk `r2` (endpoint, access key, secret key, nama bucket; region `auto`) sesuai akun R2 Anda. Bucket **harus privat**. Bila koneksi gagal, sesuaikan opsi *path style* pada disk `r2` di `config/filesystems.php`.

6. Jalankan migration. Migration ini juga membuat stored procedure, trigger, dan stored function:

   ```bash
   php artisan migrate
   ```

7. Atur batas upload pada `php.ini`, lalu jalankan ulang server:

   ```ini
   upload_max_filesize = 20M
   post_max_size       = 25M
   ```

   Bila memakai Nginx, tambahkan juga `client_max_body_size 25m;`.

8. Jalankan server:

   ```bash
   php artisan serve
   ```

   Agar dapat diakses dari HP fisik pada jaringan Wi-Fi yang sama:

   ```bash
   php artisan serve --host=0.0.0.0
   ```

   Web dapat dibuka di `http://127.0.0.1:8000`.

---

## 9. Menjalankan Android

### Prasyarat

- Android Studio
- Emulator atau HP fisik dengan aplikasi penampil PDF terpasang
- Server Laravel sedang berjalan

### Menghubungkan ke backend

| Perangkat | Alamat server yang dipakai |
| --------- | -------------------------- |
| Emulator Android | `http://10.0.2.2:8000` |
| HP fisik | `http://{IP komputer di jaringan lokal}:8000`; HP dan komputer harus berada pada Wi-Fi yang sama, dan server dijalankan dengan `--host=0.0.0.0` |

Izin HTTP tanpa enkripsi (cleartext) hanya diaktifkan untuk pengembangan. Alamat dasar API (*base URL*) diatur pada konfigurasi klien API di proyek Android.

### Perilaku aplikasi

- Token login disimpan di `SharedPreferences` privat. Ini belum terenkripsi dan dicatat sebagai utang teknis; enkripsi penyimpanan token menjadi perbaikan setelah MVP.
- PDF yang diunduh disimpan di penyimpanan privat aplikasi dan dipakai ulang selama masih ada. Penyimpanan ini dihapus saat logout.
- Upload dari Android dibatasi 20 MB dan ditolak lebih awal bila perangkat offline.

---

## 10. Pengujian

- **Uji API:** Postman collection berisi 7 endpoint dengan variabel `base_url` dan `token`. Matriks uji berisi 18 skenario, di antaranya register dan login, throttle login, upload valid, duplikat (409), bukan PDF (422), file lebih dari 20 MB, ubah status dengan nilai berbeda dan sama, akses ebook milik user lain (404), serta pemakaian token setelah logout (401).
- **Uji lintas platform:** upload dari satu platform terlihat di platform lain, status baca tersinkron, duplikat ditolak lintas platform, dua akun tidak dapat melihat ebook satu sama lain, dan Android offline menolak upload tetapi tetap membuka PDF yang sudah diunduh.
- **Uji database:** procedure mengubah status dan membuat tepat satu baris riwayat; pemanggilan dengan nilai sama tidak menambah riwayat; pemanggilan pada ebook user lain menghasilkan error; `ROLLBACK` membatalkan perubahan status dan riwayat sekaligus.
- **Uji end-to-end:** Register, Login, Upload, Buka, Read/Unread, Logout, dijalankan di Web dan di Android.

---

## 11. Batasan dan Di Luar Cakupan

Batasan Fase 1:

- Hanya format PDF.
- Upload membutuhkan koneksi internet; tidak ada antrean upload atau sinkronisasi latar belakang.
- Pembacaan offline di Android hanya berlaku untuk PDF yang sudah diunduh ke penyimpanan aplikasi.
- Daftar ebook tidak memakai pagination, dan token API tidak memiliki masa kedaluwarsa khusus.
- Unduhan file tidak mendukung range request; Android mengunduh file secara utuh.

Di luar cakupan MVP: format selain PDF, pembaca PDF internal, hapus ebook, ubah judul, pencarian, kategori, tag, bookmark, progres baca, rating, statistik kompleks, berbagi ebook, rekomendasi, fitur sosial, dashboard admin, dan pemindahan otomatis setiap PDF yang terdeteksi.

---

## 12. Dokumen Perancangan

Perancangan lengkap berada pada enam dokumen. Bila isi dua dokumen berbeda, dokumen 00 adalah acuan tertinggi untuk scope, prioritas, milestone, dan keputusan arsitektur.

| Dokumen | Isi |
| ------- | --- |
| `00-Project-Overview.md` | Latar belakang, solusi, arsitektur, prioritas P0/P1/P2, target milestone, kriteria keberhasilan |
| `01-Use-Case.md` | Sepuluh use case (register hingga ubah status baca) beserta alur utama dan alur alternatif |
| `02-User-Flow.md` | Alur perjalanan pengguna di Android dan Web |
| `03-Rancangan-Database-Ebook-Storage.md` | Skema tiga tabel, stored procedure, trigger, function, serta aturan transaksi |
| `04-API-Specification.md` | Kontrak lengkap tujuh endpoint REST API |
| `05-Backlog-dan-Jadwal-Chunking.md` | Daftar pekerjaan C01 sampai C26, jadwal, gate pengujian, dan log perubahan rancangan |

### Milestone

| Tanggal | Milestone |
| ------- | --------- |
| 7 Oktober 2026 | **Core MVP Functional:** alur inti berjalan end-to-end (autentikasi, upload, daftar, buka PDF, ubah status, tujuh endpoint API teruji) dan Web berfungsi; Android login ditargetkan secara lunak |
| 12 Oktober 2026 | **Full MVP:** Android dan Web memakai koleksi yang sama, file tersimpan di Cloudflare R2, kepemilikan terjamin, duplikat ditolak, dan pengujian end-to-end selesai |
