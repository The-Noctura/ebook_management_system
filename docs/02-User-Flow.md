# User Flow

## 1. Overview

Dokumen ini menjelaskan perjalanan pengguna saat menggunakan **Ebook Storage & To-Do List**, dari masuk ke sistem sampai mengelola collection ebook.

Berbeda dengan Use Case yang menjelaskan fungsi sistem, dokumen ini berfokus pada:

- urutan aktivitas pengguna;
- perpindahan antar kondisi atau halaman;
- keputusan yang dapat diambil user;
- jalur utama dan jalur alternatif;
- perbedaan alur Android dan Web;
- hubungan antara core flow dan fitur local ebook discovery.
  User Flow mengikuti prioritas MVP terbaru:

```text
P0 — Core MVP
Authentication
Collection
Upload / Storage
Read / Unread
Backend / API

        ↓

P1 — Platform MVP
Web Laravel Blade
Android Kotlin
Shared Collection

        ↓

P2 — Friction Reduction
Local Ebook Discovery
Add Local Ebook
Duplicate Prevention
```

**P2 tidak menjadi dependency bagi P0 dan P1.** Jika local discovery belum tersedia atau belum stabil, Android tetap menggunakan manual upload sebagai fallback.

---

# 2. Prinsip User Flow

User Flow dirancang berdasarkan prinsip:

> **Aplikasi harus mengurangi pekerjaan pengguna, bukan sekadar memindahkan pekerjaan tersebut.**

Karena itu:

1. User tetap memiliki kontrol terhadap ebook yang masuk collection.
2. Ebook yang ditemukan melalui discovery tidak otomatis di-upload.
3. Manual upload tetap tersedia.
4. Android dan Web menggunakan collection yang sama.
5. User tidak perlu mengelola collection secara terpisah di setiap platform.
6. Local discovery merupakan pengurangan langkah manual, bukan syarat agar sistem dapat digunakan.

---

# 3. Actor

MVP hanya memiliki satu actor:

```text
User
```

User dapat menggunakan:

```text
Android Kotlin
      │
      └──► Laravel REST API

Web Laravel Blade
      │
      └──► Laravel

          ↓
       MySQL + R2
```

---

# 4. Global User Flow

Alur penggunaan sistem secara umum:

```text
                    START
                      │
                      ▼
             ┌─────────────────┐
             │ Buka Aplikasi / │
             │      Web        │
             └────────┬────────┘
                      │
                      ▼
               Sudah punya akun?
                 /          \
               Tidak         Ya
                │             │
                ▼             ▼
             Register       Login
                │             │
                └──────┬──────┘
                       ▼
                  Collection
                       │
          ┌────────────┼─────────────┐
          │            │             │
          ▼            ▼             ▼
      Tambah Ebook   Akses Ebook   Kelola Status
          │                          │
          │                     Read / Unread
          │
      ┌───┴───────────────┐
      │                   │
      ▼                   ▼
 Manual Upload       Local Discovery
                          │
                          ▼
                    Pilih Ebook
                          │
                          ▼
                   Tambah ke Koleksi
                          │
                          ▼
                      Collection
```

Logout dapat dilakukan dari area pengguna kapan saja setelah login.

---

# 5. Authentication Flow

## 5.1 Register Flow

### Tujuan

Membuat akun baru agar user dapat memiliki collection ebook pribadi.

```text
START
  │
  ▼
Buka Register
  │
  ▼
Isi Data Registrasi
  │
  ▼
Submit
  │
  ▼
Validasi
  │
  ├── Gagal ──► Tampilkan Error
  │                 │
  │                 └──► Perbaiki Data
  │
  └── Berhasil
          │
          ▼
      Akun Dibuat
          │
          ▼
       Login
```

### Hasil

User memiliki akun dan dapat melanjutkan ke proses login.

---

## 5.2 Login Flow

```text
START
  │
  ▼
Buka Login
  │
  ▼
Masukkan Kredensial
  │
  ▼
Submit
  │
  ▼
Validasi Kredensial
  │
  ├── Tidak Valid ──► Tampilkan Error
  │                       │
  │                       └──► Login Kembali
  │
  └── Valid
        │
        ▼
    User Login
        │
        ▼
    Collection
```

### Hasil

User masuk ke area collection miliknya.

---

## 5.3 Logout Flow

```text
Collection / Area User
        │
        ▼
     Logout
        │
        ▼
   Sesi Diakhiri
        │
        ▼
      Login
```

---

