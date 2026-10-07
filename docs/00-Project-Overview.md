# Project Overview

## 1. Project Information

| Item                | Detail                           |
| ------------------- | -------------------------------- |
| Project Name        | Ebook Storage & To-Do List       |
| Type                | Personal Ebook Management System |
| Platform            | Android & Web                    |
| Android Technology  | Kotlin                           |
| Web Technology      | Laravel Blade                    |
| Backend             | Laravel REST API                 |
| Database            | MySQL                            |
| Cloud Storage       | Cloudflare R2                    |
| Core MVP Functional | 7 October 2026                   |
| Full MVP            | 12 October 2026                  |

### 1.1 Hierarki Dokumen

Proyek ini memiliki enam dokumen perancangan: 00 (Project Overview, dokumen ini), 01 (Use Case), 02 (User Flow), 03 (Rancangan Database), 04 (API Specification), dan 05 (Backlog dan Jadwal Chunking). Bila isi dua dokumen berbeda, aturan berikut menentukan dokumen yang berlaku.

1. **Dokumen 00 adalah acuan tertinggi** untuk scope, prioritas (P0/P1/P2), milestone, dan keputusan arsitektur tingkat proyek.
2. **Detail yang didelegasikan oleh dokumen 00.** Untuk detail API (format request dan respons, validasi, kode error), dokumen 04 yang berlaku (lihat bagian 12). Untuk detail skema database (kolom, tipe data, constraint), dokumen 03 yang berlaku (lihat bagian 13). Bila dokumen 00 berbeda dari dokumen 03 atau 04 pada detail tersebut, dokumen 03 atau 04 yang berlaku dan dokumen 00 diperbarui. Bila perbedaan menyangkut scope, prioritas, atau milestone, dokumen 00 tetap yang menentukan.
3. **Dokumen 01 dan 02 berada di bawah dokumen 00.** Dokumen 01 menjelaskan perilaku sistem per use case dan dokumen 02 menjelaskan alur pengguna. Bila keduanya berbeda dari dokumen 00, dokumen 00 yang berlaku dan keduanya disesuaikan, kecuali dokumen 00 sengaja diubah.
4. **Dokumen 05 hanya berisi jadwal dan backlog.** Dokumen 05 tidak menentukan isi desain; bila berbeda dari dokumen lain, dokumen 05 yang disesuaikan.
5. Setiap perubahan isi dokumen akibat aturan ini dicatat pada Log Perubahan Rancangan (dokumen 05 bagian 9).

---

## 2. Background

Pengguna dapat memiliki banyak ebook yang berasal dari berbagai sumber
dan tersimpan di perangkat. Seiring bertambahnya koleksi, pengguna dapat
kesulitan mengetahui ebook apa saja yang dimiliki, di mana ebook
tersebut berada, dan ebook mana yang sudah atau belum dibaca.

Masalahnya bukan hanya lokasi file, tetapi juga tidak adanya **koleksi
ebook terpusat** yang dapat digunakan untuk mengelola ebook dan status
bacanya.

Sistem harus mengurangi masalah tersebut tanpa membuat pengguna
melakukan terlalu banyak langkah tambahan.

---

## 3. Problems

Masalah utama yang ingin diselesaikan:

- Ebook dapat tersebar di berbagai lokasi penyimpanan perangkat.
- Pengguna lupa ebook apa saja yang sudah dimiliki.
- Sulit mengetahui ebook mana yang sudah atau belum dibaca.
- Koleksi ebook di perangkat belum tersedia secara terpusat.
- Koleksi di satu perangkat tidak otomatis tersedia di perangkat lain.
- Proses memasukkan ebook ke sistem secara manual berpotensi menjadi
  pekerjaan tambahan.

### Design Principle

Sistem tidak boleh hanya memindahkan masalah dari:

> "Saya tidak tahu ebook saya ada di mana."

menjadi:

> "Saya tahu ebook saya ada di aplikasi, tetapi saya harus upload
> semuanya secara manual."

Karena itu, Android perlu memiliki pendekatan **local ebook discovery**
sebagai bagian dari solusi.

---

## 4. Proposed Solution

Membangun sistem **Ebook Storage & To-Do List** yang menyediakan koleksi
ebook pribadi terpusat melalui Android dan Web.

Untuk **Fase 1 / MVP, sistem hanya mendukung file PDF**. Sistem tidak
menjadi PDF reader; proses membaca diserahkan kepada PDF viewer
eksternal yang tersedia pada perangkat atau browser.

Konsep utama:

1.  Android dapat menemukan file PDF yang sudah tersedia di perangkat,
    terutama dari lokasi seperti folder Download.
2.  User dapat memilih PDF yang ingin dimasukkan ke koleksi.
3.  PDF yang dipilih diunggah ke Cloudflare R2 ketika perangkat memiliki
    koneksi internet.
4.  Laravel menyimpan metadata PDF dan status baca di MySQL.
5.  Koleksi yang sama dapat diakses melalui Android maupun Web.
6.  User dapat menandai PDF sebagai sudah atau belum dibaca.
7.  Saat membuka PDF di Android, sistem memprioritaskan file lokal jika
    masih tersedia; jika tidak tersedia, sistem menggunakan file dari
    cloud dan membutuhkan koneksi internet.
