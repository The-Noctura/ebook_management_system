# Backlog & Jadwal (Versi 3)

## Ebook Storage & To-Do List

| Item                      | Detail                                                                                                    |
| ------------------------- | --------------------------------------------------------------------------------------------------------- |
| Versi                     | 3 (menggantikan versi 2)                                                                                  |
| Tanggal disusun           | 5 Oktober 2026                                                                                            |
| Acuan                     | 00-Project-Overview, 01-Use-Case, 02-User-Flow, 03-Rancangan-Database-Ebook-Storage, 04-API-Specification |
| Milestone (tidak berubah) | Core MVP Functional 7 Oktober 2026; Full MVP 12 Oktober 2026                                              |
| Jam kerja                 | 16.00 sampai 23.00 atau 00.00 (6-8 jam per hari)                                                          |

---

## 1. Cara Membaca Dokumen Ini

Dokumen ini adalah **daftar hal yang harus ada**, bukan daftar perintah. Setiap pekerjaan berisi apa yang perlu dibuat, dikelompokkan per topik. Cara mengerjakannya, urutan di dalam pekerjaan, dan lamanya diserahkan kepada Anda.

- **Kode pekerjaan** (C01 sampai C26) hanyalah nama pekerjaan. Kode ini juga dipakai pada Log Perubahan Rancangan (bagian 9).
- **Tempat kerja.** Setiap pekerjaan punya baris **Dikerjakan di**, dan setiap butir diawali tanda dalam kurung siku, misalnya **[Laravel API]**, yang menyebut butir itu dikerjakan di mana. Bila suatu pekerjaan tidak memuat tanda **[Android]**, pekerjaan itu tidak menyentuh Android. Bila tidak memuat tanda Laravel, pekerjaan itu tidak mengubah Laravel. Arti setiap tanda ada di tabel di bawah.
- **Membutuhkan** menyebut pekerjaan yang harus selesai lebih dulu.
- **Selesai jika** adalah satu-satunya tolok ukur selesai.
- **Kotak centang** (`[ ]` belum, `[x]` sudah) ada di setiap butir pekerjaan dan di daftar status pada awal bagian 4. Centang butir saat selesai; centang kotak **Selesai jika** hanya setelah syaratnya terbukti, lalu centang pekerjaan itu di daftar status.
- Setiap rujukan "dokumen X bagian Y" menunjuk bagian dokumen perancangan yang menjadi sumber detailnya. Bila ada pertentangan, dokumen 00 (Project Overview) dan 01 (Use Case) menjadi acuan utama, dan dokumen 05 yang disesuaikan. Bila saat membangun ditemukan hal yang bertentangan dengan dokumen 00-04, perbarui dokumen terkait lalu catat di bagian 9.
- Ide atau bug baru yang muncul di tengah jalan dicatat di Parkir (bagian 8), tidak dikerjakan saat itu.

### Tanda tempat kerja

Empat tanda pertama adalah tempat **menulis kode**. Tanda sisanya adalah alat atau tempat untuk **menjalankan, menguji, atau mengatur**.

| Tanda               | Artinya                                                                                                                                                                                     |
| ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Laravel Backend** | Bagian proyek Laravel yang dipakai bersama oleh Web dan API: file `.env`, migration (file pembuat tabel), model, `EbookService`, dan objek MySQL (procedure, trigger, function). Tidak punya halaman dan tidak punya alamat `/api`. |
| **Laravel API**     | Bagian proyek Laravel yang melayani Android dan Postman lewat alamat `/api/...` dengan jawaban JSON dan token Sanctum: route API, controller API, `EbookResource`.                           |
| **Laravel Web**     | Bagian proyek Laravel yang melayani browser: route web, controller web, view Blade (file tampilan), dan sesi login.                                                                         |
| **Android**         | Proyek Kotlin di Android Studio. Tidak ada kode Laravel di sini.                                                                                                                            |
| Terminal            | Perintah (`php artisan`, `composer`, `git`) yang dijalankan di folder proyek Laravel.                                                                                                       |
| Klien MySQL         | Aplikasi yang Anda pakai untuk membuka database MySQL dan menjalankan SQL secara manual.                                                                                                    |
| Postman             | Aplikasi untuk mengirim request ke API dan melihat jawabannya.                                                                                                                              |
| Browser             | Browser di komputer untuk memakai halaman Web.                                                                                                                                              |
| HP fisik            | HP Android yang dipakai menjalankan aplikasi Android.                                                                                                                                       |
| Cloudflare          | Dashboard web Cloudflare (untuk bucket R2).                                                                                                                                                 |
| php.ini             | File pengaturan PHP di komputer, bukan bagian proyek Laravel.                                                                                                                               |
| Dokumen             | Menulis keputusan, wireframe, atau README, atau memperbarui dokumen perancangan.                                                                                                            |

---

## 2. Beban dan Kelayakan

Semua durasi di bawah adalah **perkiraan saya**, bukan ukuran pasti.

- Total beban C03 sampai C26 sekitar **39 jam**. C01 dan C02 dijadwalkan 4 Oktober.
- Waktu tersedia 5 sampai 12 Oktober pada 6-8 jam per hari adalah sekitar 48 sampai 64 jam, sehingga ada sisa sekitar 9 sampai 25 jam sebagai penyangga.
- Jadwal tidak memperhitungkan hari ketika pekerjaan lain (tim Roblox, startup) mengambil waktu. Bila ada hari yang sudah pasti kosong, jadwal perlu dihitung ulang.

| Target                                   | Nilai    | Catatan                                                                                                                        |
| ---------------------------------------- | -------- | ------------------------------------------------------------------------------------------------------------------------------ |
| Gate 7 Okt: P0 backend dan API, Web P1   | **7/10** | Beban 6 Okt dan 7 Okt padat, tetapi 8 Okt masih dapat menampung limpahan                                                       |
| Gate 7 Okt: Android login (target lunak) | **5/10** | C11, C12, dan C13 berada pada hari yang sama (sekitar 5,5 jam); bila meluber, bergeser ke 8 Okt                                |
| Gate 12 Okt: Full MVP                    | **6/10** | Risiko terbesar: upload dan pemilih file Android (C16, C17), FileProvider (C20), dan Cloudflare R2 yang baru dipasang 9-10 Okt |

---

## 3. Jadwal

Urutan mengikuti prioritas Anda: database, autentikasi, Ebook API, Web, Android, integrasi, polish. Pekerjaan pada baris yang sama dapat ditukar urutannya selama syarat **Membutuhkan** terpenuhi.

| Tanggal | Hari   | Pekerjaan (perkiraan jam)                                                                                                        | Jam       |
| ------- | ------ | -------------------------------------------------------------------------------------------------------------------------------- | --------- |
| 4 Okt   | Minggu | C01 setup, register, login, logout Web (1,5-2); C02 tabel dan model (1)                                                          | sekitar 3 |
| 5 Okt   | Senin  | C03 objek database (1,5); C04 autentikasi API (1,5); C05 layanan penyimpanan ebook (1,5); C06 layanan ubah status dan file (1,5) | 6         |
| 6 Okt   | Selasa | C07 API daftar dan ubah status (1,5); C08 API upload dan unduh (1,5); C09 uji API (1,5); C10 Web daftar dan ubah status (2)      | 6,5       |
| 7 Okt   | Rabu   | C11 Web upload dan buka PDF (1,5); C12 setup Android (2); C13 Android login dan register (2). **Gate 7 Oktober**                 | 5,5       |
| 8 Okt   | Kamis  | C14 Android daftar koleksi (1,5); C15 Android ubah status (1); C16 Android pemilih file (2); C17 Android upload (2)              | 6,5       |
| 9 Okt   | Jumat  | C18 uji upload (1); C19 Android unduh PDF (2); C20 Android buka PDF (1,5); C21 setup Cloudflare R2 (1,5)                         | 6         |
| 10 Okt  | Sabtu  | C22 pindah ke R2 (1,5); C23 Android akses offline dan logout (2); C24 uji lintas platform (1,5); C25 uji end-to-end (2)          | 7         |
| 11 Okt  | Minggu | C26 regresi dan finalisasi (1,5); sisa waktu untuk limpahan dan item P2                                                          | 1,5+      |
| 12 Okt  | Senin  | Penyangga; **Gate 12 Oktober**                                                                                                   | -         |

