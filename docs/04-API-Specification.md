# API Specification

## Ebook Storage & To-Do List

**Versi:** 1.0
**Tanggal:** 3 Oktober 2026
**Acuan:** 00-Project-Overview, 01-Use-Case, 02-User-Flow, 03-Rancangan-Database-Ebook-Storage

---

## 1. Tujuan dan Ruang Lingkup

Dokumen ini mendefinisikan kontrak REST API Laravel yang digunakan oleh **Android Kotlin**. Web Laravel Blade tidak memanggil REST API; Web menggunakan route Blade yang memanggil logika bisnis yang sama (lihat bagian 8).

API mencakup fitur MVP pada dokumen 01-Use-Case dan tidak mencakup fitur di luar MVP (delete ebook, edit judul, kategori, tag, pencarian, dan sebagainya).

### Keputusan yang mendasari dokumen ini

1. API terdiri dari **7 endpoint**, termasuk `logout` dan `GET /api/ebooks/{id}/file`.
2. Tabel `ebooks` ditambah kolom **`file_hash`** untuk duplicate prevention; duplikat dijawab dengan **409 Conflict**.
3. Batas ukuran upload PDF adalah **20 MB**.

---

## 2. Daftar Endpoint

| Method | Path                    | Auth  | Use Case     | Prioritas |
| ------ | ----------------------- | ----- | ------------ | --------- |
| POST   | `/api/register`         | Tidak | UC-01        | P0        |
| POST   | `/api/login`            | Tidak | UC-02        | P0        |
| POST   | `/api/logout`           | Ya    | UC-03        | P0        |
| GET    | `/api/ebooks`           | Ya    | UC-04        | P0        |
| POST   | `/api/ebooks`           | Ya    | UC-06, UC-07 | P0        |
| GET    | `/api/ebooks/{id}/file` | Ya    | UC-08        | P0        |
| PATCH  | `/api/ebooks/{id}`      | Ya    | UC-09, UC-10 | P0        |

Local Discovery (UC-05) terjadi di perangkat Android dan tidak memiliki endpoint. File yang dipilih dikirim melalui `POST /api/ebooks`.

---

## 3. Konvensi Umum

### 3.1 Header

Setiap request dari Android **wajib** mengirim:

```text
Accept: application/json
```

Tanpa header ini, Laravel dapat mengembalikan redirect HTML, bukan JSON, pada kondisi tidak terautentikasi.

Endpoint dengan Auth = Ya juga mengirim:

```text
Authorization: Bearer {token}
```

Request JSON menggunakan `Content-Type: application/json`. Upload menggunakan `multipart/form-data`.

### 3.2 Autentikasi

- Laravel Sanctum, personal access token.
- Token diperoleh dari `POST /api/login` dan dicabut oleh `POST /api/logout`.
- Android menyimpan token pada `SharedPreferences` privat (`MODE_PRIVATE`) pada MVP. Penyimpanan terenkripsi menjadi perbaikan setelah MVP.

### 3.3 Format Sukses

```json
{ "data": {} }
```

Untuk daftar, `data` berisi array dan terdapat `meta`.

### 3.4 Format Error

Error validasi (422) menggunakan format standar Laravel:

```json
{
  "message": "The file field must be a file of type: pdf.",
  "errors": { "file": ["The file field must be a file of type: pdf."] }
}
```

Error lainnya menggunakan format:

```json
{
  "message": "Pesan yang dapat ditampilkan kepada user.",
  "code": "kode_mesin"
}
```

Android menggunakan `code` untuk percabangan logika dan `message` untuk ditampilkan.

### 3.5 Kode Status

| Kode | Kondisi                                                     | `code`                                    |
| ---- | ----------------------------------------------------------- | ----------------------------------------- |
| 200  | Berhasil                                                    | -                                         |
| 201  | Berhasil dibuat                                             | -                                         |
| 401  | Token tidak ada/tidak valid, atau login gagal               | `unauthenticated` / `invalid_credentials` |
| 404  | Ebook tidak ada atau bukan milik user                       | `ebook_not_found`                         |
| 404  | Record ada, tetapi file tidak ditemukan di R2               | `file_missing`                            |
| 409  | PDF duplikat dalam koleksi user                             | `duplicate_ebook`                         |
| 413  | Ditolak web server karena melebihi batas body (lihat 7.1)   | -                                         |
| 422  | Validasi gagal, termasuk file bukan PDF atau melebihi 20 MB | -                                         |
| 429  | Terlalu banyak percobaan login                              | `too_many_attempts`                       |
| 500  | Kegagalan server (MySQL atau R2)                            | `server_error`                            |