8.  PDF dibuka menggunakan PDF viewer eksternal, bukan reader internal
    yang dibuat oleh sistem.
9.  Jika user mencoba upload ketika tidak memiliki koneksi internet,
    proses upload dibatalkan dan user perlu mencoba kembali.
10. User tidak harus selalu mencari file secara manual melalui file
    picker ketika PDF sudah dapat ditemukan oleh aplikasi.

### Simple User Experience

```text
Download PDF
      ↓
Android mendeteksi PDF baru
      ↓
"3 PDF ditemukan"
      ↓
User pilih PDF
      ↓
[ Tambahkan ke Koleksi ]
      ↓
Jika online → Laravel
Jika offline → Upload dibatalkan
      ↓
Laravel
      ├── Metadata → MySQL
      └── File → Cloudflare R2
      ↓
PDF tersedia di Android + Web
```

---

## 5. System Architecture

Arsitektur utama:

```text
                       ┌─────────────────┐
                       │  Cloudflare R2  │
                       │   Ebook Files   │
                       └────────▲────────┘
                                │
                            File Storage
                                │
┌────────────────┐       ┌──────┴───────┐       ┌────────────────┐
│ Android Kotlin │──────►│    Laravel   │◄──────│  Web Laravel   │
│                │ REST  │   Backend    │       │     Blade      │
└───────┬────────┘ API   └──────┬───────┘       └────────────────┘
        │                       │
        │ Local Discovery       ▼
        │                ┌─────────────┐
        │                │    MySQL    │
        │                │  Metadata   │
        │                │ Read Status │
        │                └─────────────┘
        │
        ▼
   Device Storage
   └── Download/
       ├── Ebook A.pdf
       ├── Ebook B.pdf
       └── Ebook C.pdf
```

### 5.1 Explicit Data Flow

Laravel merupakan **pusat akses data dan business logic**. Android dan
Web tidak mengakses MySQL atau Cloudflare R2 secara langsung.

```text
Android Kotlin
      │
      │ REST API
      ▼
┌──────────────────┐
│ Laravel Backend  │
│                  │
│ Authentication   │
│ Business Logic   │
│ Validation       │
│ Authorization    │
└───────┬──────────┘
        │
   ┌────┴───────────────┐
   │                    │
   ▼                    ▼
┌─────────────┐   ┌───────────────┐
│    MySQL    │   │ Cloudflare R2 │
│             │   │               │
│ User        │   │ Ebook Files   │
│ Ebook       │   │               │
│ Read Status │   │               │
└─────────────┘   └───────────────┘

Web Laravel Blade
      │
      │ Laravel internal
      ▼
┌──────────────────┐
│ Laravel Backend  │
└───────┬──────────┘
        │
   ┌────┴───────────────┐
   ▼                    ▼
 MySQL              Cloudflare R2
```

**Aturan utama:**

- Android berkomunikasi dengan Laravel melalui REST API.
- Web menggunakan Laravel secara langsung melalui
  Blade/route/controller.
- Android tidak terhubung langsung ke MySQL.
- Web tidak terhubung langsung ke MySQL dari sisi client.
- Android dan Web tidak terhubung langsung ke Cloudflare R2 untuk
  operasi data utama.
- Laravel menjadi penghubung antara platform, database, dan cloud
  storage.
- MySQL menjadi sumber utama metadata dan status baca.
- Cloudflare R2 menjadi sumber utama file ebook.

Dengan pola ini, Android dan Web tidak memiliki database collection
masing-masing. Keduanya menggunakan data collection yang sama melalui
Laravel.

### Architecture Responsibilities

> Catatan: detail arsitektur PDF access akan diperjelas pada dokumen
> arsitektur terpisah. Bagian ini hanya menetapkan tanggung jawab
> tingkat proyek.

**Android Kotlin**

- Login/register.
- Menampilkan koleksi ebook.
- Mendeteksi file ebook yang tersedia di perangkat.
- Menampilkan ebook yang belum masuk koleksi.
- Memilih ebook untuk ditambahkan.
- Upload ebook melalui REST API.
- Menampilkan status baca.
- Mengubah status baca.
- Mengakses dan membuka PDF.
- Menggunakan file lokal jika tersedia untuk akses offline.
- Menggunakan file cloud jika file lokal tidak tersedia dan koneksi
  internet tersedia.

**Laravel**

- Authentication.
- Web application.
- REST API.
- Business logic.
- Validasi request dan file.
- Pengelolaan database.
- Pengelolaan cloud storage.
- Authorization dan ownership checking.

**Laravel Blade**

- Login/register.
- Daftar ebook.
- Upload ebook manual.
- Read/unread status.
- Akses dan buka PDF melalui browser PDF viewer.

**MySQL**

- Data pengguna.
- Metadata ebook.
- Status baca.
- Relasi pengguna dengan ebook.

**Cloudflare R2**

- Menyimpan file ebook sebenarnya.
- Object storage utama.
- Bucket private.
- File diakses melalui mekanisme yang dikontrol Laravel.

**Android Device Storage**

- Sumber awal PDF lokal.
- Digunakan untuk proses discovery.
- Menjadi sumber akses lokal/offline jika file PDF masih tersedia di
  perangkat.
- Tidak menjadi sumber data collection terpusat.

---

## 6. Cloud Storage

### Storage Choice

