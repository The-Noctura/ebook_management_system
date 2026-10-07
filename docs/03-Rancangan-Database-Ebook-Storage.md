# Rancangan Database

## Ebook Storage & To-Do List

**Versi:** 1.1\
**Tanggal:** 4 Oktober 2026\
**Database:** MySQL

---

## 1. Tujuan Database

Database dirancang untuk mendukung sistem **Ebook Storage & To-Do List**
dengan kebutuhan utama:

- Menyimpan akun pengguna.
- Menyimpan koleksi ebook milik pengguna.
- Menyimpan status sudah/belum dibaca.
- Mencatat perubahan status ebook.
- Mendukung Stored Procedure, Trigger, Stored Function, COMMIT, dan
  ROLLBACK.
- Tetap sederhana agar sesuai dengan kebutuhan MVP.
  Database tidak dirancang untuk mengelola fitur yang belum masuk MVP
  seperti kategori, tag, bookmark, rating, statistik kompleks,
  rekomendasi, atau fitur sosial.

---

## 2. Struktur Database

Database menggunakan 3 tabel utama:

1.  `users`
2.  `ebooks`
3.  `ebook_status_history`

### Relasi

```text
users
  │
  │ 1 : N
  ▼
ebooks
  │
  │ 1 : N
  ▼
ebook_status_history
```

### Penjelasan Relasi

- Satu `user` dapat memiliki banyak `ebook`.
- Satu `ebook` hanya dimiliki oleh satu `user`.
- Satu `ebook` dapat memiliki banyak catatan perubahan status.
- `ebook_status_history` digunakan sebagai riwayat perubahan
  `is_read`.

---

## 3. Tabel `users`

Menyimpan data akun pengguna.

Kolom Tipe Aturan Keterangan

---

```sql
`id` BIGINT UNSIGNED PK, AUTO_INCREMENT ID pengguna
`name` VARCHAR(100) NOT NULL Nama pengguna
`email` VARCHAR(255) NOT NULL, UNIQUE Email login
`password` VARCHAR(255) NOT NULL Password yang sudah di-hash
`created_at` TIMESTAMP NULL Waktu pembuatan
`updated_at` TIMESTAMP NULL Waktu perubahan
```

### Fungsi

Tabel ini digunakan untuk:

- Registrasi.
- Login.
- Identitas pemilik ebook.
- Relasi kepemilikan ebook.

---

## 4. Tabel `ebooks`

Menyimpan data ebook yang dimiliki pengguna.

Kolom Tipe Aturan Keterangan

---

Kolom Tipe Aturan Keterangan

---

```sql
`id` BIGINT UNSIGNED PK, AUTO_INCREMENT ID ebook
`user_id` BIGINT UNSIGNED FK, NOT NULL Pemilik ebook
`title` VARCHAR(255) NOT NULL Judul ebook
`file_path` VARCHAR(500) NOT NULL Lokasi file di Cloudflare R2
`file_hash` CHAR(64) NOT NULL SHA-256 isi file, dihitung server, untuk
mendeteksi PDF duplikat
`is_read` BOOLEAN NOT NULL, DEFAULT FALSE Status sudah dibaca
`created_at` TIMESTAMP NULL Waktu ebook ditambahkan
`updated_at` TIMESTAMP NULL Waktu perubahan
```

### Fungsi

Tabel ini merupakan tabel utama koleksi ebook.

Digunakan untuk:

- Menampilkan koleksi ebook.
- Menyimpan informasi file ebook.
- Menghubungkan ebook dengan pengguna.
- Menentukan status sudah/belum dibaca.
- Mengakses file yang tersimpan di Cloudflare R2.
- Mencegah PDF yang sama masuk dua kali ke koleksi user yang sama
  (melalui `file_hash`).

### Tentang `file_hash`

`file_hash` adalah hasil SHA-256 dari isi file PDF. Nilainya dihitung
oleh Laravel di server, bukan dikirim oleh client. Dua file dengan isi
yang sama menghasilkan hash yang sama, sehingga duplikat terdeteksi
walaupun nama filenya berbeda.

---

## 5. Tabel `ebook_status_history`

Menyimpan riwayat perubahan status baca ebook.

Kolom Tipe Aturan Keterangan

---