### 3.6 Kepemilikan

Ebook milik user lain diperlakukan **sama dengan ebook yang tidak ada**: respons 404 `ebook_not_found`. Keberadaan ebook tidak boleh bocor kepada user yang tidak berhak.

---

## 4. Resource Ebook

```json
{
  "id": 15,
  "title": "Clean Code",
  "is_read": false,
  "created_at": "2026-10-03T08:15:00+00:00",
  "updated_at": "2026-10-03T08:15:00+00:00"
}
```

Field yang **tidak** diekspos: `user_id`, `file_path` (kunci internal R2), `file_hash`.

---

## 5. Detail Endpoint

### 5.1 POST /api/register

**Auth:** Tidak

**Request**

```json
{
  "name": "Noctura",
  "email": "user@example.com",
  "password": "rahasia123",
  "password_confirmation": "rahasia123"
}
```

**Validasi**

| Field      | Aturan                                                  |
| ---------- | ------------------------------------------------------- |
| `name`     | wajib, string, maks 100                                 |
| `email`    | wajib, format email, maks 255, unik pada `users`        |
| `password` | wajib, min 8, harus sama dengan `password_confirmation` |

**Respons 201**

```json
{
  "data": { "id": 1, "name": "Noctura", "email": "user@example.com" }
}
```

Register **tidak** mengembalikan token. Sesuai alur pada 02-User-Flow, setelah akun dibuat user melanjutkan ke login.

**Error:** 422 (validasi, termasuk email sudah terdaftar).

---

### 5.2 POST /api/login

**Auth:** Tidak

**Request**

```json
{
  "email": "user@example.com",
  "password": "rahasia123",
  "device_name": "android"
}
```

`device_name` opsional; default `"android"`. Digunakan sebagai nama token.

**Respons 200**

```json
{
  "data": {
    "token": "1|abcdef...",
    "token_type": "Bearer",
    "user": { "id": 1, "name": "Noctura", "email": "user@example.com" }
  }
}
```

**Error**

| Kode                      | Kondisi                                                     |
| ------------------------- | ----------------------------------------------------------- |
| 401 `invalid_credentials` | Email atau password salah (pesan tidak membedakan keduanya) |
| 422                       | Field wajib tidak diisi                                     |
| 429 `too_many_attempts`   | Melebihi 5 percobaan per menit per kombinasi email dan IP   |

---

### 5.3 POST /api/logout

**Auth:** Ya

**Request:** tanpa body.

**Perilaku:** mencabut token yang digunakan pada request ini. Token pada perangkat lain tidak terpengaruh.

**Respons 200**

```json
{ "message": "Logout berhasil." }
```

**Error:** 401.

Setelah respons, Android menghapus token lokal dan kembali ke layar login. Jika logout gagal karena tidak ada koneksi, Android tetap menghapus token lokal; token di server dapat dicabut pada login berikutnya atau melalui kedaluwarsa (bila dikonfigurasi).

---

### 5.4 GET /api/ebooks

**Auth:** Ya

**Perilaku:** mengembalikan seluruh ebook milik user yang sedang login, diurutkan `created_at` menurun. Tanpa pagination pada MVP.

**Respons 200**

```json
{
  "data": [
    {
      "id": 15,
      "title": "Clean Code",
      "is_read": false,
      "created_at": "2026-10-03T08:15:00+00:00",
      "updated_at": "2026-10-03T08:15:00+00:00"
    },
    {
      "id": 12,
      "title": "Atomic Habits",
      "is_read": true,
      "created_at": "2026-10-02T10:00:00+00:00",
      "updated_at": "2026-10-03T07:30:00+00:00"
    }
  ],
  "meta": { "total": 2, "read_count": 1 }
}
```