Cloud storage yang digunakan adalah **Cloudflare R2**.

Alasan utama:

- Free tier sesuai untuk MVP kecil.
- 10 GB-month free storage.
- S3-compatible API.
- Cocok untuk integrasi Laravel.
- Tidak mengenakan biaya egress Internet pada model R2.
- Mendukung private object storage.
- Cocok untuk file seperti PDF/ebook.

### Storage Principle

Cloud storage bukan dimaksudkan untuk membuat user wajib mengupload
semua file secara manual.

Fungsinya adalah:

> **Menyediakan penyimpanan terpusat agar ebook yang sudah dipilih masuk
> ke koleksi yang dapat digunakan lintas perangkat.**

### File Storage

MySQL menyimpan metadata ebook pada tabel `ebooks`: judul, lokasi file
di R2, hash file untuk pencegahan duplikat, status baca, dan timestamp.
Rincian kolom ada pada dokumen **03-Rancangan-Database-Ebook-Storage**.

---

## 6.1 Source of Truth

Setiap jenis data memiliki sumber utama yang jelas:

| Data                                            | Source of Truth        |
| ----------------------------------------------- | ---------------------- |
| User                                            | MySQL                  |
| Ebook metadata                                  | MySQL                  |
| Read/unread status                              | MySQL                  |
| Ebook file                                      | Cloudflare R2          |
| Ebook lokal yang belum dimasukkan ke collection | Android Device Storage |
| File PDF lokal untuk akses offline              | Android Device Storage |
| Ebook yang sudah masuk collection               | MySQL + Cloudflare R2  |

Android Device Storage menjadi sumber awal untuk **local ebook
discovery** dan dapat tetap digunakan untuk membuka PDF secara offline
selama file lokal masih tersedia. Setelah PDF berhasil dimasukkan ke
collection, data terpusat berada pada Laravel, MySQL, dan Cloudflare R2.

Penting: menemukan file di Android **tidak otomatis berarti** file
tersebut sudah menjadi bagian dari collection. User tetap menentukan
ebook yang akan dimasukkan.

---

## 7. Ebook Discovery

### Purpose

Fitur discovery dibuat untuk mengurangi ekstra step dari sistem.

Tanpa discovery:

```text
Download
↓
Buka aplikasi
↓
Cari file
↓
Pilih file
↓
Upload
```

Dengan discovery:

```text
Download
↓
Aplikasi mendeteksi ebook
↓
"3 ebook baru ditemukan"
↓
Pilih / Tambahkan
```

### Initial Discovery Scope

Untuk MVP, Android dapat memprioritaskan pencarian ebook pada lokasi
yang mudah dipahami user, misalnya folder Download dan lokasi file yang
dapat diakses aplikasi sesuai permission/storage model Android.

Discovery tidak berarti semua file langsung di-upload.

User tetap menentukan ebook mana yang akan masuk ke cloud collection.

### Duplicate Handling

Jika file sudah pernah dimasukkan ke collection, aplikasi sebaiknya
tidak menampilkannya sebagai ebook baru secara terus-menerus.

Implementasi duplicate detection dapat dimulai sederhana dan
dikembangkan setelah fitur utama selesai.

---

## 8. Core Concept

Sistem memiliki satu koleksi PDF pribadi yang digunakan bersama oleh
Android dan Web.

Fase 1 berfokus pada **penyimpanan, pengelolaan, akses, dan status baca
PDF**. Sistem tidak menyediakan PDF reader internal.

### PDF Access Principle

Pada Android, akses PDF mengikuti prioritas berikut:

```text
User memilih PDF
      ↓
Apakah file lokal masih tersedia?
      ├── Ya → Buka file lokal → PDF Viewer Eksternal
      │
      └── Tidak → Akses file Cloud
                         ↓
                    Butuh internet
                         ↓
                    PDF Viewer Eksternal
```

Jika user berada dalam kondisi offline saat ingin membuka PDF yang hanya
tersedia di cloud, PDF tidak dapat diakses sampai koneksi tersedia.

Contoh:

```text
Android Device
      ↓
Clean Code.pdf ditemukan
      ↓
User memilih "Tambah ke Koleksi"
      ↓
Laravel
      ├── File → R2
      └── Metadata → MySQL
      ↓
My Ebook Collection
      ├── Clean Code
      ├── Atomic Habits
      └── Kotlin Basics
              │
        ┌─────┴─────┐
        ↓           ↓
     Android       Web
```

Jika status diubah dari Web:

```text
Web
 ↓
is_read = true
 ↓
MySQL
 ↓
Android
 ↓
Clean Code = Sudah Dibaca
```

Android dan Web menggunakan koleksi yang sama.

---

## 9. Actors

### User

Satu-satunya actor untuk MVP.

User dapat:

- Register.
- Login.
- Menemukan PDF lokal melalui Android.
- Memilih PDF untuk dimasukkan ke koleksi.
- Upload PDF manual.
- Melihat koleksi PDF.
- Membuka PDF melalui PDF viewer eksternal.
- Menandai ebook sudah dibaca.
- Mengubah kembali menjadi belum dibaca.

Admin khusus tidak diperlukan untuk MVP.

---

## 10. MVP Scope

MVP dibagi menjadi tiga tingkat prioritas agar batas fitur yang wajib
selesai dan fitur yang dapat ditunda tetap jelas.