Hari yang pekerjaannya selesai lebih cepat dapat menarik pekerjaan hari berikutnya maju. Hari yang meluber memindahkan sisanya ke hari berikutnya; bila Gate terancam, ikuti urutan pemotongan di bagian 7.

---

## 4. Backlog per Pekerjaan

### Daftar Status Pekerjaan

Centang pekerjaan di sini setelah kotak **Selesai jika**-nya tercentang.

- [ ] C01 · Laravel Web · Setup, register, login, logout (4 Okt)
- [ ] C02 · Laravel Backend · Tabel ebook dan model (4 Okt)
- [ ] C03 · Laravel Backend · Objek database: procedure, trigger, function (5 Okt)
- [ ] C04 · Laravel Backend dan Laravel API · Autentikasi API (5 Okt)
- [ ] C05 · Laravel Backend · Layanan penyimpanan ebook (5 Okt)
- [ ] C06 · Laravel Backend · Layanan ubah status dan akses file (5 Okt)
- [ ] C07 · Laravel API · API daftar dan ubah status (6 Okt)
- [ ] C08 · Laravel API · API upload dan unduh (6 Okt)
- [ ] C09 · Uji di Postman · Pengujian API menyeluruh (6 Okt)
- [ ] C10 · Laravel Web · Daftar koleksi dan ubah status (6 Okt)
- [ ] C11 · Laravel Web · Upload dan buka PDF (7 Okt)
- [ ] C12 · Android · Setup proyek dan koneksi ke backend (7 Okt)
- [ ] C13 · Android · Login dan register (7 Okt)
- [ ] C14 · Android · Daftar koleksi (8 Okt)
- [ ] C15 · Android · Ubah status baca (8 Okt)
- [ ] C16 · Android · Pemilih file (8 Okt)
- [ ] C17 · Android · Upload multipart (8 Okt)
- [ ] C18 · Uji di Browser dan HP fisik · Pengujian upload end-to-end (9 Okt)
- [ ] C19 · Android · Unduh PDF (9 Okt)
- [ ] C20 · Android · Penampil PDF (9 Okt)
- [ ] C21 · Laravel Backend dan Cloudflare · Setup Cloudflare R2 (9 Okt)
- [ ] C22 · Laravel Backend dan Cloudflare · Pindah ke R2 (10 Okt)
- [ ] C23 · Android · Akses offline, logout, dan penanganan 401 (10 Okt)
- [ ] C24 · Laravel Web, Laravel API, dan Android · Pengujian lintas platform dan pesan error (10 Okt)
- [ ] C25 · Uji di Browser dan HP fisik · Pengujian end-to-end Web dan Android (10 Okt)
- [ ] C26 · Regresi dan finalisasi (11 Okt)

### Epic A: Dasar Backend (P0)

#### C01 · Laravel Web · Setup, register, login, logout · P0 · 4 Okt

**Dikerjakan di:** Terminal, Klien MySQL, Laravel Backend (hanya setup dan tabel `users`), Laravel Web, Browser. Android dan Laravel API tidak terlibat.

**Setup**

- [x] **[Terminal]** Proyek Laravel baru.
- [x] **[Klien MySQL]** Database MySQL kosong.
- [x] **[Laravel Backend]** Isi koneksi database di file `.env` (file pengaturan Laravel).
- [x] **[Laravel Backend]** Tiga pengaturan di `.env` (`SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`) agar sesi, cache, dan antrean tidak membutuhkan tabel di database.
- [x] **[Laravel Backend]** Hapus bawaan Laravel yang tidak dipakai: tabel `password_reset_tokens`, `sessions`, cache, dan jobs, serta kolom `email_verified_at` dan `remember_token` di tabel `users`.
- [x] **[Laravel Backend]** Migration (file pembuat tabel) `users` sesuai dokumen 03 bagian 3: `name` (maks 100 karakter), `email` unik, `password`, `created_at`, `updated_at`.
- [x] **[Laravel Web]** Satu layout Blade (kerangka halaman yang dipakai bersama) dengan CSS biasa, tanpa Tailwind, Vite, atau Node.js.
- [x] **[Laravel Web]** File `AuthController` untuk Web (satu file yang menampung logika register, login, dan logout halaman Web).

**Register**

- [x] **[Laravel Web]** Dua route (alamat halaman): `GET /register` untuk menampilkan form, `POST /register` untuk memproses form.
- [x] **[Laravel Web]** Satu view (file tampilan) berisi form dengan kolom nama, email, password, konfirmasi password, tombol daftar, dan tautan ke halaman login.
- [x] **[Laravel Web]** Method di `AuthController` yang menampilkan form.
- [x] **[Laravel Web]** Method di `AuthController` yang memeriksa isian: nama wajib diisi dan maksimal 100 karakter; email wajib diisi, berformat email, maksimal 255 karakter, dan belum dipakai akun lain; password wajib diisi, minimal 8 karakter, dan sama dengan konfirmasinya (dokumen 04 bagian 5.1).
- [x] **[Laravel Web]** Penyimpanan user baru ke tabel `users`, dengan password di-hash (diacak satu arah sehingga tidak tersimpan sebagai teks asli).
- [x] **[Laravel Web]** Jika berhasil: arahkan ke halaman login dengan pesan sukses (dokumen 02 bagian 5.1: akun dibuat, lalu login).
- [x] **[Laravel Web]** Jika gagal: kembali ke form, tampilkan pesan error berbahasa Indonesia, dan isian nama serta email tetap terisi.

**Login**

- [x] **[Laravel Web]** Dua route: `GET /login` dan `POST /login`.
- [x] **[Laravel Web]** Satu view berisi form email, password, dan tombol masuk.
- [x] **[Laravel Web]** Method yang menampilkan form, dan method yang memeriksa bahwa email dan password terisi.
- [x] **[Laravel Web]** Pencocokan email dan password dengan tabel `users`. Di Laravel ini dilakukan oleh `Auth::attempt`, yaitu fungsi yang membandingkan isian dengan data user dan, bila cocok, membuat sesi login.
- [x] **[Laravel Web]** Jika cocok: buat ulang ID sesi (mencegah pembajakan sesi), lalu arahkan ke `/ebooks`.
- [x] **[Laravel Web]** Jika tidak cocok: kembali ke form dengan pesan "Email atau password salah" (tidak membedakan mana yang salah).
- [x] **[Laravel Web]** Batas 5 percobaan per menit pada `POST /login`.
- [x] **[Laravel Web]** Halaman `/ebooks` sementara yang hanya menampilkan nama user dan hanya bisa dibuka setelah login. Jika belum login, user diarahkan ke halaman login. Halaman ini diganti pada C10.

**Logout**