- `meta.total`: jumlah ebook milik user.
- `meta.read_count`: hasil `fn_count_read_ebooks(user_id)`.
  Koleksi kosong mengembalikan `"data": []` dengan `total` dan `read_count` bernilai 0 (UC-04: empty state ditangani Android).

**Error:** 401, 500.

---

### 5.5 POST /api/ebooks

**Auth:** Ya
**Content-Type:** `multipart/form-data`

**Field**

| Field   | Tipe   | Wajib | Aturan                                                                     |
| ------- | ------ | ----- | -------------------------------------------------------------------------- |
| `file`  | file   | Ya    | PDF (ekstensi `pdf` dan MIME `application/pdf`), maks **20 MB** (20480 KB) |
| `title` | string | Tidak | maks 255; jika kosong, diambil dari nama file tanpa ekstensi               |

**Respons 201**

```json
{
  "data": {
    "id": 16,
    "title": "Kotlin Basics",
    "is_read": false,
    "created_at": "2026-10-03T09:00:00+00:00",
    "updated_at": "2026-10-03T09:00:00+00:00"
  }
}
```

**Error**

| Kode                  | Kondisi                                                                                      |
| --------------------- | -------------------------------------------------------------------------------------------- |
| 401                   | Token tidak valid                                                                            |
| 409 `duplicate_ebook` | File dengan hash yang sama sudah ada dalam koleksi user; body menyertakan `data.existing_id` |
| 413                   | Ditolak web server (lihat 7.1)                                                               |
| 422                   | File tidak ada, bukan PDF, atau melebihi 20 MB                                               |
| 500                   | Penyimpanan ke R2 atau MySQL gagal; ebook tidak masuk koleksi                                |

Contoh 409:

```json
{
  "message": "PDF ini sudah ada di koleksi Anda.",
  "code": "duplicate_ebook",
  "data": { "existing_id": 12 }
}
```

**Urutan proses internal**

```text
1. Validasi user dan file
2. Hitung SHA-256 file
3. Cek duplikat (user_id + file_hash) ──► ada: 409, berhenti
4. Simpan file ke R2
5. START TRANSACTION
6. INSERT ebooks (title, file_path, file_hash, ...)
7. Berhasil ──► COMMIT ──► 201
   Gagal    ──► ROLLBACK ──► hapus file di R2 (compensating action) ──► 500
```

Catatan:

- Kunci objek di R2: `ebooks/user-{user_id}/{uuid}.pdf`. Nama file asli tidak dipakai agar tidak terjadi tabrakan nama atau karakter tidak aman; judul manusiawi disimpan di kolom `title`.
- Pemeriksaan pada langkah 3 bersifat penghalang awal. Unique index `(user_id, file_hash)` tetap menjadi pengaman terakhir; jika INSERT gagal karena duplicate key (misalnya dua upload bersamaan), hasilnya sama: ROLLBACK, hapus file R2, respons 409.
- Jika penghapusan file R2 pada compensating action gagal, Laravel mencatat `file_path` yatim ke log agar dapat dibersihkan manual.
- Upload membutuhkan koneksi internet. Tidak ada pending upload atau offline queue; Android memeriksa koneksi sebelum mengirim dan menampilkan pesan "coba lagi saat online" bila offline.

---

### 5.6 GET /api/ebooks/{id}/file

**Auth:** Ya

**Perilaku:** Laravel memeriksa kepemilikan, lalu men-stream PDF dari R2 melalui Laravel. Android dan Web tidak mengakses R2 langsung.

**Respons 200**

```text
Content-Type: application/pdf
Content-Length: {ukuran}
Content-Disposition: inline; filename="{judul}.pdf"
```

Body berisi byte PDF.

**Error**

| Kode                  | Kondisi                                                             |
| --------------------- | ------------------------------------------------------------------- |
| 401                   | Token tidak valid                                                   |
| 404 `ebook_not_found` | Ebook tidak ada atau bukan milik user                               |
| 404 `file_missing`    | Record ada, file tidak ditemukan di R2 (UC-08: file tidak tersedia) |
| 500                   | R2 tidak dapat dijangkau                                            |

