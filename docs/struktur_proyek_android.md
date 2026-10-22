# Struktur Proyek Android

## Ebook Storage & To-Do List

| Item     | Detail                                                                                      |
| -------- | ------------------------------------------------------------------------------------------- |
| Tanggal  | 22 Oktober 2026                                                                             |
| Platform | Android (Kotlin, XML + ViewBinding), dikerjakan di Android Studio                           |
| Acuan    | 00-Project-Overview, 04-API-Specification, 05-Backlog-dan-Jadwal-Chunking, 06-Design-System |

---

## 1. Tujuan dan Konteks

Dokumen ini menetapkan folder, isi, dan aturan kode pada proyek Android agar setiap kode punya tempat yang jelas sebelum pengerjaan dimulai. Dokumen ini dapat dibaca tanpa dokumen lain.

Aplikasi Android adalah klien dari backend Laravel. Android hanya berkomunikasi lewat REST API Laravel (7 endpoint: `register`, `login`, `logout`, `GET /api/ebooks`, `POST /api/ebooks`, `GET /api/ebooks/{id}/file`, `PATCH /api/ebooks/{id}`) dan tidak pernah mengakses database atau Cloudflare R2 langsung. Format request dan respons setiap endpoint ditetapkan di dokumen 04 (API Specification). Aplikasi hanya mendukung file PDF, dan PDF dibuka lewat aplikasi penampil PDF eksternal, bukan penampil buatan sendiri.

Fitur Local Discovery (penemuan PDF otomatis di perangkat) berstatus P2 dan tidak dijadwalkan, sehingga tidak dibuatkan folder maupun kode.

---

## 2. Struktur Folder

Semua path di bawah relatif terhadap package utama proyek (di Android Studio: folder `kotlin+java`, lalu package utama). Nama package ditentukan saat proyek dibuat.

```text
<package utama>
├── data
│   ├── ApiService.kt
│   ├── ApiClient.kt
│   ├── Models.kt
│   └── TokenStore.kt
├── ui
│   ├── Motion.kt
│   ├── login
│   │   ├── LoginActivity.kt
│   │   └── RegisterActivity.kt
│   ├── ebooks
│   │   ├── EbooksActivity.kt
│   │   └── EbookAdapter.kt
│   └── upload
│       └── UploadActivity.kt
└── util
    ├── Connectivity.kt
    ├── FileCopy.kt
    ├── ErrorMessages.kt
    ├── PdfStorage.kt
    └── PdfOpener.kt
```

`MainActivity` bawaan Android Studio dihapus. `LoginActivity` menjadi layar peluncur di `AndroidManifest.xml`. Activity adalah satu kelas Kotlin yang mengendalikan satu layar.

---

## 3. Tugas Setiap Berkas

### Folder `data` (hubungan dengan server dan penyimpanan token)

| Berkas          | Tugas                                                                                                                                                                                                                                                                                                                                                            |
| --------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `ApiService.kt` | Daftar 7 endpoint untuk Retrofit (pustaka pemanggil API). Unduhan PDF memakai mode streaming agar seluruh file tidak dimuat ke memori sekaligus.                                                                                                                                                                                                                 |
| `ApiClient.kt`  | Membuat koneksi Retrofit dan OkHttp. Interceptor (penyela setiap request) menambah header `Accept: application/json`, serta `Authorization: Bearer {token}` bila token ada. Batas waktu tulis dan baca dinaikkan untuk upload hingga 20 MB. Bila server menjawab 401, interceptor menghapus token lalu membuka `LoginActivity` (lihat pengecualian di bagian 6). |
| `Models.kt`     | Semua kelas data dalam satu berkas: data user, data ebook (`id`, `title`, `is_read`, `created_at`, `updated_at`), isi request login dan register, pembungkus respons (`data` dan `meta` berisi `total` dan `read_count`), serta bentuk body error (`message`, `code`, dan `data.existing_id` untuk duplikat). Isinya mengikuti dokumen 04.                       |
| `TokenStore.kt` | Menyimpan, membaca, dan menghapus token login di `SharedPreferences` privat (`MODE_PRIVATE`). Belum terenkripsi, dicatat sebagai utang teknis yang diterima untuk MVP.                                                                                                                                                                                           |