- [x] **[Laravel Web]** Route `POST /logout` dan tombol logout di `/ebooks`.
- [x] **[Laravel Web]** Akhiri sesi, buat ulang token CSRF (kode pengaman form), lalu kembali ke login.

**Penutup**

- [x] **[Terminal]** Inisialisasi Git dan commit pertama.

- [x] **Selesai jika:** **[Browser]** register, login, dan logout berjalan; **[Klien MySQL]** tabel `users` sesuai dokumen 03 bagian 3 tanpa kolom atau tabel bawaan yang tidak dipakai.

#### C02 · Laravel Backend · Tabel ebook dan model · P0 · 4 Okt

Membutuhkan: C01.

**Dikerjakan di:** Laravel Backend, Terminal, Klien MySQL. Android, Laravel API, dan Laravel Web tidak terlibat.

**Tabel**

- [x] **[Laravel Backend]** Migration `ebooks` (dokumen 03 bagian 4): `id`, `user_id` (kunci asing ke `users.id`, yaitu penanda bahwa setiap ebook dimiliki satu user), `title`, `file_path`, `file_hash` (CHAR 64, hasil SHA-256 isi file), `is_read` (bawaan false), `created_at`, `updated_at`.
- [x] **[Laravel Backend]** Constraint unik pada pasangan `(user_id, file_hash)` agar satu user tidak punya dua PDF yang sama, dan index pada `user_id` (dokumen 03 bagian 12). => karena hal ini fitur delete ebooks dipertimbangkan untuk masuk lebih cepat.
- [x] **[Laravel Backend]** Migration `ebook_status_history` (dokumen 03 bagian 5): `id`, `ebook_id` (kunci asing ke `ebooks.id`), `old_status`, `new_status`, `changed_at`.

**Model**

- [x] **[Laravel Backend]** Model `Ebook`: `is_read` dibaca sebagai boolean; relasi ke `User` (milik satu user) dan ke riwayat status (punya banyak).
- [x] **[Laravel Backend]** Model `User`: relasi punya banyak `Ebook`.

- [x] **Selesai jika:** **[Terminal]** `php artisan migrate:fresh` berhasil dan **[Klien MySQL]** ketiga tabel beserta kunci asing, index, dan constraint unik terlihat benar (dokumen 03 dan 04).

#### C03 · Laravel Backend · Objek database: procedure, trigger, function · P0 · 5 Okt

Membutuhkan: C02.

**Dikerjakan di:** Laravel Backend (menulis migration), Terminal, Klien MySQL (menguji). Android, Laravel API, dan Laravel Web tidak terlibat.

**Cara pembuatan**

- [x] **[Laravel Backend]** Satu migration khusus yang membuat semua objek di bawah memakai `DB::unprepared()` (menjalankan SQL mentah). Migration ini tidak boleh berada di dalam transaksi karena perintah pembuatan objek di MySQL otomatis meng-commit.

**Stored procedure** (dokumen 03 bagian 7.1)

- [x] **[Laravel Backend]** `sp_mark_ebook_read(user_id, ebook_id)`: memeriksa bahwa ebook milik user (jika bukan, menghasilkan error), lalu mengubah `is_read` menjadi true dan `updated_at` menjadi `NOW()`. `updated_at` harus diisi di sini karena perubahan lewat procedure tidak melewati Eloquent yang biasanya mengisinya.
- [x] **[Laravel Backend]** `sp_mark_ebook_unread(user_id, ebook_id)`: sama, dengan `is_read` menjadi false.
- [x] **[Laravel Backend]** Procedure tidak mengisi tabel riwayat; itu tugas trigger.

**Trigger** (dokumen 03 bagian 7.2)

- [x] **[Laravel Backend]** Trigger `AFTER UPDATE` pada `ebooks`: menambah satu baris ke `ebook_status_history` (`old_status`, `new_status`, `changed_at`) hanya bila `OLD.is_read <> NEW.is_read`.

**Function** (dokumen 03 bagian 7.3)

- [x] **[Laravel Backend]** `fn_count_read_ebooks(user_id)`: mengembalikan jumlah ebook milik user dengan `is_read` true.

**Pengujian manual**

- [x] **[Klien MySQL]** Memanggil procedure mengubah status dan menambah tepat satu baris riwayat.
- [x] **[Klien MySQL]** Memanggil procedure kedua kali dengan nilai yang sama tidak menambah riwayat.
- [x] **[Klien MySQL]** Memanggil procedure pada ebook milik user lain menghasilkan error.
- [x] **[Klien MySQL]** Function mengembalikan jumlah yang benar.

- [x] **Selesai jika:** keempat objek database teruji manual.

#### C04 · Laravel Backend dan Laravel API · Autentikasi API · P0 · 5 Okt

Membutuhkan: C01.

**Dikerjakan di:** Terminal, Laravel Backend (hanya model `User`), Laravel API, Postman. **Android tidak terlibat.** Aplikasi Android baru memakai endpoint ini pada C13 (pekerjaan login dan register Android). Laravel Web tidak diubah.

**Pemasangan**

- [x] **[Terminal]** Pasang Sanctum (paket token bawaan Laravel untuk API) dengan `php artisan install:api`.
- [x] **[Laravel Backend]** Tambahkan `HasApiTokens` pada model `User`.

**Endpoint**

- [x] **[Laravel API]** `POST /api/register`: aturan isian sama dengan register Web; respons 201 berisi `id`, `name`, `email`, **tanpa token** (dokumen 04 bagian 5.1).
- [x] **[Laravel API]** `POST /api/login`: menerima `email`, `password`, dan `device_name` (opsional, bawaan "android"); respons 200 berisi `token`, `token_type` "Bearer", dan data user (dokumen 04 bagian 5.2).
- [x] **[Laravel API]** `POST /api/login` memberi 401 `invalid_credentials` bila salah, 422 bila isian kurang, dan 429 `too_many_attempts` bila lebih dari 5 percobaan per menit.
- [x] **[Laravel API]** `POST /api/logout`: mencabut token yang dipakai pada request tersebut; respons 200 dengan pesan "Logout berhasil." (dokumen 04 bagian 5.3).
- [ ] **[Laravel API]** Jawaban error ditulis dalam format dokumen 04 bagian 3.4.

**Pengujian**

- [ ] **[Postman]** Setiap request diberi header `Accept: application/json`. Header ini **dikirim oleh pihak yang memanggil API** (di sini Postman, nanti Android pada C13), bukan oleh Laravel; tanpa header ini Laravel dapat menjawab dengan halaman HTML, bukan JSON (dokumen 04 bagian 3.1).
- [ ] **[Postman]** Uji register, login, dan logout.

- [ ] **Selesai jika:** **[Postman]** register, login, dan logout lulus; token yang sudah di-logout ditolak dengan 401.

#### C05 · Laravel Backend · Layanan penyimpanan ebook · P0 · 5 Okt

Membutuhkan: C02.

**Dikerjakan di:** Laravel Backend, Terminal. Android, Laravel API, dan Laravel Web tidak terlibat; Web dan API baru memanggil layanan ini pada C07, C08, dan C11.