**Catatan untuk Android**

PDF viewer eksternal tidak dapat mengirim header `Authorization`. Karena itu Android tidak boleh menyerahkan URL endpoint ini ke viewer. Alur yang benar:

```text
Local-first (UC-08):
  File lokal tersedia? ──► Ya ──► buka lewat FileProvider ──► viewer eksternal
                        └─► Tidak
                              ├── Offline ──► informasikan "butuh internet"
                              └── Online ──► GET /api/ebooks/{id}/file (dengan token)
                                              ──► simpan ke penyimpanan aplikasi
                                              ──► buka lewat FileProvider ──► viewer eksternal
```

Lokasi dan mekanisme penyimpanan lokal Android (termasuk apakah hasil unduhan menjadi file lokal permanen) masih menunggu keputusan tersendiri dan tidak diatur dalam dokumen ini.

Range request tidak didukung pada MVP karena Android mengunduh file secara utuh.

---

### 5.7 PATCH /api/ebooks/{id}

**Auth:** Ya

**Request**

```json
{ "is_read": true }
```

| Field     | Aturan         |
| --------- | -------------- |
| `is_read` | wajib, boolean |

Field lain pada body diabaikan. Pengubahan judul berada di luar MVP.

**Respons 200**

```json
{
  "data": {
    "id": 15,
    "title": "Clean Code",
    "is_read": true,
    "created_at": "2026-10-03T08:15:00+00:00",
    "updated_at": "2026-10-03T09:30:00+00:00"
  }
}
```

**Perilaku**

- Endpoint bersifat **idempotent**. Jika status ebook sudah sama dengan nilai yang diminta, Laravel tidak memanggil procedure dan langsung mengembalikan 200 dengan state terkini. Tidak ada error karena status sudah sama.
- Jika status berbeda:

```text
Cari ebook milik user ──► tidak ada: 404
START TRANSACTION
  CALL sp_mark_ebook_read(user_id, ebook_id)      -- is_read = true
  atau
  CALL sp_mark_ebook_unread(user_id, ebook_id)    -- is_read = false
  Trigger AFTER UPDATE mencatat ebook_status_history
Berhasil ──► COMMIT ──► 200
Gagal    ──► ROLLBACK ──► 500; status dan riwayat sama-sama dibatalkan
```

- Jika perubahan gagal, status sebelumnya dipertahankan (UC-09 dan UC-10).
- Laravel tidak melakukan INSERT ke `ebook_status_history`; itu tanggung jawab Trigger.
  **Error:** 401, 404 `ebook_not_found`, 422 (`is_read` kosong atau bukan boolean), 500.

---

## 6. Perubahan pada Dokumen 03 (Database)

Keputusan `file_hash` membutuhkan perubahan berikut pada **03-Rancangan-Database-Ebook-Storage**.

### 6.1 Tabel `ebooks` (bagian 4)

Tambahkan satu baris kolom:

| Kolom       | Tipe     | Aturan   | Keterangan                                                |
| ----------- | -------- | -------- | --------------------------------------------------------- |
| `file_hash` | CHAR(64) | NOT NULL | SHA-256 isi file, dihitung server, untuk deteksi duplikat |

### 6.2 Index dan constraint (bagian 12)

Tambahkan pada `ebooks`:

```text
UNIQUE (user_id, file_hash)
```

Pasangan `(user_id, file_hash)` dipilih, bukan `file_hash` saja, agar dua user berbeda tetap boleh menyimpan PDF yang sama.

### 6.3 Aturan integritas (bagian 13)

Tambahkan aturan: satu user tidak boleh memiliki dua ebook dengan `file_hash` yang sama.

### 6.4 Catatan untuk Stored Procedure

Procedure `sp_mark_ebook_read` dan `sp_mark_ebook_unread` memperbarui `ebooks` langsung di MySQL, sehingga Eloquent tidak mengisi `updated_at` secara otomatis. Procedure harus menyertakan `updated_at = NOW()` pada `UPDATE`. Tanpa itu, `updated_at` pada respons PATCH tidak berubah.