### P0 --- Core MVP

P0 adalah fungsi inti yang **wajib berjalan**. Jika salah satu fungsi P0
belum berjalan, sistem belum dianggap memiliki MVP yang fungsional.

#### Authentication

- Register.
- Login.
- Logout.
- Authentication pada Android.
- Authentication pada Web.

#### PDF Collection

- Membuat/memasukkan PDF ke collection.
- Menampilkan daftar PDF.
- Menampilkan informasi dasar PDF.
- Membuka PDF melalui PDF viewer eksternal.
- Pada Android, menggunakan file lokal jika tersedia.

#### PDF Upload & Storage

- Upload PDF ketika koneksi internet tersedia.
- Menyimpan metadata PDF di MySQL.
- Menyimpan file PDF di Cloudflare R2.
- File hanya dapat diakses oleh user yang memiliki hak akses.

#### Reading Checklist

Status sederhana:

```text
[ ] Belum dibaca
[x] Sudah dibaca
```

- Mengubah status menjadi sudah dibaca.
- Mengubah status kembali menjadi belum dibaca.

#### Backend & API

- Laravel berjalan sebagai backend pusat.
- Database MySQL berjalan.
- REST API dasar berjalan.
- Business logic dan authorization dasar berjalan.

### P1 --- Platform MVP

P1 memastikan core system benar-benar dapat digunakan dari kedua
platform.

#### Web Laravel Blade

- Login/register.
- Menampilkan collection.
- Manual upload PDF.
- Read/unread status.
- Membuka PDF melalui browser PDF viewer.

#### Android Kotlin

- Login/register.
- Terhubung ke REST API.
- Menampilkan collection.
- Menambahkan PDF.
- Manual upload sebagai fallback.
- Read/unread status.
- Membuka PDF melalui PDF viewer eksternal.
- Menggunakan file lokal sebagai fallback akses offline jika tersedia.

#### Shared Data

- Android dan Web menggunakan collection yang sama.
- Perubahan status dari satu platform dapat dilihat dari platform
  lainnya.

### P2 --- Friction Reduction

P2 meningkatkan pengalaman penggunaan, tetapi **tidak boleh menghambat
penyelesaian P0 dan P1**.

#### Local Ebook Discovery --- Android

- Scan/discovery ebook lokal.
- Menampilkan ebook yang belum masuk collection.
- Memilih ebook.
- Menambahkan ebook ke collection.

#### Duplicate Prevention

- Mencegah ebook yang sudah masuk collection terus muncul sebagai
  ebook baru.
- Implementasi dapat dimulai secara sederhana.

Local discovery merupakan fitur untuk mengurangi langkah manual, bukan
dependency bagi core system. Jika discovery belum stabil, Android tetap
harus dapat menggunakan file picker sebagai fallback.

### MVP Cut Line

Jika waktu pengembangan tidak mencukupi:

```text
WAJIB SELESAI
─────────────
P0 — Core MVP

HARUS ADA, BOLEH SEDERHANA
───────────────────────────
P1 — Platform MVP

BOLEH DITUNDA / DISEDERHANAKAN
───────────────────────────────
P2 — Friction Reduction
```

Dengan demikian, fitur tambahan tidak boleh mengorbankan fungsi inti
sistem.

---

## 11. Platform Responsibilities

### Android Kotlin

Android berkomunikasi dengan Laravel melalui REST API.

Fitur P0/P1:

- Register.
- Login.
- PDF list.
- Manual PDF upload.
- Read/unread status.
- Membuka PDF melalui PDF viewer eksternal.
- Menggunakan file lokal untuk akses offline jika tersedia.

Fitur P2 (boleh ditunda atau disederhanakan):

- Local PDF discovery.
- Add local PDF to collection.

### Web Laravel Blade

Web menggunakan Laravel secara langsung.

Fitur MVP:

- Register.
- Login.
- PDF list.
- Manual PDF upload.
- Read/unread status.
- Membuka PDF melalui browser PDF viewer.

Web tidak perlu menggunakan REST API untuk komunikasi internal dengan
Laravel.

### Backend Laravel

Laravel menjadi pusat sistem:

- Authentication.
- REST API.
- Web application.
- Business logic.
- Database operations.
- File storage operations.
- Authorization.
- Validation.

---

## 12. API Specification

> Kontrak API yang lengkap (format request dan respons, validasi, kode
> error, aturan kepemilikan, dan konfigurasi) didefinisikan pada dokumen
> **04-API-Specification**. Bagian ini hanya memberi ringkasan tingkat
> proyek. Jika terdapat perbedaan, dokumen 04 menjadi acuan untuk detail
> API.

Ringkasan endpoint (7 endpoint):

```text
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/ebooks
POST   /api/ebooks
GET    /api/ebooks/{id}/file
PATCH  /api/ebooks/{id}
```

Endpoint dapat dikembangkan setelah implementasi dasar selesai.

Local discovery Android pada dasarnya terjadi di perangkat, kemudian
file yang dipilih dikirim ke endpoint upload.

---

## 12.1 API → Database & Storage Flow

Endpoint API bukan hanya daftar URL, tetapi menjadi jalur operasi
Android terhadap Laravel.