- [ ] **[Laravel Backend]** File `app/Services/EbookService.php`: satu kelas yang dipakai bersama oleh Web dan API agar logika tidak ditulis dua kali (dokumen 04 bagian 8).
- [ ] **[Laravel Backend]** Pengaturan disk penyimpanan dari `.env` (`EBOOK_DISK`, bawaan `local`), sehingga pindah ke Cloudflare R2 nanti hanya perubahan pengaturan.
- **[Laravel Backend]** Method `store` (dokumen 04 bagian 5.5):
    - [ ] menghitung SHA-256 isi file (`hash_file`);
    - [ ] memeriksa apakah user sudah punya file dengan hash sama; jika ya, melempar `DuplicateEbookException` yang membawa `existing_id`;
    - [ ] menyimpan file ke kunci `ebooks/user-{id}/{uuid}.pdf`;
    - [ ] menyimpan baris `ebooks` di dalam `DB::transaction`; judul diambil dari nama file tanpa ekstensi bila kosong;
    - [ ] bila penyimpanan baris gagal: ROLLBACK, lalu hapus file yang tadi disimpan (compensating action, yaitu langkah pembalik yang dijalankan Laravel karena ROLLBACK tidak mengurus file); bila penghapusan file gagal, catat `file_path` ke log;
    - [ ] kegagalan _duplicate key_ dari constraint unik (misalnya dua upload bersamaan) diperlakukan sebagai duplikat: file dihapus dan hasilnya sama dengan duplikat.
- [ ] **[Terminal]** Pengujian lewat `php artisan tinker` (konsol interaktif Laravel): berhasil, duplikat, dan kegagalan buatan.

- [ ] **Selesai jika:** ebook tersimpan (file dan baris), duplikat ditolak, dan kegagalan penyimpanan baris tidak meninggalkan file yatim.

#### C06 · Laravel Backend · Layanan ubah status dan akses file · P0 · 5 Okt

Membutuhkan: C03, C05.

**Dikerjakan di:** Laravel Backend, Terminal. Android, Laravel API, dan Laravel Web tidak terlibat.

- **[Laravel Backend]** Method `setReadStatus` pada `EbookService` (dokumen 04 bagian 5.7):
    - [ ] mencari ebook milik user; bila tidak ada, `ModelNotFoundException`;
    - [ ] bersifat idempotent: bila status sudah sama dengan yang diminta, procedure tidak dipanggil dan keadaan terkini dikembalikan;
    - [ ] bila berbeda, memanggil `sp_mark_ebook_read` atau `sp_mark_ebook_unread` di dalam `DB::transaction`.
- [ ] **[Terminal]** Pengujian ROLLBACK lewat `tinker`: error dipaksa di dalam transaksi, lalu status dan riwayat sama-sama tidak berubah.
- [ ] **[Laravel Backend]** Method `streamFile` (dokumen 04 bagian 5.6): memeriksa kepemilikan; memeriksa file ada di disk (bila tidak, exception `file_missing`); mengembalikan stream PDF dengan `Content-Disposition: inline`.
- [ ] **[Terminal]** Pengujian `streamFile` lewat `tinker`.

- [ ] **Selesai jika:** COMMIT dan ROLLBACK terbukti benar pada perubahan status, dan akses file hanya untuk pemiliknya.

### Epic B: Ebook API (P0)

#### C07 · Laravel API · API daftar dan ubah status · P0 · 6 Okt

Membutuhkan: C04, C06.

**Dikerjakan di:** Laravel API, Postman. Android dan Laravel Web tidak terlibat; Laravel Backend hanya dipanggil, tidak diubah.

- [ ] **[Laravel API]** `EbookResource` (bentuk data ebook yang dikirim ke klien): `id`, `title`, `is_read`, `created_at`, `updated_at`; tanpa `user_id`, `file_path`, `file_hash` (dokumen 04 bagian 4).
- [ ] **[Laravel API]** `GET /api/ebooks`: ebook milik user, urut `created_at` menurun, tanpa pagination; `meta.total` dan `meta.read_count` (dari `fn_count_read_ebooks`); koleksi kosong menghasilkan `data: []` dengan nilai 0 (dokumen 04 bagian 5.4).
- [ ] **[Laravel API]** `PATCH /api/ebooks/{id}`: `is_read` wajib boolean, memanggil `setReadStatus`, respons 200 berisi data terkini (dokumen 04 bagian 5.7).
- [ ] **[Laravel API]** Penanganan error JSON secara global: 401 `unauthenticated`, 404 `ebook_not_found` (ebook milik user lain diperlakukan sama dengan tidak ada), 500 `server_error` (dokumen 04 bagian 3.4 sampai 3.6).
- [ ] **[Postman]** Pengujian: koleksi kosong, berisi, tanpa token; `PATCH` dengan status sama (riwayat tidak bertambah), pada ebook user lain (404), dan dengan body kosong (422).

- [ ] **Selesai jika:** **[Postman]** respons sesuai dokumen 04 bagian 5.4 dan 5.7.

#### C08 · Laravel API · API upload dan unduh · P0 · 6 Okt

Membutuhkan: C05, C07.

**Dikerjakan di:** php.ini, Laravel API, Postman. Android dan Laravel Web tidak terlibat; Laravel Backend hanya dipanggil, tidak diubah.

- [ ] **[php.ini]** `upload_max_filesize = 20M` dan `post_max_size = 25M`, lalu server dijalankan ulang (dokumen 04 bagian 7.1).
- [ ] **[Laravel API]** `POST /api/ebooks` (dokumen 04 bagian 5.5): `file` wajib PDF (ekstensi `pdf` dan MIME `application/pdf`) maksimal 20480 KB; `title` opsional maksimal 255; memanggil `EbookService::store`; respons 201.
- [ ] **[Laravel API]** `DuplicateEbookException` dipetakan ke 409 `duplicate_ebook` dengan pesan "PDF ini sudah ada di koleksi Anda." dan `data.existing_id`.
- [ ] **[Laravel API]** `GET /api/ebooks/{id}/file` (dokumen 04 bagian 5.6): header `Content-Type`, `Content-Length`, dan `Content-Disposition: inline`; 404 `ebook_not_found` dan 404 `file_missing`.
- [ ] **[Postman]** Pengujian: upload valid, duplikat, bukan PDF, di atas 20 MB; hasil unduhan dibuka.

- [ ] **Selesai jika:** **[Postman]** keempat kasus upload benar dan PDF hasil unduhan dapat dibuka.

#### C09 · Uji di Postman · Pengujian API menyeluruh · P0 · 6 Okt

Membutuhkan: C08.

**Dikerjakan di:** Postman, Terminal (tag Git), dan Laravel API atau Laravel Backend bila ada bug yang diperbaiki. Android dan Laravel Web tidak terlibat.

- [ ] **[Postman]** Postman collection berisi 7 endpoint dengan environment (`base_url` dan `token`).
- [ ] **[Postman]** Seluruh skenario pada matriks uji API (bagian 6) dijalankan.
- [ ] **[Laravel API]** Bug yang ditemukan diperbaiki, lalu **[Terminal]** beri tag Git `api-stable`.

- [ ] **Selesai jika:** seluruh baris matriks uji API lulus.

### Epic C: Web (P1)

#### C10 · Laravel Web · Daftar koleksi dan ubah status · P1 · 6 Okt

Membutuhkan: C06, C01.

**Dikerjakan di:** Dokumen, Laravel Web, Browser, Klien MySQL. Android dan Laravel API tidak terlibat; Laravel Backend hanya dipanggil.

**Rancangan layar (dikerjakan lebih dulu agar tampilan jelas)**

- [ ] **[Dokumen]** Wireframe (sketsa kasar tata letak) empat layar: Login, Daftar Ebook, Upload, dan Empty State (tampilan saat koleksi kosong), berupa sketsa tangan atau ASCII yang menentukan elemen, tombol, dan pesan.
- [ ] **[Dokumen]** Wireframe Login dan Register berlaku untuk Web dan Android; halaman Web dari C01 tidak dikerjakan ulang.
- [ ] **[Dokumen]** Wireframe disimpan di repositori, misalnya folder `docs/`.