# 6. Collection Flow

Collection merupakan pusat aktivitas setelah user login.

```text
Login
  │
  ▼
Collection
  │
  ├──► Lihat Daftar Ebook
  │
  ├──► Tambah Ebook
  │       │
  │       ├──► Manual Upload
  │       │
  │       └──► Local Discovery (Android)
  │
  ├──► Akses Ebook
  │
  └──► Ubah Status
          ├──► Tandai Sudah Dibaca
          └──► Tandai Belum Dibaca
```

Jika belum ada ebook:

```text
Collection
    │
    ▼
Collection Kosong
    │
    ▼
Tambah Ebook
```

---

# 7. Manual Upload Flow

Manual upload merupakan jalur penting dalam MVP.

Pada Web, manual upload menjadi metode utama untuk menambahkan ebook.

Pada Android, manual upload menjadi fallback apabila local discovery belum tersedia, tidak digunakan, atau tidak dapat bekerja pada kondisi perangkat.

```text
Collection
    │
    ▼
Tambah Ebook
    │
    ▼
Pilih Manual Upload
    │
    ▼
Pilih File
    │
    ├── Batal ──► Kembali
    │
    └── File Dipilih
            │
            ▼
        Validasi File
            │
            ├── Gagal ──► Tampilkan Error
            │                 │
            │                 └──► Pilih File Lagi
            │
            └── Berhasil
                    │
                    ▼
            Internet tersedia?
                    │
                    ├── Tidak ──► Upload Dibatalkan
                    │                 │
                    │                 ▼
                    │         Informasi: "Coba lagi
                    │         saat online"
                    │         (tidak ada pending upload)
                    │
                    └── Ya
                         │
                         ▼
                      Upload
                         │
                         ├── Gagal ──► Tampilkan Error
                         │             (PDF tidak masuk collection)
                         │
                         └── Berhasil
                                 │
                                 ▼
                          Ebook Ditambahkan
                                 │
                                 ▼
                             Collection
```

Validasi file memastikan file berformat **PDF**. File yang bukan PDF atau tidak valid ditolak.

Jika proses penyimpanan gagal, PDF tidak dianggap masuk ke collection.

Fase 1 tidak menyediakan offline upload queue atau pending synchronization. Jika user offline, upload dibatalkan dan user mencoba kembali ketika koneksi tersedia.

### Hasil

Ebook yang berhasil diproses menjadi bagian dari collection dan dapat digunakan dari platform yang menggunakan collection tersebut.

---

# 8. Local Ebook Discovery Flow — Android

Local discovery merupakan fitur **P2** untuk mengurangi langkah manual.

```text
Android
   │
   ▼
Buka Local Discovery
   │
   ▼
Sistem Mencari Ebook
   │
   ▼
Ebook Ditemukan?
   │
   ├── Tidak
   │    │
   │    ▼
   │  Tampilkan
   │  "Tidak ada ebook baru"
   │
   └── Ya
        │
        ▼
  Bandingkan dengan
  Collection
        │
        ▼
  Tampilkan Ebook Lokal
  yang Belum Masuk Collection
        │
        ▼
      User Memilih
        │
        ▼
   Tambah ke Koleksi
```

### Important Rule

Discovery **tidak otomatis melakukan upload**.

Flow-nya:

```text
Ebook Lokal
    ↓
Ditemukan
    ↓
Ditampilkan
    ↓
User Memilih
    ↓
User Konfirmasi
    ↓
Baru Diproses ke Collection
```

Bukan:

```text
Ebook Lokal
    ↓
Ditemukan
    ↓
Otomatis Upload
```

---

# 9. Add Local Ebook Flow

Setelah local discovery menemukan ebook, user dapat memilih ebook yang ingin dimasukkan.

```text
Local Discovery
      │
      ▼
Daftar Ebook Lokal
      │
      ▼
User Memilih Ebook
      │
      ▼
Tambah ke Koleksi
      │
      ▼
Validasi
      │
      ├── Gagal
      │     │
      │     ▼
      │  Tampilkan Error
      │
      └── Berhasil
            │
            ▼
    Internet tersedia?
            │
            ├── Tidak ──► Upload Dibatalkan
            │                 │
            │                 ▼
            │         Informasi: "Coba lagi
            │         saat online"
            │         (tidak ada pending upload)
            │
            └── Ya
                 │
                 ▼
          Proses Penyimpanan
                 │
                 ├── Gagal ──► Tampilkan Error
                 │             (PDF tidak masuk collection)
                 │
                 └── Berhasil
                         │
                         ▼
                     Collection
```