Diagram berikut hanya menggambarkan alur tingkat proyek. Urutan proses
internal yang rinci (hash file, pemeriksaan duplikat, transaksi, dan
compensating action) terdapat pada 04-API-Specification bagian 5.

Untuk Fase 1, operasi upload PDF membutuhkan koneksi internet. Tidak ada
mekanisme pending upload atau offline queue.

### Login

```text
Android
   ↓
POST /api/login
   ↓
Laravel
   ↓
MySQL
   ↓
Authentication result
   ↓
Android
```

### Menampilkan Collection

```text
Android
   ↓
GET /api/ebooks
   ↓
Laravel
   ↓
MySQL
   ↓
Ebook metadata + read status
   ↓
JSON response
   ↓
Android
```

### Menambahkan / Upload PDF

```text
Android / Web
      ↓
Laravel
      │
      ├── Validate user + file
      │
      ├──────────────► Cloudflare R2
      │                  └── Ebook file
      │
      └──────────────► MySQL
                         └── Ebook metadata
```

### Mengubah Read/Unread

```text
Android / Web
      ↓
Laravel
      ↓
MySQL
      ↓
ebooks.is_read
      ↓
Android / Web
```

Dengan demikian, perubahan data dari salah satu platform akan tersedia
bagi platform lainnya karena keduanya menggunakan sumber data yang sama.

---

## 13. Database Design

Database menggunakan tiga tabel: `users`, `ebooks`, dan
`ebook_status_history`. Rincian kolom, tipe data, dan constraint
tercantum pada dokumen **03-Rancangan-Database-Ebook-Storage**, yang
menjadi acuan untuk skema database.

### Relationship

```text
User 1 ──────── * Ebook 1 ──────── * Ebook Status History
```

Satu user dapat memiliki banyak ebook.

Setiap ebook hanya dimiliki oleh satu user dalam MVP. Satu user tidak boleh memiliki dua ebook dengan isi file yang sama;
duplikat ditolak (lihat 03 dan 04).

Satu ebook dapat memiliki banyak catatan perubahan status. Catatan
tersebut dibuat otomatis oleh Trigger ketika `ebooks.is_read` berubah.

---

## 13.1 End-to-End Example

Contoh ketika user menambahkan `Clean Code.pdf` dari Android:

```text
1. Android memperoleh Clean Code.pdf (via file picker atau local discovery)
                ↓
2. User memilih "Tambah ke Koleksi"
                ↓
3. Android mengirim file + metadata melalui REST API
                ↓
4. Laravel memvalidasi user dan file
                ↓
5. Laravel menyimpan file ke Cloudflare R2
                ↓
6. Laravel menyimpan metadata ke MySQL
                ↓
7. Laravel mengembalikan response ke Android
                ↓
8. Android menampilkan Clean Code dalam collection
                ↓
9. User membuka Web
                ↓
10. Web meminta collection melalui Laravel
                ↓
11. Laravel mengambil metadata dari MySQL
                ↓
12. Clean Code muncul di Web
```

Contoh tersebut menunjukkan bahwa Android dan Web tidak melakukan
sinkronisasi database satu sama lain. Keduanya menggunakan collection
yang sama melalui Laravel.

### Shared Data Model

```text
                    MySQL
                ebooks table
                     │
             ┌───────┴───────┐
             │               │
          Android           Web
             │               │
             └──── Laravel ──┘
```

Jika Android mengubah `is_read` menjadi `true`, Web akan membaca nilai
yang sama dari MySQL. Sebaliknya, perubahan dari Web juga dapat dibaca
oleh Android melalui REST API.

---

## 13.1 Database Programming Requirements

Selain struktur tabel dan operasi CRUD, sistem memiliki requirement
database programming yang wajib diimplementasikan menggunakan MySQL.

Requirement ini tidak dibuat sebagai fitur terpisah, tetapi harus
mendukung proses bisnis yang memang terjadi di dalam sistem.

### Database Requirement Summary

| Requirement          | Fungsi dalam Sistem                                                                          | Target Implementasi                                                                            |
| -------------------- | -------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| **Stored Procedure** | Menjalankan operasi database yang memiliki alur terdefinisi, terutama perubahan status ebook | `sp_mark_ebook_read` / `sp_mark_ebook_unread`                                                  |
| **Trigger**          | Menjalankan aksi otomatis ketika terjadi perubahan data tertentu                             | Trigger AFTER UPDATE pada `ebooks` yang mencatat perubahan `is_read` ke `ebook_status_history` |
| **Function**         | Menghasilkan nilai berdasarkan data database yang dapat digunakan oleh sistem                | `fn_count_read_ebooks(user_id)`                                                                |
| **COMMIT**           | Menetapkan perubahan database setelah transaksi berhasil                                     | Perubahan status baca (utama) dan penyimpanan metadata ebook                                   |
| **ROLLBACK**         | Membatalkan perubahan database ketika transaksi gagal                                        | Perubahan status baca (utama) dan penyimpanan metadata ebook                                   |

---

### 13.1.1 Stored Procedure

Stored procedure digunakan untuk operasi database yang memiliki proses
terdefinisi dan dapat dipanggil kembali.

Contoh penggunaan:

```text
sp_mark_ebook_read(user_id, ebook_id)
        ↓
Validasi ownership
        ↓
UPDATE ebooks
SET is_read = true
        ↓
Selesai
```