**Halaman**

- [ ] **[Laravel Web]** Route `GET /ebooks` dalam grup `auth` (hanya untuk user yang sudah login) dan `WebEbookController@index`, menggantikan halaman sementara dari C01 (dokumen 04 bagian 8).
- [ ] **[Laravel Web]** View daftar berisi judul, status baca, dan tanggal; Empty State sesuai wireframe; jumlah ebook yang sudah dibaca dari `fn_count_read_ebooks`.
- [ ] **[Laravel Web]** Kontrol ubah status: form dengan `@method('PATCH')` ke `PATCH /ebooks/{id}` yang memanggil `EbookService::setReadStatus`.
- [ ] **[Klien MySQL]** Setelah status diubah dari Browser, tabel `ebook_status_history` terisi.

- [ ] **Selesai jika:** **[Browser]** setelah login user melihat koleksinya sendiri (atau Empty State) dan dapat mengubah status baca.

#### C11 · Laravel Web · Upload dan buka PDF · P1 · 7 Okt

Membutuhkan: C10, C05.

**Dikerjakan di:** Laravel Web, Browser. Android dan Laravel API tidak terlibat; Laravel Backend hanya dipanggil.

- [ ] **[Laravel Web]** Route `POST /ebooks` dan view form upload (`multipart/form-data`, yaitu jenis form yang dapat mengirim file) sesuai wireframe.
- [ ] **[Laravel Web]** Validasi: file harus PDF, maksimal 20480 KB; `title` opsional.
- [ ] **[Laravel Web]** Pesan error berbahasa Indonesia untuk file bukan PDF, lebih dari 20 MB, dan duplikat ("PDF ini sudah ada di koleksi Anda.").
- [ ] **[Laravel Web]** Route `GET /ebooks/{id}/file` dan tautan buka PDF di daftar, yang membuka PDF di penampil bawaan browser.
- [ ] **[Laravel Web]** Ebook milik user lain menghasilkan 404.

- [ ] **Selesai jika:** **[Browser]** upload, penolakan duplikat, dan buka PDF berjalan. **Pekerjaan ini menutup Gate 7 Oktober** (bagian 6).

### Epic D: Android (P1)

#### C12 · Android · Setup proyek dan koneksi ke backend · P1 · 7 Okt

Membutuhkan: C04.

**Dikerjakan di:** Dokumen, Android, Terminal (hanya menjalankan server Laravel), HP fisik. Laravel tidak diubah; yang dipakai adalah `POST /api/login` dari C04.

**Keputusan yang harus tertulis**

- [ ] **[Dokumen]** Struktur proyek Android: package dan lapisan sederhana (misalnya `data/api`, `data/model`, `ui/login`, `ui/ebooks`, `util`) dengan satu paragraf aturan.
- [ ] **[Dokumen]** Akses backend dari HP fisik. Karena pengujian memakai HP fisik, rekomendasinya jaringan lokal: server Laravel dijalankan dengan `php artisan serve --host=0.0.0.0` dan HP serta komputer berada pada Wi-Fi yang sama. Hosting online tidak direkomendasikan karena menambah pekerjaan. Tulis keputusan akhir di sini.

**Proyek**

- [ ] **[Android]** Proyek Kotlin dengan ViewBinding (cara mengakses elemen layar tanpa `findViewById`), dependency Retrofit (pustaka pemanggil API), converter Gson (pengubah JSON), OkHttp logging-interceptor (pencatat request di Logcat), dan RecyclerView (komponen daftar).
- [ ] **[Android]** `AndroidManifest`: izin `INTERNET` dan izin HTTP tanpa enkripsi (cleartext) khusus pengembangan.
- [ ] **[Terminal]** Jalankan Laravel dengan `php artisan serve --host=0.0.0.0` sesuai keputusan di atas.
- [ ] **[Android]** Uji sambung dari **[HP fisik]**: memanggil `POST /api/login` dengan data tetap dan melihat respons di Logcat (jendela log Android Studio).

- [ ] **Selesai jika:** Android menerima respons dari Laravel dan dua keputusan di atas tertulis.

#### C13 · Android · Login dan register · P1 · 7 Okt

Membutuhkan: C12.

**Dikerjakan di:** Dokumen, Android, HP fisik. Laravel tidak diubah; yang dipakai adalah `POST /api/register` dan `POST /api/login` dari C04.

- [ ] **[Dokumen]** Wireframe Login dan Register dari C10 disesuaikan untuk Android (elemen dan pesan error).
- [ ] **[Android]** `ApiService` (daftar endpoint untuk Retrofit), model request dan respons.
- [ ] **[Android]** `ApiClient` dengan interceptor yang menambah header `Accept: application/json` pada setiap request dan `Authorization: Bearer {token}` bila token ada. Di sini Android yang mengirim header `Accept`, sama seperti Postman pada C04.
- [ ] **[Android]** `TokenStore` menyimpan token di `SharedPreferences` privat (`MODE_PRIVATE`); dicatat sebagai utang teknis karena belum terenkripsi (dokumen 04 bagian 3.2).
- [ ] **[Android]** `LoginActivity`: kolom email dan password, tombol, indikator loading, dan pesan untuk 401, 422, 429, dan tanpa koneksi.
- [ ] **[Android]** `RegisterActivity`: kolom nama, email, password, dan konfirmasi password; memanggil `POST /api/register`; menampilkan error 422 (termasuk email sudah terdaftar); setelah 201 kembali ke Login dengan pesan sukses (tanpa token, dokumen 04 bagian 5.1).
- [ ] **[Android]** Saat aplikasi dibuka dan token sudah ada, langsung ke daftar ebook.

- [ ] **Selesai jika:** **[HP fisik]** register dan login berhasil dan token tersimpan.

#### C14 · Android · Daftar koleksi · P1 · 8 Okt

Membutuhkan: C13, C07.

**Dikerjakan di:** Dokumen, Android, HP fisik, Browser (untuk mengunggah ebook uji). Laravel tidak diubah; yang dipakai adalah `GET /api/ebooks` dari C07.

- [ ] **[Dokumen]** Wireframe Daftar Ebook dan Empty State disesuaikan untuk Android.
- [ ] **[Android]** Layout daftar dan item (judul dan kontrol status).
- [ ] **[Android]** `RecyclerView` dan adapter yang memanggil `GET /api/ebooks`.
- [ ] **[Android]** Tampilan loading, Empty State, dan error dengan tombol muat ulang; jumlah ebook yang sudah dibaca diambil dari `meta.read_count`.

- [ ] **Selesai jika:** ebook yang diunggah lewat **[Browser]** (Web) muncul di **[HP fisik]**.

#### C15 · Android · Ubah status baca · P1 · 8 Okt

Membutuhkan: C14.

**Dikerjakan di:** Android, HP fisik, Browser. Laravel tidak diubah; yang dipakai adalah `PATCH /api/ebooks/{id}` dari C07.

- [ ] **[Android]** Kotak centang pada item memanggil `PATCH /api/ebooks/{id}`; item diperbarui dari respons.
- [ ] **[Android]** Bila gagal, status dikembalikan ke sebelumnya dan pesan ditampilkan (dokumen 01, UC-09 dan UC-10: status lama dipertahankan saat penyimpanan gagal).
- [ ] **[HP fisik]** dan **[Browser]** Diuji dua arah: ubah di Android lalu lihat di Web, dan sebaliknya.

- [ ] **Selesai jika:** status konsisten antara Android dan Web.