Seperti Manual Upload, Fase 1 tidak menyediakan offline upload queue atau pending synchronization.

Jika fitur duplicate prevention tersedia:

```text
Ebook Dipilih
     │
     ▼
Cek Duplicate
     │
     ├── Duplicate ──► Jangan Tambahkan Lagi
     │
     └── Bukan Duplicate
              │
              ▼
        Tambahkan ke Collection
```

Duplicate prevention termasuk **P2**, sehingga implementasinya dapat disederhanakan atau ditunda jika diperlukan untuk menjaga penyelesaian core MVP.

---

# 10. Access Ebook Flow

Fase 1 hanya mendukung **PDF**. Sistem tidak menyediakan PDF reader internal; PDF dibuka melalui viewer eksternal. Alur akses berbeda antara Android dan Web.

## 10.1 Android — Local-First

Pada Android, file lokal digunakan lebih dahulu jika masih tersedia. Cloud storage menjadi fallback dan membutuhkan koneksi internet.

```text
Collection
    │
    ▼
Pilih PDF
    │
    ▼
Buka PDF
    │
    ▼
Pemeriksaan Hak Akses
    │
    ├── Ditolak ──► Tampilkan Error
    │
    └── Diizinkan
            │
            ▼
   File lokal masih tersedia?
            │
            ├── Ya ──► Gunakan File Lokal ─────────────┐
            │                                          │
            └── Tidak                                  │
                  │                                    │
                  ▼                                    │
          Internet tersedia?                           │
                  │                                    │
                  ├── Tidak ──► Informasi:             │
                  │             "Butuh internet        │
                  │              untuk membuka PDF"    │
                  │                                    │
                  └── Ya                               │
                       │                               │
                       ▼                               │
              Ambil File dari Cloud                    │
                       │                               │
                       ├── Tidak ditemukan / gagal     │
                       │      ──► Tampilkan Error      │
                       │                               │
                       └── Berhasil ───────────────────┤
                                                       ▼
                                        Serahkan ke PDF Viewer Eksternal
                                                       │
                                  ┌────────────────────┴───────┐
                                  │                            │
                         Tidak ada aplikasi PDF         Ada aplikasi PDF
                                  │                            │
                                  ▼                            ▼
                      Informasi kepada user          User Membaca PDF
```

Hasil: PDF dibuka melalui PDF viewer eksternal, baik dari file lokal maupun dari cloud.

## 10.2 Web

Pada Web, PDF diakses melalui sistem dan dibuka menggunakan PDF viewer bawaan browser.

```text
Collection
    │
    ▼
Pilih PDF
    │
    ▼
Buka PDF
    │
    ▼
Pemeriksaan Hak Akses
    │
    ├── Ditolak / File tidak tersedia ──► Tampilkan Error
    │
    └── Diizinkan
            │
            ▼
   Buka di PDF Viewer
   Bawaan Browser
```

User hanya dapat mengakses PDF yang menjadi bagian dari collection miliknya.

---

# 11. Read / Unread Flow

## 11.1 Mark as Read

```text
Collection
    │
    ▼
Pilih Ebook
    │
    ▼
Tandai Sudah Dibaca
    │
    ▼
Status Diperbarui
    │
    ▼
Collection Menampilkan
"Sudah Dibaca"
```

Jika gagal:

```text
Status Lama
    │
    ▼
Update Gagal
    │
    ▼
Status Tetap
    │
    ▼
Tampilkan Error
```

---

## 11.2 Mark as Unread

```text
Collection
    │
    ▼
Pilih Ebook
    │
    ▼
Tandai Belum Dibaca
    │
    ▼
Status Diperbarui
    │
    ▼
Collection Menampilkan
"Belum Dibaca"
```

---

# 12. Cross-Platform Flow

Android dan Web menggunakan collection yang sama.

## 12.1 Android → Web

Contoh:

```text
Android
   │
   ▼
User Menambahkan Ebook
   │
   ▼
Laravel
   │
   ├──► File
   │     ↓
   │     R2
   │
   └──► Metadata
         ↓
         MySQL
               │
               ▼
              Web
               │
               ▼
        Ebook Tersedia
        di Collection
```

---

## 12.2 Web → Android

```text
Web
 │
 ▼
User Upload Ebook
 │
 ▼
Laravel
 │
 ├──► R2
 │
 └──► MySQL
       │
       ▼
     Android
       │
       ▼
Collection Terbaru
```