Procedure harus memastikan bahwa ebook yang diubah memang dimiliki oleh
user yang melakukan operasi.

Procedure lain dapat digunakan untuk mengembalikan status menjadi
unread.

Procedure tidak melakukan INSERT ke `ebook_status_history`; pencatatan
riwayat dilakukan oleh Trigger.

> Detail nama procedure dan implementasi final dapat disesuaikan saat
> database mulai diimplementasikan.

### 13.1.2 Trigger

Trigger digunakan untuk menjalankan proses otomatis setelah atau sebelum
perubahan tertentu pada database.

Trigger utama yang direncanakan berkaitan dengan perubahan status baca
ebook.

Contoh konsep:

```text
ebooks.is_read berubah
        ↓
Trigger berjalan
        ↓
Mencatat / memproses perubahan
```

Trigger tidak digunakan untuk menggantikan business logic Laravel.
Trigger hanya digunakan untuk kebutuhan database yang memang cocok
dilakukan secara otomatis oleh MySQL.

Trigger AFTER UPDATE pada `ebooks` mencatat setiap perubahan `is_read`
ke tabel `ebook_status_history`. Trigger hanya membuat record ketika
nilai `is_read` benar-benar berubah.

### 13.1.3 Function

Stored function digunakan untuk menghasilkan nilai dari data database.

Contoh:

```text
fn_count_read_ebooks(user_id)
        ↓
Menghitung ebook user
yang memiliki is_read = true
        ↓
Mengembalikan jumlah ebook
```

Function tersebut dapat digunakan untuk kebutuhan informasi collection
atau statistik sederhana tanpa menambahkan fitur statistik kompleks ke
dalam MVP.

### 13.1.4 Transaction --- COMMIT

COMMIT digunakan untuk menetapkan perubahan database setelah seluruh
operasi dalam transaksi berhasil.

Contoh alur:

```text
START TRANSACTION
        ↓
Validasi data
        ↓
Simpan metadata ebook
        ↓
Operasi database berhasil
        ↓
COMMIT
```

Dengan demikian, perubahan database dianggap berhasil hanya setelah
seluruh operasi database yang diperlukan selesai.

Transaksi digunakan pada dua operasi: perubahan status baca dan
penyimpanan metadata ebook. Ringkasannya ada pada bagian 13.1.5.

### 13.1.5 Transaction --- ROLLBACK

ROLLBACK digunakan ketika operasi database dalam transaksi mengalami
kegagalan.

Contoh:

```text
START TRANSACTION
        ↓
Simpan metadata ebook
        ↓
Terjadi error
        ↓
ROLLBACK
        ↓
Perubahan database dibatalkan
```

Hal ini mencegah kondisi ketika sebagian data berhasil tersimpan
sementara bagian lainnya gagal.

#### Operasi yang Menggunakan Transaksi

| Operasi                  | Isi transaksi                                                                                                | Jika berhasil | Jika gagal                                                        |
| ------------------------ | ------------------------------------------------------------------------------------------------------------ | ------------- | ----------------------------------------------------------------- |
| Ubah status baca (utama) | CALL `sp_mark_ebook_read` / `sp_mark_ebook_unread` → UPDATE `ebooks` → Trigger INSERT `ebook_status_history` | COMMIT        | ROLLBACK; perubahan status dan riwayat sama-sama dibatalkan       |
| Simpan ebook (upload)    | INSERT `ebooks` setelah file berhasil disimpan di R2                                                         | COMMIT        | ROLLBACK, lalu Laravel menghapus file di R2 (compensating action) |

Perubahan status baca ditetapkan sebagai demonstrasi utama COMMIT dan
ROLLBACK karena operasi tersebut memuat lebih dari satu penulisan
(UPDATE oleh procedure dan INSERT riwayat oleh Trigger) yang harus
berhasil atau gagal bersama.

ROLLBACK hanya membatalkan perubahan MySQL. Penghapusan file di R2
merupakan compensating action oleh Laravel, bukan bagian dari ROLLBACK
(lihat 13.1.6).

### 13.1.6 Database Transaction vs Cloud Storage

Cloudflare R2 berada di luar transaction MySQL.

Karena itu:

```text
Laravel
   │
   ├── MySQL
   │     └── Transaction → COMMIT / ROLLBACK
   │
   └── Cloudflare R2
         └── File Storage
```

COMMIT dan ROLLBACK hanya menjamin konsistensi transaksi database MySQL.
Keduanya tidak secara otomatis membatalkan file yang telah berhasil
dikirim ke R2.

Laravel harus menangani kondisi kegagalan antara database dan cloud
storage secara eksplisit.

Contoh prinsip:

```text
Upload file
    ↓
R2 berhasil
    ↓
Simpan metadata ke MySQL
    ↓
MySQL berhasil
    ↓
COMMIT
```

Jika penyimpanan metadata gagal setelah file berhasil di-upload, Laravel
harus menangani file tersebut agar tidak menjadi file yatim di R2.

### 13.1.7 Requirement Integration

Kelima requirement database harus terhubung dengan alur sistem:

```text
Android / Web
      ↓
Laravel
      ↓
Business Logic
      ↓
MySQL
      ├── Stored Procedure
      ├── Trigger
      ├── Function
      └── Transaction
            ├── COMMIT
            └── ROLLBACK
```