#### C16 · Android · Pemilih file · P1 · 8 Okt · risiko tinggi

Membutuhkan: C14.

**Dikerjakan di:** Dokumen, Android, HP fisik. Laravel tidak terlibat.

- [ ] **[Dokumen]** Wireframe Upload disesuaikan untuk Android (tombol, nama dan ukuran file, pesan).
- [ ] **[Android]** Pemilih file memakai `ActivityResultContracts.OpenDocument` dengan tipe `application/pdf`.
- [ ] **[Android]** Nama file dibaca lewat `ContentResolver` (`OpenableColumns.DISPLAY_NAME`).
- [ ] **[Android]** Isi `Uri` (alamat file dari pemilih) disalin ke file sementara di `cacheDir`.
- [ ] **[Android]** File lebih dari 20 MB ditolak sebelum dikirim.

- [ ] **Selesai jika:** **[HP fisik]** nama dan ukuran file terbaca dan file sementara terbentuk.

#### C17 · Android · Upload multipart · P1 · 8 Okt · risiko tinggi

Membutuhkan: C16, C08.

**Dikerjakan di:** Android, HP fisik. Laravel tidak diubah; yang dipakai adalah `POST /api/ebooks` dari C08.

- [ ] **[Android]** Retrofit `@Multipart` mengirim file (`application/pdf`) dan `title` opsional.
- [ ] **[Android]** Batas waktu tulis dan baca OkHttp dinaikkan karena unggahan 20 MB pada koneksi lambat dapat melampaui bawaan (dokumen 04 bagian 7.1).
- [ ] **[Android]** Sebelum mengirim, koneksi diperiksa lewat `ConnectivityManager`; bila offline tampil "coba lagi saat online", tanpa antrean (dokumen 01 bagian 1.2 dan dokumen 02 bagian 7).
- [ ] **[Android]** Pemetaan error ke pesan: 409 duplikat, 413 atau 422 file terlalu besar atau bukan PDF, 401 sesi habis, 500 gagal di server.
- [ ] **[Android]** Saat sukses: kembali ke daftar, muat ulang, dan hapus file sementara.
- [ ] **[Android]** Tombol upload tidak dapat ditekan dua kali selama proses berjalan.

- [ ] **Selesai jika:** **[HP fisik]** PDF kecil berhasil terunggah.

#### C18 · Uji di Browser dan HP fisik · Pengujian upload end-to-end · P1 · 9 Okt

Membutuhkan: C17, C11.

**Dikerjakan di:** HP fisik, Browser, dan perbaikan di Android, Laravel Web, atau Laravel API bila ada bug.

- [ ] **[HP fisik]** dan **[Browser]** PDF kecil yang diunggah dari Android muncul di Android dan Web.
- [ ] **[HP fisik]** PDF yang sama dikirim ulang menghasilkan pesan duplikat (409).
- [ ] **[HP fisik]** Mode pesawat menghasilkan pesan offline dan tidak ada upload tertunda.
- [ ] **[HP fisik]** PDF sekitar 15-20 MB berhasil (atau batas waktu disesuaikan); di atas 20 MB menghasilkan pesan yang jelas.

- [ ] **Selesai jika:** keempat skenario berperilaku benar.

#### C19 · Android · Unduh PDF · P1 · 9 Okt

Membutuhkan: C14, C08.

**Dikerjakan di:** Dokumen, Android, HP fisik. Laravel tidak diubah; yang dipakai adalah `GET /api/ebooks/{id}/file` dari C08.

**Keputusan yang harus tertulis**

- [ ] **[Dokumen]** Penyimpanan lokal Android. Rekomendasi: PDF diunduh ke `filesDir/pdfs/{id}.pdf` (folder privat aplikasi); file hasil unduhan menjadi "file lokal" yang dipakai ulang selama masih ada; aplikasi tidak melacak file asli di folder Download; folder `pdfs` dihapus saat logout. Konfirmasi atau ubah, lalu tulis keputusan akhir.
- [ ] **[Dokumen]** Dokumen 00 dan 01 (bagian yang menjelaskan akses PDF Android dan UC-08) diperbarui agar istilah "file lokal" jelas mengacu pada file hasil unduhan; catat di bagian 9.

**Unduhan**

- [ ] **[Android]** Retrofit dengan `@Streaming` untuk `GET /api/ebooks/{id}/file`, agar seluruh file tidak dimuat ke memori sekaligus.
- [ ] **[Android]** Isi ditulis ke `{id}.pdf.part`, lalu namanya diganti menjadi `{id}.pdf` hanya setelah unduhan selesai, supaya file setengah jadi tidak pernah dianggap utuh.
- [ ] **[Android]** Indikator loading selama mengunduh.

- [ ] **Selesai jika:** **[HP fisik]** file `{id}.pdf` utuh tersimpan di penyimpanan aplikasi dan keputusan tertulis.

#### C20 · Android · Penampil PDF · P1 · 9 Okt · risiko tinggi

Membutuhkan: C19.

**Dikerjakan di:** Android, HP fisik. Laravel tidak terlibat.

- [ ] **[Android]** `FileProvider` (komponen yang memberi aplikasi lain izin sementara membaca file milik aplikasi ini) dideklarasikan di manifest beserta `res/xml/file_paths.xml`.
- [ ] **[Android]** `Intent` (permintaan membuka aplikasi lain) `ACTION_VIEW` bertipe `application/pdf` dengan `FLAG_GRANT_READ_URI_PERMISSION`.
- [ ] **[Android]** Bila tidak ada aplikasi PDF (`ActivityNotFoundException`), tampil pesan "tidak ada aplikasi PDF".
- [ ] **[HP fisik]** HP yang dipakai memiliki aplikasi penampil PDF.

- [ ] **Selesai jika:** **[HP fisik]** PDF terbuka di penampil eksternal.

### Epic E: Cloudflare R2 (P0)

#### C21 · Laravel Backend dan Cloudflare · Setup Cloudflare R2 · P0 · 9 Okt

Membutuhkan: C05.

**Dikerjakan di:** Cloudflare, Terminal, Laravel Backend. Android, Laravel API, dan Laravel Web tidak terlibat.

- [ ] **[Cloudflare]** Bucket (wadah penyimpanan) **privat** di Cloudflare R2.
- [ ] **[Cloudflare]** API token dengan izin Object Read and Write; endpoint, access key, dan secret key dicatat.
- [ ] **[Terminal]** Paket `league/flysystem-aws-s3-v3` dipasang lewat Composer.
- [ ] **[Laravel Backend]** Disk `r2` di `config/filesystems.php` (driver `s3`, endpoint R2, region `auto`); opsi _path style_ disesuaikan bila koneksi gagal.
- [ ] **[Terminal]** Uji tulis dan baca file kecil lewat `tinker`.

- [ ] **Selesai jika:** Laravel dapat menulis dan membaca objek di bucket R2.

#### C22 · Laravel Backend dan Cloudflare · Pindah ke R2 · P0 · 10 Okt

Membutuhkan: C21, C11, C08.

**Dikerjakan di:** Laravel Backend (hanya `.env`), Cloudflare, Browser, Postman, HP fisik. Tidak ada kode baru di Laravel API, Laravel Web, maupun Android; ketiganya hanya diuji.