---

## 12.3 Status Read/Unread Antar Platform

```text
Android
   │
   ▼
Ubah Status
   │
   ▼
Laravel
   │
   ▼
MySQL
   │
   ▼
Web Membaca Data Terbaru
```

Hal yang sama berlaku sebaliknya:

```text
Web
 │
 ▼
Ubah Status
 │
 ▼
Laravel
 │
 ▼
MySQL
 │
 ▼
Android Membaca Data Terbaru
```

---

# 13. End-to-End Scenario — Core MVP

Skenario ini tidak bergantung pada local discovery.

```text
START
  │
  ▼
Register / Login
  │
  ▼
Collection
  │
  ▼
Manual Upload Ebook
  │
  ▼
Pilih File
  │
  ▼
Upload
  │
  ▼
Ebook Masuk Collection
  │
  ├──────────────► Akses Ebook
  │
  └──────────────► Tandai Read / Unread
                         │
                         ▼
                    Status Tersimpan
                         │
                         ▼
              Dapat Dilihat Platform Lain
```

Flow ini merupakan jalur yang harus tetap dapat berjalan walaupun P2 belum selesai.

---

# 14. End-to-End Scenario — Android Discovery

Skenario ini merupakan flow tambahan untuk mengurangi pekerjaan manual.

```text
User Download Ebook
        │
        ▼
Ebook Berada di Perangkat
        │
        ▼
Android Local Discovery
        │
        ▼
Ebook Ditemukan
        │
        ▼
User Memilih Ebook
        │
        ▼
Tambah ke Koleksi
        │
        ▼
Ebook Diproses
        │
        ▼
Collection
        │
        ▼
Web Juga Dapat Mengakses
Collection yang Sama
```

Jika discovery tidak tersedia:

```text
Android
   │
   ▼
Manual Upload
   │
   ▼
Collection
```

Dengan demikian, kegagalan atau penundaan fitur discovery tidak memutus fungsi utama aplikasi.

---

# 15. Empty State Flow

## Collection Kosong

```text
Login
  │
  ▼
Collection
  │
  ▼
Tidak Ada Ebook
  │
  ▼
Tampilkan Empty State
  │
  ▼
Tambah Ebook
```

User dapat memilih jalur:

```text
Tambah Ebook
   │
   ├──► Manual Upload
   │
   └──► Local Discovery (Android)
```

---

# 16. Error Flow Umum

Error tidak boleh membuat user kehilangan konteks aktivitas.

Pola umum:

```text
User Action
    │
    ▼
Processing
    │
    ├── Berhasil ──► Lanjut ke State Berikutnya
    │
    └── Gagal
          │
          ▼
      Tampilkan Error
          │
          ▼
     User Memperbaiki
          │
          ▼
       Coba Lagi
```

Contoh:

```text
Upload
  │
  ▼
Gagal
  │
  ▼
Tampilkan Informasi
  │
  ▼
Coba Upload Lagi
```

---

# 17. Platform-Specific Flow

## 17.1 Web

Web menggunakan Laravel Blade.

Flow utama:

```text
Login
  ↓
Collection
  ├── Upload Ebook
  ├── Akses Ebook
  └── Read / Unread
```

Web tidak menggunakan local ebook discovery karena discovery dilakukan pada penyimpanan perangkat Android.

---

## 17.2 Android

Android menggunakan Kotlin dan REST API Laravel.

Flow utama:

```text
Login
  ↓
Collection
  ├── Manual Upload
  ├── Local Discovery
  │      ↓
  │   Pilih Ebook
  │      ↓
  │   Tambah ke Collection
  │
  ├── Akses Ebook
  └── Read / Unread
```

Manual upload tetap tersedia sebagai fallback.

---

# 18. MVP Priority Flow

User Flow berdasarkan prioritas pengerjaan:

## P0 — Wajib Berjalan

```text
Register
   ↓
Login
   ↓
Collection
   ↓
Manual Upload / Ebook Storage
   ↓
Access Ebook
   ↓
Read / Unread
```

Core flow ini harus dapat digunakan tanpa local discovery.

---

## P1 — Kedua Platform

```text
             ┌──► Web Laravel Blade
             │
Core System ─┤
             │
             └──► Android Kotlin
```

Kedua platform harus menggunakan collection yang sama.

---

## P2 — Pengurangan Friksi

```text
Android
   ↓
Local Discovery
   ↓
Pilih Ebook
   ↓
Tambah ke Collection
```