Tujuan requirement ini adalah memastikan database tidak hanya berfungsi
sebagai tempat penyimpanan data, tetapi juga memiliki mekanisme database
programming yang relevan dengan proses sistem.

---

## 15. Offline Upload Behavior

Untuk Fase 1, upload PDF **tidak mendukung offline queue atau pending
synchronization**.

Jika user mencoba menambahkan PDF ketika tidak memiliki koneksi
internet:

```text
Pilih PDF lokal
      ↓
Tidak ada koneksi internet
      ↓
Upload dibatalkan
      ↓
Tampilkan informasi kepada user
      ↓
User mencoba kembali ketika online
```

File lokal tetap dapat dibuka secara offline menggunakan PDF viewer
eksternal selama file tersebut masih tersedia di perangkat.

---

## 15.1 Alternative Android Approach

Jika local discovery ternyata terlalu kompleks untuk target MVP,
aplikasi tetap dapat menyediakan file picker sebagai fallback.

File picker juga hanya dapat menyelesaikan proses upload ketika koneksi
internet tersedia.

Alur fallback:

```text
Android
  ↓
Pilih file secara manual
  ↓
Upload
  ↓
Laravel
  ↓
R2 + MySQL
```

Dengan demikian local discovery menjadi fitur untuk **mengurangi
friction**, bukan dependency yang dapat menghambat seluruh sistem.

---

## 16. MVP Feature Table

| Feature             | Prioritas | Android | Web | Laravel | Database | Cloud Storage |
| ------------------- | --------- | ------- | --- | ------- | -------- | ------------- |
| Register            | P0        | ✓       | ✓   | ✓       | ✓        | -             |
| Login               | P0        | ✓       | ✓   | ✓       | ✓        | -             |
| Manual PDF Upload   | P0/P1     | ✓       | ✓   | ✓       | ✓        | ✓             |
| PDF List            | P0        | ✓       | ✓   | ✓       | ✓        | -             |
| Read/Unread         | P0        | ✓       | ✓   | ✓       | ✓        | -             |
| PDF File Access     | P0        | ✓       | ✓   | ✓       | -        | ✓             |
| Shared Data         | P1        | ✓       | ✓   | ✓       | ✓        | ✓             |
| Local PDF Discovery | P2        | ✓       | -   | -       | -        | -             |
| Add Local PDF       | P2        | ✓       | -   | ✓       | ✓        | ✓             |

---

## 17. Out of MVP

Fase 1 hanya mendukung **PDF**. Format ebook lain tidak termasuk dalam
scope.

Fitur berikut tidak menjadi prioritas:

- EPUB dan format ebook selain PDF.
- Internal PDF reader.
- Categories.
- Tags.
- Complex search.
- Bookmarks.
- Reading progress.
- Ratings.
- Statistics.
- Sharing ebook.
- Recommendations.
- Social features.
- Admin dashboard.
- Advanced duplicate detection.
- Automatic background synchronization.
- Offline upload queue / pending upload.
- Automatic upload of every detected ebook.
- PDF annotations, highlights, dan fitur reader lanjutan.

Fitur tersebut dapat dipertimbangkan setelah MVP selesai.

---

## 18. Constraints

Beberapa batasan proyek:

- Waktu pengembangan singkat.
- Pengembangan dilakukan oleh satu developer.
- Android dan Web harus sama-sama berfungsi.
- Backend harus dapat digunakan oleh kedua platform.
- Data harus tersinkronisasi.
- Cloud storage harus tetap sederhana untuk diintegrasikan.
- UI tidak perlu terlalu kompleks pada tahap MVP.
- Local discovery tidak boleh menghambat fitur inti.
- Upload membutuhkan koneksi internet pada Fase 1.
- Tidak ada offline synchronization pada Fase 1.
- Pembacaan offline hanya berlaku jika file PDF lokal masih tersedia
  di Android.

### Data Synchronization Rule

Sinkronisasi tidak dilakukan dengan membuat database terpisah untuk
Android dan Web.

Model yang digunakan adalah:

```text
Android ──REST API──► Laravel ──► MySQL
                         │
                         └──────► Cloudflare R2

Web ────────────────► Laravel ──► MySQL
                         │
                         └──────► Cloudflare R2
```

Dengan demikian:

- MySQL menjadi sumber data collection dan status baca.
- R2 menjadi sumber file ebook.
- Laravel menjadi satu-satunya pusat business logic dan akses data.
- Android dan Web membaca serta mengubah sumber data yang sama.

---

### Prioritas Utama

1.  Backend berjalan.
2.  Database berjalan.
3.  Authentication berjalan.
4.  PDF dapat dimasukkan ke collection.
5.  PDF dapat ditampilkan.
6.  PDF dapat dibuka melalui PDF viewer eksternal.
7.  Status baca dapat diubah.
8.  Android dan Web menggunakan data yang sama.
9.  File PDF dapat disimpan dan diakses dengan aman.
10. Local discovery mengurangi langkah manual jika memungkinkan.

---

## 19. Development Target

Target pengembangan tidak lagi didefinisikan hanya berdasarkan
persentase progress, tetapi berdasarkan **tingkat fungsi yang sudah
dapat digunakan**.

### 7 October 2026 --- Core MVP Functional