- [ ] **[Laravel Backend]** `EBOOK_DISK=r2` di `.env`.
- [ ] **[Browser]** dan **[Postman]** Upload dari Web dan API menghasilkan objek di bucket (dilihat di **[Cloudflare]**).
- [ ] **[Browser]** dan **[HP fisik]** PDF dari R2 terbuka lewat Web dan Android.
- [ ] **[Terminal]** Uji compensating action: penyimpanan baris dipaksa gagal lewat `tinker`, lalu objek R2 harus terhapus.
- [ ] **[Cloudflare]** Bucket tidak dapat diakses publik.

- [ ] **Selesai jika:** upload dan akses file berjalan penuh melalui R2.

### Epic F: Integrasi dan Finalisasi

#### C23 · Android · Akses offline, logout, dan penanganan 401 · P1 · 10 Okt

Membutuhkan: C20, C19.

**Dikerjakan di:** Android, HP fisik. Laravel tidak diubah; yang dipakai adalah `POST /api/logout` dari C04.

- **[Android]** Saat item diketuk (dokumen 02 bagian 10.1):
    - [ ] bila `filesDir/pdfs/{id}.pdf` ada, langsung dibuka;
    - [ ] bila tidak ada dan offline, tampil "Butuh internet untuk membuka PDF";
    - [ ] bila tidak ada dan online, diunduh lalu dibuka;
    - [ ] bila server menjawab 404 `file_missing`, tampil "file tidak tersedia".
- [ ] **[Android]** Logout memanggil `POST /api/logout`; token lokal **selalu** dihapus meski offline (dokumen 04 bagian 5.3); folder `pdfs` dihapus sesuai keputusan C19.
- [ ] **[Android]** Interceptor: jawaban 401 menghapus token dan mengembalikan user ke Login.
- [ ] **[HP fisik]** Pengujian: buka PDF sekali saat online, aktifkan mode pesawat, buka lagi (tetap terbuka); logout lalu login ulang.

- [ ] **Selesai jika:** **[HP fisik]** skenario offline setelah unduhan pertama berhasil dan tidak ada kondisi tersangkut setelah token tidak valid.

#### C24 · Laravel Web, Laravel API, dan Android · Pengujian lintas platform dan pesan error · P1 · 10 Okt

Membutuhkan: C23, C22.

**Dikerjakan di:** Browser, HP fisik, Postman, dan perbaikan pesan di Laravel Web, Laravel API, dan Android. Laravel Backend tidak terlibat.

- [ ] **[Browser]** dan **[HP fisik]** Seluruh skenario matriks lintas platform (bagian 6) dijalankan.
- [ ] **[Laravel Web]** dan **[Laravel API]** Pesan error berbahasa Indonesia diseragamkan.
- [ ] **[Android]** Tidak ada layar kosong atau kondisi tanpa pesan.
- [ ] **[Browser]**, **[Postman]**, dan **[HP fisik]** Diuji: kredensial salah, email sudah terdaftar, password kurang dari 8 karakter.

- [ ] **Selesai jika:** seluruh baris matriks lulus atau tercatat sebagai bug.

#### C25 · Uji di Browser dan HP fisik · Pengujian end-to-end Web dan Android · P1 · 10 Okt

Membutuhkan: C24.

**Dikerjakan di:** Browser, HP fisik, Klien MySQL, dan perbaikan di bagian mana pun yang bermasalah.

- [ ] **[Browser]** Skenario dokumen 02 bagian 13 pada Web: Register, Login, Upload, Buka, Read/Unread, Logout.
- [ ] **[Klien MySQL]** Tabel `ebook_status_history` dan `fn_count_read_ebooks` diperiksa.
- [ ] **[HP fisik]** Skenario yang sama dari Android (register dari Android).
- [ ] Bug yang menghalangi alur P0 diperbaiki di bagian yang bermasalah.

- [ ] **Selesai jika:** kedua skenario berjalan tanpa campur tangan manual.

#### C26 · Regresi dan finalisasi · P0/P1 · 11 Okt

Membutuhkan: C25.

**Dikerjakan di:** Browser, HP fisik, Terminal, Klien MySQL, Dokumen, dan perbaikan di bagian mana pun yang bermasalah.

- [ ] **[Browser]** dan **[HP fisik]** Daftar periksa Gate 12 Oktober (bagian 6) dijalankan ulang; hanya bug yang menghalangi P0 dan P1 yang diperbaiki.
- [ ] **[Terminal]** Tag Git rilis.
- [ ] **[Dokumen]** `README` berisi langkah menjalankan Laravel dan Android.
- [ ] **[Klien MySQL]** Akun dan data demo disiapkan dan database dicadangkan.
- [ ] **[Browser]** dan **[HP fisik]** Skenario demo dilatih satu kali.

- [ ] **Selesai jika:** Gate 12 Oktober terpenuhi dan proyek dapat dijalankan ulang mengikuti `README`.

### Item P2 (hanya jika waktu tersisa)

Tidak dijadwalkan.

- [ ] **[Android]** **Polish tampilan minimal:** judul layar, label, dan nama aplikasi konsisten; tarik-untuk-muat-ulang pada daftar Android.
- [ ] **[Android]** **Penelitian Local Discovery (fitur penemuan PDF otomatis di Android):** penelitian kelayakan, bukan janji fitur. Saya tidak yakin pendekatan mana yang layak pada versi Android HP Anda karena pembatasan akses penyimpanan (scoped storage). Uji `MediaStore` dan/atau pemilihan folder lewat SAF (Storage Access Framework); putuskan jalan atau tidak. Bila tidak, catat sebagai keterbatasan dan jadikan pemilih file jalur utama (dokumen 00 bagian 15.1).

---

## 5. Pemetaan Pekerjaan per Tempat Kerja

**ya** = ada yang dibuat atau diubah di tempat itu. **pakai** = hanya dipanggil atau diuji, tanpa dibuat atau diubah. **-** = tidak terlibat.

| Pekerjaan | Laravel Backend | Laravel API | Laravel Web | Android |
| --------- | --------------- | ----------- | ----------- | ------- |
| C01       | ya              | -           | ya          | -       |
| C02       | ya              | -           | -           | -       |
| C03       | ya              | -           | -           | -       |
| C04       | ya              | ya          | -           | -       |
| C05       | ya              | -           | -           | -       |
| C06       | ya              | -           | -           | -       |
| C07       | pakai           | ya          | -           | -       |
| C08       | pakai           | ya          | -           | -       |
| C09       | -               | pakai       | -           | -       |
| C10       | pakai           | -           | ya          | -       |
| C11       | pakai           | -           | ya          | -       |
| C12       | -               | pakai       | -           | ya      |
| C13       | -               | pakai       | -           | ya      |
| C14       | -               | pakai       | -           | ya      |
| C15       | -               | pakai       | -           | ya      |
| C16       | -               | -           | -           | ya      |
| C17       | -               | pakai       | -           | ya      |
| C18       | -               | pakai       | pakai       | pakai   |
| C19       | -               | pakai       | -           | ya      |
| C20       | -               | -           | -           | ya      |
| C21       | ya              | -           | -           | -       |
| C22       | ya              | pakai       | pakai       | pakai   |
| C23       | -               | pakai       | -           | ya      |
| C24       | -               | ya          | ya          | ya      |
| C25       | pakai           | pakai       | pakai       | pakai   |
| C26       | pakai           | pakai       | pakai       | pakai   |

---

## 6. Gate dan Matriks Uji

### Gate 7 Oktober (Core MVP Functional)

**P0**