P2 boleh ditunda atau disederhanakan tanpa menghentikan P0/P1.

---

# 19. Milestone Relationship

## 7 Oktober 2026 — Core MVP Functional

Target flow minimum:

```text
Register
   ↓
Login
   ↓
Collection
   ↓
Manual Upload
   ↓
Ebook Tersimpan
   ↓
Access Ebook
   ↓
Read / Unread
```

Android dan Web mulai menggunakan core system yang sama.

**Local discovery tidak menjadi syarat untuk milestone ini.**

---

## 12 Oktober 2026 — Full MVP

Target flow:

```text
                    ┌──► Web
                    │
Core System ────────┤
                    │
                    └──► Android
                           │
                           ▼
                    Local Discovery
                           │
                           ▼
                    Add Local Ebook
```

Ditambah:

- validation;
- error handling;
- shared collection testing;
- UI polish;
- end-to-end testing;
- fallback manual upload yang tetap berfungsi.

---

# 20. Complete User Journey

```text
                         START
                           │
                           ▼
                  ┌─────────────────┐
                  │ Register / Login│
                  └────────┬────────┘
                           │
                           ▼
                      Collection
                           │
             ┌─────────────┼─────────────┐
             │             │             │
             ▼             ▼             ▼
         Tambah Ebook   Akses Ebook   Read / Unread
             │
       ┌─────┴─────┐
       │           │
       ▼           ▼
 Manual Upload   Discovery
       │          Android
       │           │
       │           ▼
       │      Pilih Ebook
       │           │
       │           ▼
       │      Tambah ke
       │       Collection
       │           │
       └─────┬─────┘
             │
             ▼
        Ebook Tersimpan
             │
             ▼
     Collection Bersama
             │
       ┌─────┴─────┐
       ▼           ▼
    Android       Web
       │           │
       └─────┬─────┘
             ▼
      Read / Unread
             │
             ▼
           Logout
             │
             ▼
            END
```

---

# 21. Relationship dengan Use Case

User Flow ini menggunakan dokumen **01-Use-Case** sebagai dasar penomoran:

| User Flow            | Use Case | Prioritas |
| -------------------- | -------- | --------- |
| Register             | UC-01    | P0        |
| Login                | UC-02    | P0        |
| Logout               | UC-03    | P0        |
| View Collection      | UC-04    | P0        |
| Discover Local Ebook | UC-05    | P2        |
| Add Local Ebook      | UC-06    | P2        |
| Manual Upload        | UC-07    | P0        |
| Access Ebook         | UC-08    | P0        |
| Mark as Read         | UC-09    | P0        |
| Mark as Unread       | UC-10    | P0        |

> Catatan: Penomoran mengikuti dokumen 01-Use-Case. Nomor UC tidak mencerminkan prioritas pengerjaan; prioritas ditentukan oleh label P0/P1/P2 pada dokumen 00-Project-Overview. Jika penomoran pada 01-Use-Case berubah, tabel ini harus diperbarui.

---

# 22. Boundary

User Flow ini tidak mencakup fitur di luar MVP, seperti:

- Categories;
- Tags;
- Complex Search;
- Bookmarks;
- Reading Progress;
- Ratings;
- Statistics;
- Sharing;
- Recommendations;
- Social Features;
- Admin Dashboard;
- Advanced Duplicate Detection;
- Automatic Background Synchronization;
- Automatic Upload setiap ebook yang terdeteksi.
  Fitur-fitur tersebut tidak menjadi bagian dari alur pengguna MVP.

---

# 23. Kesimpulan

User Flow sistem memiliki dua jalur utama untuk memasukkan ebook:

```text
                    Tambah Ebook
                         │
             ┌───────────┴───────────┐
             │                       │
             ▼                       ▼
       Manual Upload          Local Discovery
             │                    Android
             │                       │
             │                 User memilih
             │                       │
             └──────────┬────────────┘
                        ▼
                   Collection
                        │
              ┌─────────┴─────────┐
              ▼                   ▼
         Access Ebook        Read / Unread
```

**Manual Upload adalah jalur yang menjaga core system tetap berjalan. Local Discovery adalah jalur untuk mengurangi pekerjaan manual pengguna.**

Dengan struktur ini, sistem tidak bergantung pada fitur discovery untuk menyelesaikan fungsi utamanya, tetapi tetap memiliki jalur yang lebih praktis ketika discovery sudah tersedia.
