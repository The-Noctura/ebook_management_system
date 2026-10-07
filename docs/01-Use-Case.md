# Use Case Specification

## 1. Overview

### 1.1 Purpose

Dokumen ini mendefinisikan use case untuk sistem **Ebook Storage & To-Do
List** berdasarkan scope dan konsep yang telah ditetapkan pada Project
Overview.

Use case digunakan untuk menjelaskan:

- actor yang berinteraksi dengan sistem;
- tujuan setiap interaksi;
- kondisi sebelum proses;
- alur utama;
- alur alternatif atau kondisi gagal;
- hasil setelah proses selesai.
  Dokumen ini berfokus pada perilaku sistem dari sudut pandang user, bukan
  detail implementasi teknis seperti controller, endpoint API, database
  query, Retrofit, atau struktur Cloudflare R2.

### 1.2 Scope

Use case mencakup fitur MVP:

- Authentication;
- Local Ebook Discovery pada Android;
- Penambahan file PDF lokal ke collection;
- Manual upload file PDF;
- Melihat collection;
- Membuka file PDF melalui PDF viewer eksternal;
- Mengubah status read/unread;
- Penggunaan collection yang sama melalui Android dan Web.
  Pada Fase 1, sistem hanya mendukung format **PDF**. Sistem tidak
  menyediakan PDF reader internal; pembacaan PDF diserahkan kepada PDF
  viewer eksternal pada perangkat Android atau viewer PDF bawaan browser
  pada Web.

Fitur seperti format ebook selain PDF, categories, tags, bookmarks,
reading progress, ratings, statistics, sharing, recommendations, social
features, admin dashboard, advanced duplicate detection, automatic
background synchronization, offline upload queue, dan automatic upload
setiap file yang terdeteksi berada di luar MVP.

### 1.3 Actor

| Actor | Deskripsi                                                                   |
| ----- | --------------------------------------------------------------------------- |
| User  | Pengguna yang mengelola koleksi ebook pribadi melalui Android dan/atau Web. |

MVP hanya memiliki satu actor utama. Admin khusus tidak diperlukan.

---

## 2. Use Case Diagram

```text
                         ┌─────────────────────────────┐
                         │   Ebook Storage & To-Do     │
                         │           List              │
                         │                             │
 User ──────────────────►│ UC-01 Register              │
 User ──────────────────►│ UC-02 Login                 │
 User ──────────────────►│ UC-03 Logout                │
 User ──────────────────►│ UC-04 View Collection       │
 User ──────────────────►│ UC-05 Discover Local Ebook  │
 User ──────────────────►│ UC-06 Add Local Ebook        │
 User ──────────────────►│ UC-07 Manual Upload          │
 User ──────────────────►│ UC-08 Access Ebook           │
 User ──────────────────►│ UC-09 Mark as Read           │
 User ──────────────────►│ UC-10 Mark as Unread         │
                         │                             │
                         └─────────────────────────────┘
```

> Catatan: Local Ebook Discovery merupakan aktivitas yang berlangsung
> pada Android. Web tidak melakukan local discovery.

---

## 3. Use Case List

| ID    | Use Case             | Platform      | Tujuan                                                     |
| ----- | -------------------- | ------------- | ---------------------------------------------------------- |
| UC-01 | Register             | Android / Web | Membuat akun pengguna                                      |
| UC-02 | Login                | Android / Web | Masuk ke sistem                                            |
| UC-03 | Logout               | Android / Web | Mengakhiri sesi pengguna                                   |
| UC-04 | View Collection      | Android / Web | Melihat koleksi ebook pribadi                              |
| UC-05 | Discover Local Ebook | Android       | Menemukan file PDF yang tersedia di perangkat              |
| UC-06 | Add Local Ebook      | Android       | Memasukkan file PDF lokal yang dipilih ke collection       |
| UC-07 | Manual Upload Ebook  | Android / Web | Memasukkan file PDF melalui pemilihan file manual          |
| UC-08 | Access Ebook         | Android / Web | Membuka file PDF dalam collection melalui viewer eksternal |
| UC-09 | Mark Ebook as Read   | Android / Web | Mengubah status ebook menjadi sudah dibaca                 |
| UC-10 | Mark Ebook as Unread | Android / Web | Mengubah status ebook menjadi belum dibaca                 |

---

# 4. Detailed Use Cases

## UC-01 --- Register