---

## 7. Konfigurasi yang Harus Disesuaikan

### 7.1 Batas upload 20 MB

Batas bawaan PHP (`upload_max_filesize` 2 MB, `post_max_size` 8 MB) akan menolak banyak PDF **sebelum** request sampai ke Laravel. Sesuaikan:

```text
upload_max_filesize = 20M
post_max_size       = 25M
```

Jika memakai Nginx, sesuaikan juga `client_max_body_size 25m;`. Apabila web server menolak body lebih dahulu, respons yang muncul adalah 413; Android memperlakukan 413 dan 422 pada `file` sebagai "file terlalu besar".

Aturan validasi Laravel: `max:20480` (satuan KB).

Android perlu menaikkan _write timeout_ klien HTTP untuk upload, karena unggahan 20 MB pada koneksi lambat dapat melampaui default.

### 7.2 Throttling

- Login: 5 percobaan per menit.
- Endpoint lain: batas bawaan `api` Laravel (60 per menit) cukup untuk MVP.

---

## 8. Hubungan dengan Web (Laravel Blade)

Web tidak menggunakan `/api/*`. Namun Web dan API menjalankan operasi yang sama, sehingga logikanya **harus** berada pada satu service class (misalnya `EbookService`), bukan diduplikasi pada dua controller.

```text
Android ──► ApiEbookController ──┐
                                 ├──► EbookService ──► MySQL + R2
Web ──────► WebEbookController ──┘
```

`EbookService` memegang:

- `store(user, file, title)`: hash, cek duplikat, upload R2, transaksi INSERT, compensating action.
- `setReadStatus(user, ebook, isRead)`: pengecekan idempotensi, transaksi, pemanggilan procedure.
- `streamFile(user, ebook)`: pengecekan kepemilikan dan stream dari R2.
  Route Web (session, bukan token):

| Method   | Path                  | Operasi                           |
| -------- | --------------------- | --------------------------------- |
| GET/POST | `/register`, `/login` | Autentikasi                       |
| POST     | `/logout`             | Logout                            |
| GET      | `/ebooks`             | Daftar koleksi                    |
| POST     | `/ebooks`             | Manual upload                     |
| GET      | `/ebooks/{id}/file`   | Buka PDF di viewer bawaan browser |
| PATCH    | `/ebooks/{id}`        | Ubah status baca                  |

---

## 9. Traceability

| Use Case                   | Endpoint                                         |
| -------------------------- | ------------------------------------------------ |
| UC-01 Register             | `POST /api/register`                             |
| UC-02 Login                | `POST /api/login`                                |
| UC-03 Logout               | `POST /api/logout`                               |
| UC-04 View Collection      | `GET /api/ebooks`                                |
| UC-05 Discover Local Ebook | Tidak ada (proses lokal di Android)              |
| UC-06 Add Local Ebook      | `POST /api/ebooks`                               |
| UC-07 Manual Upload        | `POST /api/ebooks`                               |
| UC-08 Access Ebook         | `GET /api/ebooks/{id}/file`                      |
| UC-09 Mark as Read         | `PATCH /api/ebooks/{id}` dengan `is_read: true`  |
| UC-10 Mark as Unread       | `PATCH /api/ebooks/{id}` dengan `is_read: false` |

---

## 10. Di Luar Cakupan

- Delete ebook, edit judul, pencarian, filter, dan pagination.
- Versioning API (`/api/v1`).
- Refresh token dan kedaluwarsa token khusus.
- Presigned URL R2.
- Range request pada pengunduhan file.
- Offline upload queue dan sinkronisasi latar belakang.
- Endpoint khusus Local Discovery.

---

## 11. Urutan Implementasi yang Disarankan

```text
1. Migration (users, ebooks + file_hash, ebook_status_history)
2. Procedure, Trigger, Function
3. Sanctum + register / login / logout
4. EbookService
5. GET /api/ebooks
6. POST /api/ebooks (dengan storage abstraction; R2 menyusul)
7. PATCH /api/ebooks/{id}
8. GET /api/ebooks/{id}/file
9. Uji seluruh endpoint (Postman atau feature test)
```