```sql
`id` BIGINT UNSIGNED PK, AUTO_INCREMENT ID riwayat
`ebook_id` BIGINT UNSIGNED FK, NOT NULL Ebook yang berubah
`old_status` BOOLEAN NOT NULL Status sebelum perubahan
`new_status` BOOLEAN NOT NULL Status setelah perubahan
`changed_at` TIMESTAMP NOT NULL Waktu perubahan
```

### Fungsi

Tabel ini terutama digunakan oleh **Trigger**.

Contoh:

```text
Sebelum:
is_read = FALSE

Pengguna menandai ebook sebagai sudah dibaca.

Sesudah:
is_read = TRUE

Trigger otomatis membuat:
old_status = FALSE
new_status = TRUE
changed_at = waktu perubahan
```

Dengan demikian, Trigger memiliki fungsi nyata dalam sistem dan bukan
hanya dibuat untuk memenuhi requirement.

---

## 6. ERD Sederhana

```text
┌─────────────────────┐
│       users         │
├─────────────────────┤
│ PK id               │
│    name             │
│    email            │
│    password         │
│    created_at       │
│    updated_at       │
└──────────┬──────────┘
           │
           │ 1
           │
           │ N
┌──────────▼──────────┐
│       ebooks        │
├─────────────────────┤
│ PK id               │
│ FK user_id          │
│    title            │
│    file_path        │
│    file_hash        │
│    is_read          │
│    created_at       │
│    updated_at       │
└──────────┬──────────┘
           │ 1
           │
           │ N
┌──────────▼────────────────┐
│   ebook_status_history    │
├───────────────────────────┤
│ PK id                     │
│ FK ebook_id               │
│    old_status             │
│    new_status             │
│    changed_at             │
└───────────────────────────┘
```

---

## 7. Database Programming Requirements

Database harus menggunakan lima mekanisme berikut:

### 7.1 Stored Procedure

Stored Procedure digunakan untuk perubahan status ebook.

Procedure yang direncanakan:

```text
sp_mark_ebook_read(user_id, ebook_id)
sp_mark_ebook_unread(user_id, ebook_id)
```

Tanggung jawab Procedure:

1.  Memastikan ebook memang milik user.
2.  Mengubah `ebooks.is_read` dan `ebooks.updated_at`.
3.  Membiarkan Trigger mencatat riwayat perubahan.

Karena Procedure memperbarui `ebooks` langsung di MySQL, Eloquent tidak
mengisi `updated_at` secara otomatis. Perintah `UPDATE` di dalam
Procedure harus menyertakan `updated_at = NOW()`. Tanpa itu,
`updated_at` pada respons API tidak berubah setelah status diubah.

```text
Laravel
   │
   ▼
CALL sp_mark_ebook_read(...)
   │
   ▼
Validasi ownership
   │
   ▼
UPDATE ebooks
   │
   ▼
Trigger aktif
   │
   ▼
INSERT ebook_status_history
```

Procedure tidak perlu melakukan INSERT ke `ebook_status_history`, karena
pekerjaan tersebut dilakukan oleh Trigger.

---

### 7.2 Trigger

Trigger digunakan untuk mencatat perubahan status baca.

Trigger direncanakan aktif pada tabel:

```text
ebooks
```

Event:

```text
AFTER UPDATE
```

Kondisi:

```text
OLD.is_read <> NEW.is_read
```

Jika status berubah, Trigger otomatis membuat record baru pada:

```text
ebook_status_history
```

Tujuan utamanya adalah memastikan perubahan status selalu memiliki
riwayat tanpa Laravel harus melakukan INSERT history secara manual.

---

### 7.3 Stored Function

Stored Function digunakan untuk menghitung jumlah ebook yang sudah
dibaca oleh user.

Nama:

```text
fn_count_read_ebooks(user_id)
```

Contoh penggunaan:

```text
fn_count_read_ebooks(1)
```

Hasil:

```text
5
```

Artinya user dengan ID 1 memiliki 5 ebook dengan status
`is_read = TRUE`.

Function ini dapat digunakan untuk informasi sederhana pada
collection/dashboard tanpa membuat sistem statistik yang kompleks.

---

## 8. COMMIT dan ROLLBACK

Transaction digunakan ketika operasi database membutuhkan lebih dari
satu langkah dan harus dipastikan konsisten.