### Goal

User membuat akun untuk menggunakan sistem dan memiliki collection ebook
pribadi.

### Actor

User

### Platform

Android / Web

### Preconditions

- User belum memiliki akun yang digunakan untuk masuk ke sistem.

### Main Flow

1.  User membuka halaman/register screen.
2.  Sistem menampilkan form registrasi.
3.  User memasukkan data registrasi.
4.  User mengirim form.
5.  Sistem memvalidasi data.
6.  Sistem membuat akun user.
7.  Sistem memberikan hasil bahwa registrasi berhasil.

### Alternative / Exception Flow

- Jika data registrasi tidak valid, sistem menampilkan informasi
  kesalahan dan user dapat memperbaiki data.
- Jika data yang diperlukan belum lengkap, sistem meminta user
  melengkapinya.

### Postconditions

- Akun user berhasil dibuat.
- User dapat menggunakan akun tersebut untuk login.

---

## UC-02 --- Login

### Goal

User masuk ke sistem untuk mengakses collection miliknya.

### Actor

User

### Platform

Android / Web

### Preconditions

- User telah memiliki akun.

### Main Flow

1.  User membuka halaman/login screen.
2.  Sistem menampilkan form login.
3.  User memasukkan kredensial.
4.  User mengirim form.
5.  Sistem memvalidasi kredensial.
6.  Sistem mengautentikasi user.
7.  Sistem memberikan akses ke area collection.

### Alternative / Exception Flow

- Jika kredensial tidak valid, sistem menampilkan informasi bahwa
  login gagal.
- User dapat mencoba login kembali.

### Postconditions

- User berada dalam keadaan terautentikasi.
- User dapat mengakses collection miliknya.

---

## UC-03 --- Logout

### Goal

User mengakhiri sesi penggunaan sistem.

### Actor

User

### Platform

Android / Web

### Preconditions

- User sedang login.

### Main Flow

1.  User memilih opsi logout.
2.  Sistem mengakhiri sesi user.
3.  Sistem mengembalikan user ke halaman/login screen.

### Postconditions

- User tidak lagi berada dalam sesi terautentikasi.

---

## UC-04 --- View Collection

### Goal

User melihat daftar ebook yang telah dimasukkan ke collection.

### Actor

User

### Platform

Android / Web

### Preconditions

- User telah login.

### Main Flow

1.  User membuka collection.
2.  Sistem mengambil data collection user.
3.  Sistem menampilkan daftar ebook.
4.  Sistem menampilkan informasi dasar ebook.
5.  Sistem menampilkan status read/unread.

### Alternative / Exception Flow

- Jika collection belum memiliki ebook, sistem menampilkan keadaan
  collection kosong.
- Jika data collection tidak dapat dimuat, sistem memberikan informasi
  bahwa collection belum dapat ditampilkan.

### Postconditions

- User dapat mengetahui ebook yang terdapat dalam collection.
- User dapat melihat status baca setiap ebook.

---

## UC-05 --- Discover Local Ebook

### Goal

User menemukan ebook yang sudah tersedia pada penyimpanan perangkat
Android tanpa harus mencari file secara manual satu per satu.

### Actor

User

### Platform

Android

### Preconditions

- User menggunakan Android.
- Aplikasi memiliki akses yang diperlukan untuk membaca lokasi
  penyimpanan yang menjadi scope discovery.
- Terdapat atau mungkin terdapat file ebook yang dapat ditemukan.

### Main Flow

1.  User membuka fitur discovery.
2.  Sistem melakukan pencarian ebook pada lokasi yang menjadi scope
    discovery.
3.  Sistem mengidentifikasi ebook yang tersedia.
4.  Sistem membandingkan hasil discovery dengan ebook yang telah masuk
    collection.
5.  Sistem menampilkan ebook lokal yang belum masuk collection.

### Alternative / Exception Flow

- Jika tidak ada ebook baru yang ditemukan, sistem menampilkan bahwa
  tidak ada ebook baru.
- Jika ebook sudah pernah dimasukkan ke collection, ebook tersebut
  tidak ditampilkan sebagai ebook baru secara terus-menerus.
- Jika discovery tidak dapat dilakukan sesuai kondisi perangkat, user
  dapat menggunakan file picker sebagai alternatif.

### Postconditions

- User memperoleh daftar ebook lokal yang dapat dipertimbangkan untuk
  dimasukkan ke collection.