### Folder `ui` (layar)

| Berkas                      | Tugas                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| --------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Motion.kt`                 | Fungsi animasi dari dokumen 06 (Design System): item muncul, respons tekan, getar saat error, dan sorotan item baru.                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| `login/LoginActivity.kt`    | Form login. Saat dibuka, bila token sudah ada langsung berpindah ke `EbooksActivity`. Menampilkan pesan untuk 401, 422, 429, dan tanpa koneksi.                                                                                                                                                                                                                                                                                                                                                                                                                                              |
| `login/RegisterActivity.kt` | Form register (nama, email, password, konfirmasi password). Setelah berhasil (201, tanpa token) kembali ke login dengan pesan sukses.                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| `ebooks/EbooksActivity.kt`  | Layar utama: daftar koleksi, tampilan Empty State (koleksi kosong), tampilan error dengan tombol muat ulang, jumlah ebook yang sudah dibaca dari `meta.read_count`, tombol Tambah PDF, dan tombol logout. Saat item diketuk: bila `filesDir/pdfs/{id}.pdf` ada, langsung dibuka; bila tidak ada dan offline, tampil "Butuh internet untuk membuka PDF"; bila tidak ada dan online, diunduh lalu dibuka; bila server menjawab 404 `file_missing`, tampil "file tidak tersedia". Logout memanggil `POST /api/logout`, lalu selalu menghapus token lokal (juga saat offline) dan folder `pdfs`. |
| `ebooks/EbookAdapter.kt`    | Pengisi komponen daftar `RecyclerView`: satu kartu ebook berisi judul, tanggal, dan chip status baca. Mengetuk chip memanggil `PATCH /api/ebooks/{id}`; bila gagal, status dikembalikan dan pesan ditampilkan.                                                                                                                                                                                                                                                                                                                                                                               |
| `upload/UploadActivity.kt`  | Pemilih file PDF (`ActivityResultContracts.OpenDocument`), menampilkan nama dan ukuran file, menolak file di atas 20 MB, mengirim `POST /api/ebooks` secara multipart, menampilkan progres, dan menonaktifkan tombol selama proses. Bila offline, tampil "Coba lagi saat online" tanpa antrean upload.                                                                                                                                                                                                                                                                                       |

### Folder `util` (fungsi bantu yang dipakai banyak layar)

| Berkas             | Tugas                                                                                                                                                                                                                                                |
| ------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Connectivity.kt`  | Memeriksa ketersediaan internet lewat `ConnectivityManager`.                                                                                                                                                                                         |
| `FileCopy.kt`      | Membaca nama file dari `ContentResolver` (`OpenableColumns.DISPLAY_NAME`) dan menyalin isi `Uri` (alamat file dari pemilih) ke file sementara di `cacheDir`.                                                                                         |
| `ErrorMessages.kt` | Menerjemahkan kode status dan `code` dari server (409 duplikat, 413 atau 422 file terlalu besar atau bukan PDF, 401 sesi habis, 500 gagal di server, tanpa koneksi) menjadi pesan Bahasa Indonesia.                                                  |
| `PdfStorage.kt`    | Mengelola `filesDir/pdfs/{id}.pdf` (penyimpanan privat aplikasi): memeriksa apakah file ada, menulis unduhan ke `{id}.pdf.part` lalu mengganti namanya menjadi `{id}.pdf` hanya setelah unduhan selesai, dan menghapus seluruh folder `pdfs`.        |
| `PdfOpener.kt`     | Membuka PDF di penampil eksternal lewat `FileProvider` dan `Intent ACTION_VIEW` bertipe `application/pdf` dengan `FLAG_GRANT_READ_URI_PERMISSION`. Bila tidak ada aplikasi PDF (`ActivityNotFoundException`), tampil pesan "tidak ada aplikasi PDF". |

---

## 4. Berkas di Luar Package Kotlin