Pada tanggal ini, sistem harus sudah memiliki alur inti yang dapat
berjalan end-to-end.

#### P0 --- Wajib Berjalan

- Database MySQL berjalan.
- Laravel project berjalan.
- Authentication dasar selesai.
- Ebook collection dapat dibuat/diisi.
- PDF dapat di-upload ketika online.
- Metadata tersimpan di MySQL.
- File PDF dapat disimpan ke Cloudflare R2 atau storage abstraction
  yang siap digunakan.
- PDF list dapat ditampilkan.
- Read/unread dapat diubah.
- PDF dapat dibuka melalui mekanisme akses yang sesuai.
- REST API dasar selesai dan dapat diuji.

#### P1 --- Mulai Berfungsi

- Web dapat login dan menampilkan collection.
- Web dapat melakukan manual upload.
- Android mulai terhubung ke API.
- Android dapat login.
- Android dapat menampilkan collection.

#### P2 --- Belum Wajib

- Local PDF discovery boleh masih berupa prototype.
- Duplicate prevention boleh belum lengkap.

**Definisi milestone 7 Oktober:**

> Core system sudah dapat digunakan tanpa bergantung pada local ebook
> discovery.

---

### 12 October 2026 --- Full MVP

Pada tanggal ini, seluruh MVP yang telah ditentukan harus usable dari
Android dan Web.

#### P0 --- Final

- Authentication berjalan.
- Ebook collection berjalan.
- Upload PDF berjalan ketika online.
- Metadata dan file PDF tersimpan dengan benar.
- Authorization dan ownership checking berjalan.
- Read/unread berjalan.

#### P1 --- Final

- Android MVP selesai.
- Web MVP selesai.
- Android dan Web menggunakan data yang sama.
- Read/unread tersinkronisasi.
- PDF dapat diakses kembali dari kedua platform.

#### P2 --- Final atau Fallback Stabil

- Local PDF discovery berfungsi; atau
- File picker tersedia sebagai fallback yang stabil.
- Basic duplicate prevention berjalan.
- Validation dan error handling selesai.
- UI dasar dirapikan.
- Testing end-to-end selesai.

**Definisi milestone 12 Oktober:**

> User dapat menggunakan alur utama sistem dari Android maupun Web untuk
> mengelola collection ebook yang sama.

---

### Cut Rule

Jika terjadi keterbatasan waktu:

1.  Jangan mengorbankan P0 untuk menyelesaikan P2.
2.  P1 harus tetap menghasilkan Android dan Web yang dapat menggunakan
    core system.
3.  P2 dapat disederhanakan atau ditunda selama fallback tersedia dan
    core problem tetap terselesaikan.

---

## 20. Recommended Development Order

Urutan implementasi:

```text
1. Laravel Project Setup
       ↓
2. MySQL Database
       ↓
3. User Model + Authentication
       ↓
4. Ebook Model + Migration
       ↓
5. Ebook CRUD / Business Logic
       ↓
6. Database Procedure + Function + Trigger
       ↓
7. Database Transaction — COMMIT / ROLLBACK
       ↓
8. REST API
       ↓
9. Test API
       ↓
10. Cloudflare R2 Integration
       ↓
11. Web Blade
       ↓
12. Android Basic UI
       ↓
13. Android ↔ Laravel Integration
       ↓
14. Local PDF Discovery
       ↓
15. Add Local PDF → Cloud Collection
       ↓
16. Web + Android Shared Data Testing
       ↓
17. Validation & Error Handling
       ↓
18. UI Polish
       ↓
19. Final Testing
```

Cloud storage dibuat sebagai abstraction di Laravel sehingga development
tidak sepenuhnya bergantung pada R2 sejak tahap pertama.

Local discovery juga ditempatkan setelah alur upload dasar berjalan.
Dengan demikian, apabila discovery membutuhkan waktu lebih lama dari
perkiraan, fitur inti tetap dapat selesai.

---

## 21. Success Criteria

MVP dianggap berhasil apabila user dapat:

1.  Register melalui Web atau Android.
2.  Login.
3.  Menemukan PDF lokal melalui Android atau memilih file secara manual.
4.  Memasukkan PDF ke koleksi ketika koneksi internet tersedia.
5.  Melihat PDF dalam koleksi.
6.  Menandai PDF sebagai sudah dibaca.
7.  Mengubah status menjadi belum dibaca.
8.  Membuka PDF melalui PDF viewer eksternal.
9.  Pada Android, membuka PDF secara offline jika file lokal masih
    tersedia.
10. Melihat koleksi yang sama dari Android dan Web.
11. Menyimpan file PDF di cloud storage.
12. Mengakses file hanya melalui user yang memiliki hak akses.
13. Tidak perlu melakukan upload manual jika PDF lokal dapat ditemukan
    melalui discovery.

### Main Problem Solved

Sistem berhasil apabila pengguna memiliki satu tempat terpusat untuk:

> **Mengetahui PDF apa yang dimiliki, memasukkan PDF ke koleksi dengan
> langkah seminimal mungkin, membuka PDF, dan mengetahui apakah PDF
> tersebut sudah dibaca atau belum melalui Android maupun Web.**

### Core Product Principle

> **Aplikasi harus mengurangi pekerjaan pengguna, bukan sekadar
> memindahkan pekerjaan tersebut ke tempat lain.**