Transaction diterapkan pada dua operasi: perubahan status baca (utama)
dan penyimpanan metadata ebook saat upload. Ringkasannya ada pada
bagian "Operasi yang Menggunakan Transaction" di bawah.

Contoh perubahan status:

```text
START TRANSACTION

    CALL sp_mark_ebook_read(...)

    Trigger mencatat history

Jika berhasil:
    COMMIT

Jika terjadi error:
    ROLLBACK
```

### COMMIT

Digunakan ketika seluruh proses database berhasil.

```text
BEGIN
    update ebook
    trigger membuat history
COMMIT
```

Perubahan menjadi permanen.

### ROLLBACK

Digunakan jika terjadi kegagalan dalam transaction.

```text
BEGIN
    update ebook
    terjadi error
ROLLBACK
```

Perubahan database dalam transaction dibatalkan.

### Operasi yang Menggunakan Transaction

---

Operasi Isi transaction Jika berhasil Jika gagal

---

Ubah status baca (utama) CALL `sp_mark_ebook_read` / `sp_mark_ebook_unread` COMMIT ROLLBACK; perubahan status dan
→ UPDATE `ebooks` → Trigger INSERT riwayat sama-sama dibatalkan
`ebook_status_history`

Simpan ebook (upload) INSERT `ebooks` setelah file berhasil disimpan COMMIT ROLLBACK, lalu Laravel menghapus
di R2 file di R2 (compensating action)

---

Perubahan status baca ditetapkan sebagai demonstrasi utama COMMIT dan
ROLLBACK karena operasi tersebut memuat lebih dari satu penulisan
(UPDATE oleh Procedure dan INSERT riwayat oleh Trigger) yang harus
berhasil atau gagal bersama.

ROLLBACK hanya membatalkan perubahan MySQL. Penghapusan file di R2
merupakan compensating action oleh Laravel, bukan bagian dari ROLLBACK
(lihat bagian 10).

---

## 9. Contoh Alur Lengkap

### Menandai Ebook Sebagai Sudah Dibaca

```text
Android / Web
      │
      ▼
Laravel API
      │
      ▼
Validasi user & ebook
      │
      ▼
START TRANSACTION
      │
      ▼
CALL sp_mark_ebook_read()
      │
      ▼
UPDATE ebooks.is_read
      │
      ▼
Trigger AFTER UPDATE
      │
      ▼
INSERT ebook_status_history
      │
      ▼
Proses berhasil?
   ┌──┴──┐
  YA    TIDAK
   │      │
   ▼      ▼
COMMIT  ROLLBACK
```

---

## 10. Cloudflare R2 dan Transaction

Database MySQL dan Cloudflare R2 merupakan dua sistem yang berbeda.

Penting:

> `COMMIT` dan `ROLLBACK` MySQL tidak otomatis membatalkan upload file
> ke Cloudflare R2.

Contoh masalah:

```text
1. Laravel upload ebook ke R2
2. File berhasil tersimpan
3. Laravel menyimpan data ebook ke MySQL
4. Database gagal
5. MySQL ROLLBACK
```

Hasilnya:

```text
MySQL → data ebook tidak ada
R2    → file ebook masih ada
```

File tersebut dapat menjadi **orphan file**.

Karena itu, Laravel perlu menangani kegagalan antar-sistem.

Pendekatan yang dapat digunakan:

```text
Upload R2
    │
    ▼
Simpan data MySQL
    │
    ├── berhasil → COMMIT
    │
    └── gagal → ROLLBACK + hapus file R2
```

Penghapusan file R2 dalam kasus tersebut merupakan **compensating
action**, bukan bagian dari `ROLLBACK` MySQL.

---

## 11. Foreign Key

### `ebooks.user_id`

```text
ebooks.user_id
      │
      ▼
users.id
```

Digunakan untuk memastikan setiap ebook memiliki pemilik yang valid.

### `ebook_status_history.ebook_id`

```text
ebook_status_history.ebook_id
            │
            ▼
        ebooks.id
```

Digunakan untuk memastikan setiap riwayat status berkaitan dengan ebook
yang valid.

---

## 12. Index dan Constraint

Index dan constraint yang disarankan:

### `users`

```text
PRIMARY KEY (id)
UNIQUE (email)
```

### `ebooks`