- Discovery sendiri tidak otomatis memasukkan atau meng-upload ebook.

---

## UC-06 --- Add Local Ebook

### Goal

User memasukkan file PDF lokal yang dipilih ke collection terpusat.

### Actor

User

### Platform

Android

### Preconditions

- User telah login.
- File PDF telah ditemukan melalui local discovery.
- User memilih file PDF yang ingin dimasukkan.
- Perangkat memiliki koneksi internet untuk proses upload.

### Main Flow

1.  Sistem menampilkan file PDF yang ditemukan.
2.  User memilih file PDF.
3.  User memilih aksi **Tambah ke Koleksi**.
4.  Sistem menerima file dan informasi PDF.
5.  Sistem memvalidasi user dan file.
6.  Sistem memeriksa ketersediaan koneksi internet.
7.  Sistem menyimpan file PDF ke cloud storage.
8.  Sistem menyimpan metadata PDF ke collection.
9.  Sistem memberikan hasil bahwa PDF berhasil ditambahkan.
10. PDF muncul dalam collection.

### Alternative / Exception Flow

- Jika validasi file gagal, sistem tidak memasukkan PDF dan memberikan
  informasi kesalahan.
- Jika tidak tersedia koneksi internet, proses upload dibatalkan dan
  sistem meminta user mencoba kembali ketika koneksi tersedia.
- Jika proses penyimpanan file gagal, PDF tidak dianggap berhasil
  masuk ke collection.
- Jika PDF telah terdeteksi sebagai duplicate, sistem mencegah
  penambahan yang tidak diperlukan sesuai mekanisme duplicate
  prevention yang tersedia.

### Postconditions

- PDF yang berhasil diproses menjadi bagian dari collection user.
- File PDF tersedia pada cloud storage.
- Metadata PDF tersedia pada collection.

### Important Rule

Local discovery tidak berarti semua PDF yang ditemukan otomatis
di-upload. User tetap menentukan PDF mana yang ingin dimasukkan ke
collection.

Fase 1 tidak menyediakan offline upload queue atau pending
synchronization. Jika user offline saat menambahkan PDF, user harus
mencoba kembali.

---

## UC-07 --- Manual Upload Ebook

### Goal

User memasukkan file PDF ke collection melalui pemilihan file secara
manual.

### Actor

User

### Platform

Android / Web

### Preconditions

- User telah login.
- User memiliki file PDF yang ingin dimasukkan.
- Perangkat memiliki koneksi internet untuk proses upload.

### Main Flow

1.  User memilih aksi upload PDF.
2.  Sistem membuka mekanisme pemilihan file.
3.  User memilih file PDF.
4.  Sistem menerima file.
5.  Sistem memvalidasi user dan file.
6.  Sistem memeriksa ketersediaan koneksi internet.
7.  Sistem menyimpan file PDF ke cloud storage.
8.  Sistem menyimpan metadata PDF.
9.  Sistem menampilkan hasil bahwa PDF berhasil ditambahkan.
10. PDF muncul dalam collection.

### Alternative / Exception Flow

- Jika user membatalkan pemilihan file, proses upload dihentikan.
- Jika file bukan PDF atau tidak valid, sistem menolak file dan
  memberikan informasi kesalahan.
- Jika tidak tersedia koneksi internet, proses upload dibatalkan dan
  sistem meminta user mencoba kembali ketika koneksi tersedia.
- Jika proses penyimpanan gagal, PDF tidak dianggap berhasil masuk ke
  collection.
- Jika PDF terdeteksi sebagai duplicate, sistem mencegah penambahan
  yang tidak diperlukan sesuai mekanisme duplicate prevention.

### Postconditions

- PDF yang berhasil diproses menjadi bagian dari collection user.
- PDF dapat diakses dari platform yang menggunakan collection
  tersebut.

### Note

Manual upload berfungsi sebagai metode utama pada Web dan sebagai
fallback pada Android apabila local discovery tidak tersedia atau tidak
digunakan.

Fase 1 tidak menyediakan offline upload queue atau pending
synchronization.

---

## UC-08 --- Access Ebook

### Goal

User membuka file PDF yang telah berada dalam collection menggunakan PDF
viewer eksternal.

### Actor

User

### Platform

Android / Web

### Preconditions