| Berkas                           | Isi                                                                                                                                                                                                                                                        |
| -------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `AndroidManifest.xml`            | Izin `INTERNET`, izin HTTP tanpa enkripsi (cleartext) khusus pengembangan, `LoginActivity` sebagai peluncur, deklarasi semua Activity, deklarasi `FileProvider`, dan orientasi potret (`screenOrientation="portrait"`) untuk semua Activity.               |
| `res/layout`                     | Satu file tata letak per layar dengan nama `activity_<layar>.xml` (contoh: `activity_login.xml`, `activity_ebooks.xml`) dan satu per item daftar dengan nama `item_<nama>.xml` (contoh: `item_ebook.xml`). Layar dan tata letaknya dipasangkan lewat nama. |
| `res/xml/file_paths.xml`         | Daftar lokasi file yang boleh dibagikan `FileProvider` (hanya `filesDir/pdfs`).                                                                                                                                                                            |
| `res/values`, `res/interpolator` | `colors.xml`, `dimens.xml`, `themes.xml`, dan interpolator animasi sesuai dokumen 06 bagian 11. Tanpa `values-night` karena tema gelap tidak dipakai.                                                                                                      |

---

## 5. Pemetaan Fitur ke Tempat

| Fitur                                 | Tempat                                                                             |
| ------------------------------------- | ---------------------------------------------------------------------------------- |
| Register dan login                    | `ui/login`, dengan `data/ApiService`, `data/Models`, `data/TokenStore`             |
| Melewati login bila token sudah ada   | `LoginActivity` memeriksa `TokenStore`                                             |
| Daftar koleksi, Empty State, error    | `ui/ebooks`                                                                        |
| Ubah status baca                      | `EbookAdapter` memanggil `ApiService`                                              |
| Pemilih file dan upload               | `ui/upload`, dengan `util/FileCopy`, `util/Connectivity`, `util/ErrorMessages`     |
| Unduh dan simpan PDF                  | `ApiService` dan `util/PdfStorage`                                                 |
| Membuka PDF                           | `util/PdfOpener`                                                                   |
| Akses offline (lokal dulu) dan logout | `EbooksActivity`, dengan `util/PdfStorage`, `util/Connectivity`, `data/TokenStore` |
| Penanganan 401                        | `data/ApiClient`                                                                   |
| Animasi                               | `ui/Motion.kt`                                                                     |

---

## 6. Aturan

Layar di folder `ui` boleh memanggil kode di folder `data` dan `util`, sedangkan `data` dan `util` tidak boleh memanggil `ui`; satu-satunya pengecualian adalah `ApiClient`, yang boleh membuka `LoginActivity` (dengan flag `NEW_TASK` dan `CLEAR_TASK`) setelah menghapus token ketika server menjawab 401. Aplikasi tidak memakai lapisan `repository` maupun `ViewModel`: layar memanggil `ApiService` secara langsung, dan karena itu semua Activity dikunci pada orientasi potret agar data tidak hilang saat layar diputar. Prinsip umumnya adalah membangun yang paling sedikit tetapi berfungsi: tidak membuat interface yang hanya punya satu implementasi, tidak membuat abstraksi, pengaturan, atau berkas "untuk nanti", tidak menambah dependency di luar Retrofit, Gson, OkHttp logging-interceptor, RecyclerView, Material Components, dan ViewBinding, serta memakai fitur bawaan Android lebih dulu sebelum menulis kode sendiri. Subfolder baru hanya dibuat bila sebuah folder sudah berisi sekitar lima berkas, dan `EbooksActivity` baru dipecah bila melewati sekitar 300 baris. Tidak ada fitur di luar MVP yang boleh ditambahkan atas inisiatif sendiri; bila ditemukan kebutuhan baru atau hal yang bertentangan dengan dokumen ini, hentikan pekerjaan itu dan laporkan lebih dulu kepada pemilik proyek. Setiap Activity memakai ViewBinding, setiap file tata letak mengikuti penamaan di bagian 4, dan seluruh tampilan (warna, ukuran, bentuk, animasi) mengikuti dokumen 06 melalui resource tema, tanpa nilai warna atau ukuran yang ditulis langsung di kode.