```text
PRIMARY KEY (id)
INDEX (user_id)
UNIQUE (user_id, file_hash)
FOREIGN KEY (user_id) REFERENCES users(id)
```

Pasangan `(user_id, file_hash)` dipilih, bukan `file_hash` saja, agar
dua user berbeda tetap boleh menyimpan PDF yang sama.

Pemeriksaan duplikat oleh Laravel bersifat penghalang awal. Unique
index ini menjadi pengaman terakhir, misalnya ketika dua upload yang
sama masuk bersamaan.

### `ebook_status_history`

```text
PRIMARY KEY (id)
INDEX (ebook_id)
FOREIGN KEY (ebook_id) REFERENCES ebooks(id)
```

Index `user_id` penting karena collection hampir selalu dicari
berdasarkan pengguna yang sedang login.

---

## 13. Aturan Integritas Data

Beberapa aturan penting:

1.  Email pengguna harus unik.
2.  Ebook harus memiliki pemilik.
3.  Ebook tidak boleh memiliki `user_id` yang tidak valid.
4.  Status `is_read` hanya memiliki dua keadaan:
    - `FALSE` = belum dibaca
    - `TRUE` = sudah dibaca
5.  History hanya dibuat ketika status ebook benar-benar berubah.
6.  Laravel harus memvalidasi bahwa user hanya dapat mengubah ebook
    miliknya sendiri.
7.  Perubahan status yang menggunakan requirement Stored Procedure harus
    melalui Procedure, bukan update Eloquent biasa.
8.  Laravel tidak perlu melakukan INSERT history secara manual karena
    sudah ditangani Trigger.
9.  Satu user tidak boleh memiliki dua ebook dengan `file_hash` yang
    sama (dijamin oleh `UNIQUE (user_id, file_hash)`). Percobaan
    menyimpan duplikat ditolak dan dijawab API dengan 409.

---

## 14. Pembagian Tanggung Jawab

Komponen Tanggung Jawab

---

`users` Data akun
`ebooks` Data koleksi ebook
`ebook_status_history` Riwayat status
Stored Procedure Mengubah status ebook
Trigger Mencatat perubahan status
Stored Function Menghitung ebook yang sudah dibaca
COMMIT Menetapkan transaksi berhasil
ROLLBACK Membatalkan transaksi database
Laravel Business logic, auth, validation, transaction, akses R2
MySQL Penyimpanan dan integritas data
Cloudflare R2 Penyimpanan file ebook

---

## 15. Batasan Rancangan

Database ini sengaja dibuat sederhana.

Tidak termasuk:

- Author.
- Publisher.
- Category.
- Tag.
- Bookmark.
- Reading progress.
- Rating.
- Review.
- Recommendation.
- Sharing.
- Statistik kompleks.
- Multi-role admin.
- Sistem sosial.
  Fitur tersebut dapat ditambahkan pada versi berikutnya jika kebutuhan
  proyek berkembang.

---

## 16. Urutan Implementasi

Implementasi database disarankan dengan urutan:

```text
1. Buat database
      ↓
2. Buat tabel users
      ↓
3. Buat tabel ebooks
      ↓
4. Buat tabel ebook_status_history
      ↓
5. Tambahkan foreign key & index
      ↓
6. Buat Stored Procedure
      ↓
7. Buat Trigger
      ↓
8. Buat Stored Function
      ↓
9. Uji COMMIT
      ↓
10. Uji ROLLBACK
      ↓
11. Integrasikan dengan Laravel
```

---

## 17. Kesimpulan

Rancangan database menggunakan tiga tabel utama yang cukup untuk
mendukung MVP:

```text
users
  ↓
ebooks
  ↓
ebook_status_history
```

Struktur ini tetap sederhana, tetapi setiap tabel memiliki fungsi nyata.

`ebook_status_history` sengaja dipertahankan karena memberikan tempat
yang jelas untuk penggunaan Trigger. Stored Procedure digunakan untuk
operasi perubahan status, Stored Function untuk perhitungan sederhana,
sedangkan COMMIT dan ROLLBACK digunakan untuk menjaga konsistensi
transaksi database.

Dengan rancangan ini, database tidak hanya memenuhi requirement teknis,
tetapi juga tetap relevan dengan fitur utama aplikasi.