- User telah login.
- PDF terdapat dalam collection user.
- File PDF tersedia pada salah satu sumber yang dapat diakses.

### Main Flow

1.  User membuka collection.
2.  User memilih PDF.
3.  User memilih aksi untuk membuka PDF.
4.  Sistem melakukan pemeriksaan hak akses.
5.  Pada Android, sistem memeriksa apakah file PDF lokal masih tersedia.
6.  Jika file lokal tersedia, sistem menggunakan file lokal.
7.  Jika file lokal tidak tersedia, sistem menggunakan file PDF dari
    cloud storage.
8.  Sistem menyerahkan file PDF kepada PDF viewer eksternal.
9.  User membaca PDF menggunakan viewer tersebut.

### Alternative / Exception Flow

- Jika PDF lokal tidak tersedia dan perangkat tidak memiliki koneksi
  internet, sistem tidak dapat mengambil file dari cloud dan
  memberikan informasi kepada user.
- Jika PDF tidak ditemukan pada sumber lokal maupun cloud, sistem
  menampilkan informasi bahwa file tidak tersedia.
- Jika user tidak memiliki hak akses terhadap PDF, sistem menolak
  akses.
- Jika file PDF tidak dapat diakses, sistem memberikan informasi
  kesalahan.
- Jika Android tidak memiliki aplikasi yang dapat membuka PDF, sistem
  memberi tahu user bahwa file tidak dapat dibuka melalui viewer
  eksternal yang tersedia.

### Postconditions

- User dapat membuka dan membaca PDF yang menjadi miliknya melalui PDF
  viewer eksternal.
- Sistem tidak menyediakan fitur PDF reader internal pada Fase 1.

### Important Rule

Pada Android, file lokal menjadi sumber akses yang dapat digunakan
ketika masih tersedia. Cloud storage berfungsi sebagai sumber fallback
ketika file lokal tidak tersedia. Pembukaan dari cloud membutuhkan
koneksi internet.

Pada Web, PDF diakses melalui sistem dan dapat dibuka menggunakan PDF
viewer bawaan browser.

---

## UC-09 --- Mark Ebook as Read

### Goal

User menandai ebook sebagai sudah dibaca.

### Actor

User

### Platform

Android / Web

### Preconditions

- User telah login.
- Ebook terdapat dalam collection user.
- Status ebook belum dibaca.

### Main Flow

1.  User melihat ebook dalam collection.
2.  User memilih aksi untuk menandai ebook sebagai sudah dibaca.
3.  Sistem mengubah status ebook menjadi `Sudah Dibaca`.
4.  Sistem menampilkan status terbaru.

### Alternative / Exception Flow

- Jika perubahan status gagal disimpan, sistem mempertahankan status
  sebelumnya dan memberikan informasi kesalahan.

### Postconditions

- Status ebook tersimpan sebagai sudah dibaca.
- Status tersebut dapat dilihat dari platform lain karena Android dan
  Web menggunakan collection yang sama.

---

## UC-10 --- Mark Ebook as Unread

### Goal

User mengubah ebook yang sudah dibaca menjadi belum dibaca.

### Actor

User

### Platform

Android / Web

### Preconditions

- User telah login.
- Ebook terdapat dalam collection user.
- Status ebook adalah sudah dibaca.

### Main Flow

1.  User melihat ebook dalam collection.
2.  User memilih aksi untuk mengubah status menjadi belum dibaca.
3.  Sistem mengubah status ebook menjadi `Belum Dibaca`.
4.  Sistem menampilkan status terbaru.

### Alternative / Exception Flow

- Jika perubahan status gagal disimpan, sistem mempertahankan status
  sebelumnya dan memberikan informasi kesalahan.

### Postconditions

- Status ebook tersimpan sebagai belum dibaca.
- Status terbaru dapat dilihat dari platform lain yang menggunakan
  collection yang sama.

---

# 5. PDF Access and Offline Boundary

Fase 1 menggunakan PDF sebagai satu-satunya format file yang didukung.

Sistem tidak membangun PDF reader internal. Tanggung jawab sistem
berhenti pada penyimpanan, pengelolaan, dan penyediaan akses terhadap
file PDF. Pembacaan dilakukan oleh viewer eksternal:

- Android menggunakan aplikasi PDF viewer yang tersedia pada
  perangkat.
- Web menggunakan PDF viewer yang disediakan browser.
  Pada Android, akses PDF bersifat **local-first**:

1. Jika file PDF lokal masih tersedia, sistem menggunakan file tersebut.
2. Jika file lokal tidak tersedia, sistem mencoba mengambil file dari cloud storage.
3. Pengambilan dari cloud membutuhkan internet.
   Untuk proses penambahan/upload PDF, internet tetap diperlukan. Jika user
   offline, proses upload dibatalkan dan tidak dibuat sebagai pending
   upload. User harus mencoba kembali ketika koneksi tersedia.

# 5. Cross-Platform Use Case Relationship

Android dan Web tidak memiliki collection yang terpisah.

Collection merupakan data bersama yang dikelola melalui Laravel.

```text
                    ┌───────────────┐
                    │     User      │
                    └───────┬───────┘
                            │
                  ┌─────────┴─────────┐
                  │                   │
             ┌────▼─────┐        ┌────▼────┐
             │ Android  │        │   Web   │
             └────┬─────┘        └────┬────┘
                  │                   │
                  └─────────┬─────────┘
                            ↓
                       ┌─────────┐
                       │ Laravel │
                       └────┬────┘
                            │
                   ┌────────┴────────┐
                   ↓                 ↓
                MySQL               R2
              Metadata             Files
```

Contoh:

1.  User menambahkan ebook melalui Android.
2.  Ebook masuk ke collection.
3.  User membuka Web.
4.  Web mengambil collection yang sama.
5.  Ebook yang ditambahkan dari Android tersedia pada Web.
    Hal yang sama berlaku untuk perubahan status baca. Jika status diubah
    dari salah satu platform, platform lainnya membaca status yang sama dari
    sumber data terpusat.

---

# 6. Use Case Relationship Summary

```text
UC-01 Register
      ↓
UC-02 Login
      ↓
UC-04 View Collection
      ├──────────────► UC-08 Access Ebook
      │
      ├──────────────► UC-09 Mark as Read
      │                     ↓
      │                UC-10 Mark as Unread
      │
      ├──────────────► UC-07 Manual Upload
      │
      └──────────────► UC-05 Discover Local Ebook
                              ↓
                       UC-06 Add Local Ebook

UC-03 Logout
      ↓
End Session
```

Diagram tersebut menunjukkan hubungan konseptual antaraktivitas. Detail
navigasi antarlayar dan keputusan user akan dijelaskan pada dokumen
**User Flow**, bukan pada dokumen Use Case ini.

---

# 7. MVP Boundary

Use case yang didefinisikan dalam dokumen ini dibatasi pada kebutuhan
MVP.

## Included

- Register
- Login
- Logout
- View Collection
- Local PDF Discovery
- Add Local PDF
- Manual PDF Upload
- Access/Open PDF melalui external PDF viewer
- Local-first PDF access pada Android
- Read/Unread Status
- Shared Collection Android + Web

## Excluded

- Format ebook selain PDF
- Internal PDF Reader
- Categories
- Tags
- Complex Search
- Bookmarks
- Reading Progress
- Ratings
- Statistics
- Ebook Sharing
- Recommendations
- Social Features
- Admin Dashboard
- Advanced Duplicate Detection
- Automatic Background Synchronization
- Offline Upload Queue / Pending Upload
- Automatic Upload Every Detected PDF

---

# 8. Traceability to Project Goals

Use case dalam dokumen ini mendukung tujuan utama sistem:

| Masalah                                            | Use Case yang Mendukung           |
| -------------------------------------------------- | --------------------------------- |
| Ebook tersebar di perangkat                        | UC-05, UC-06, UC-07               |
| User lupa ebook yang dimiliki                      | UC-04                             |
| Sulit mengetahui status baca                       | UC-09, UC-10                      |
| Collection belum terpusat                          | UC-06, UC-07                      |
| Collection tidak tersedia lintas perangkat         | UC-04, UC-06, UC-07, UC-09, UC-10 |
| Memasukkan ebook manual menjadi pekerjaan tambahan | UC-05, UC-06                      |

---

## Core Principle

> **Aplikasi harus mengurangi pekerjaan pengguna, bukan sekadar
> memindahkan pekerjaan tersebut ke tempat lain.**

Karena itu, local discovery diposisikan sebagai mekanisme untuk
mengurangi langkah manual, sedangkan user tetap memiliki kontrol atas
ebook yang akan dimasukkan ke collection.