- [ ] MySQL dan Laravel berjalan
- [ ] Autentikasi dasar (Web dan API) berjalan
- [ ] PDF dapat diunggah saat online; metadata tersimpan di MySQL
- [ ] File tersimpan lewat storage abstraction (disk lokal diperbolehkan)
- [ ] Daftar PDF tampil; read/unread dapat diubah (procedure, trigger, COMMIT/ROLLBACK)
- [ ] PDF dapat dibuka lewat mekanisme akses yang sesuai
- [ ] 7 endpoint API lulus uji Postman

**P1**

- [ ] Web: login, daftar, manual upload
- [ ] Android: setup dan login (**target lunak**: bergeser ke 8 Okt bila C11-C13 tidak selesai 7 Okt)
- [ ] Android: menampilkan koleksi (dijadwalkan pada C14, 8 Okt)

### Gate 12 Oktober (Full MVP)

- [ ] Seluruh Gate 7 Oktober terpenuhi
- [ ] File tersimpan di Cloudflare R2 (bucket privat)
- [ ] Android: register, login, upload via file picker, buka PDF (local-first), ubah status, logout
- [ ] Android dan Web memakai koleksi yang sama; status tersinkron
- [ ] Ownership terjamin (akses silang antar user ditolak)
- [ ] Duplikat ditolak (409)
- [ ] Validasi dan penanganan error selesai
- [ ] Pengujian end-to-end selesai
- [ ] P2: Local Discovery berfungsi **atau** file picker stabil sebagai fallback

### Matriks uji API (dipakai pada C09, dijalankan di Postman)

| #   | Skenario                             | Hasil yang diharapkan                 | Lulus |
| --- | ------------------------------------ | ------------------------------------- | ----- |
| 1   | Register data valid                  | 201, tanpa token                      | [ ]   |
| 2   | Register email sudah ada             | 422                                   | [ ]   |
| 3   | Login benar                          | 200, token                            | [ ]   |
| 4   | Login salah                          | 401 `invalid_credentials`             | [ ]   |
| 5   | Login 6 kali dalam semenit           | 429 `too_many_attempts`               | [ ]   |
| 6   | `GET /ebooks` tanpa token            | 401 `unauthenticated`                 | [ ]   |
| 7   | `GET /ebooks` koleksi kosong         | 200, `data: []`, total 0              | [ ]   |
| 8   | Upload PDF valid                     | 201                                   | [ ]   |
| 9   | Upload PDF yang sama                 | 409 `duplicate_ebook` + `existing_id` | [ ]   |
| 10  | Upload non-PDF                       | 422                                   | [ ]   |
| 11  | Upload lebih dari 20 MB              | 422 atau 413                          | [ ]   |
| 12  | `PATCH` `is_read` berbeda            | 200, history bertambah satu           | [ ]   |
| 13  | `PATCH` `is_read` sama               | 200, history tidak bertambah          | [ ]   |
| 14  | `PATCH` ebook user lain              | 404 `ebook_not_found`                 | [ ]   |
| 15  | `GET file` milik sendiri             | 200, PDF                              | [ ]   |
| 16  | `GET file` milik user lain           | 404 `ebook_not_found`                 | [ ]   |
| 17  | `GET file`, file hilang dari storage | 404 `file_missing`                    | [ ]   |
| 18  | Logout lalu pakai token lama         | 401                                   | [ ]   |

### Matriks lintas platform (dipakai pada C24, dijalankan di Browser dan HP fisik)

| #   | Skenario                                                                           | Lulus |
| --- | ---------------------------------------------------------------------------------- | ----- |
| 1   | Upload dari Android, terlihat di Web                                               | [ ]   |
| 2   | Upload dari Web, terlihat di Android                                               | [ ]   |
| 3   | Status diubah di Android, terlihat di Web                                          | [ ]   |
| 4   | Status diubah di Web, terlihat di Android                                          | [ ]   |
| 5   | Upload via Web, unggah ulang via Android: 409                                      | [ ]   |
| 6   | Dua akun berbeda tidak dapat melihat atau membuka ebook satu sama lain             | [ ]   |
| 7   | Android offline: upload ditolak dengan pesan; PDF yang sudah diunduh tetap terbuka | [ ]   |

---

## 7. Cut Line (Urutan Pemotongan)

Bila waktu tidak cukup, korbankan dari atas ke bawah:

1. Item P2 (polish dan penelitian Local Discovery); keduanya sudah tidak dijadwalkan.
2. Pengujian file besar pada C18 (kurangi menjadi satu uji).
3. Penyeragaman pesan error pada C24 (pertahankan hanya pesan yang menghalangi alur P0).

**Tidak dapat dipotong:**

- C21 dan C22 (Cloudflare R2): keduanya P0 menurut dokumen 00 (bagian 19 dan Cut Rule), dan kriteria keberhasilan dokumen 00 menyebut penyimpanan cloud.
- Seluruh pekerjaan P0 dan P1 lainnya: C01 sampai C17 dan C19 sampai C23.

Jika Gate 7 Oktober terancam, yang dikorbankan lebih dulu adalah Android login (target lunak), bukan bagian Web atau API.

---

## 8. Parkir (di luar jadwal)

Catat ide atau bug baru di sini; jangan dikerjakan di tengah pekerjaan lain.

- [ ] Temuan review dokumen 00-05 (pagi 4 Okt) yang tidak menghalangi C01-C02.
- [ ] Urutan integrasi Cloudflare R2: dokumen 05 menjadwalkan R2 (C21-C22) pada 9-10 Okt, yaitu setelah Web dan Android berjalan, sedangkan dokumen 00 bagian 20 menempatkan integrasi R2 pada langkah 10, sebelum Web dan Android. Belum diputuskan mana yang berlaku (dibahas nanti).
- [ ] Penyimpanan lokal Android: makna "file lokal" (folder Download atau penyimpanan privat aplikasi; apakah file sementara pasca-upload dipindahkan ke `filesDir/pdfs/{id}.pdf`) dan pembaruan dokumen 00, 01, 02 terkait; diputuskan paling lambat saat mengerjakan C19 (dibahas nanti).
- [ ] (tambahkan di sini)

---

## 9. Log Perubahan Rancangan

Diisi setiap kali ada perubahan pada dokumen perancangan.

| Pekerjaan | Perubahan atau temuan                                                                                                                                                                                                                       | Dokumen yang diperbarui | Selesai |
| --------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------- | ------- |
| C13       | Token memakai `SharedPreferences` privat (04 bagian 3.2)                                                                                                                                                                                    | 04                      | [x]     |
| C19       | Penyimpanan lokal Android: lokasi dan sifat file hasil unduhan                                                                                                                                                                              | 00, 01                  | [ ]     |
| C01       | Autentikasi Web dibuat sendiri, bukan Breeze; register Web diarahkan ke login sesuai 02 bagian 5.1                                                                                                                                          | 05 (C01, C10, Parkir)   | [x]     |
| (semua)   | Dokumen 05 diubah dari jadwal berisi perintah menjadi daftar hasil per pekerjaan (versi 3); jadwal dihitung ulang untuk jam kerja 16.00-23.00/00; aturan kerja, durasi 90 menit, slot cadangan, dan catatan perubahan per pekerjaan dihapus | 05                      | [x]     |
| (semua)   | Tanda platform tunggal di judul pekerjaan ambigu (C04 disangka bagian Android). Setiap pekerjaan kini punya baris "Dikerjakan di" dan setiap butir diberi tanda tempat kerja (Laravel Backend, Laravel API, Laravel Web, Android, dan alat bantu); ditambah tabel pemetaan di bagian 5. Header `Accept` dinyatakan dikirim oleh Postman atau Android, bukan Laravel | 05                      | [x]     |
|           |                                                                                                                                                                                                                                             |                         | [ ]     |
