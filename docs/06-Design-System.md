# Design System

## Ebook Storage & To-Do List

| Item     | Detail                                                                             |
| -------- | ---------------------------------------------------------------------------------- |
| Versi    | 1.2 (menggantikan 1.1)                                                             |
| Tanggal  | 19 Oktober 2026                                                                    |
| Platform | Android (Kotlin, XML + ViewBinding) dan Web (Laravel Blade, Tailwind CSS via Vite) |
| Acuan    | 00-Project-Overview, 05-Backlog-dan-Jadwal-Chunking                                |

---

## 1. Tujuan

Dokumen ini menetapkan satu bahasa visual yang dipakai bersama oleh Android dan Web, sehingga kedua platform terasa seperti satu produk. Dokumen ini hanya mengatur **tampilan dan gerak**. Scope fitur, prioritas, dan alur tetap mengikuti dokumen 00 sampai 05.

Dokumen ini sengaja ringkas: setiap aturan di sini harus bisa langsung dipakai saat membuat layar, bukan dekorasi.

---

## 2. Prinsip

Lima keputusan, dan turunannya:

| Keputusan                 | Artinya dalam praktik                                                                                                                  |
| ------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| **Flat design**           | Tanpa bayangan, tanpa gradien, tanpa tekstur. Kedalaman dibuat dengan perbedaan warna isian dan garis tepi 1px.                        |
| **Rounded corner**        | Semua elemen bersudut membulat dari satu skala radius (bagian 5). Tombol dan chip berbentuk pil.                                       |
| **Warna 60-30-10**        | Putih 60%, merah 30%, hitam 10% (bagian 3). Variasi hanya berupa turunan dari ketiganya.                                               |
| **Gerak sebagai respons** | Animasi adalah cara aplikasi "berbicara" kepada pengguna: ini bisa diklik, aksi diterima, sedang diproses, berhasil, gagal (bagian 6). |
| **Desktop bukan mobile**  | Web desktop memakai lebar layar, grid, dan spasi yang rapat. Tata letak kolom sempit hanya untuk layar kecil (bagian 8).               |

Satu prinsip tambahan: **satu aksi utama per layar**. Merah paling pekat dipakai untuk aksi terpenting agar langsung terlihat.

---

## 3. Warna

### 3.1 Aturan 60-30-10

Bobot ini adalah **bobot visual**, bukan hitungan piksel yang presisi.

| Warna | Porsi | Dipakai untuk                                                                            |
| ----- | ----- | ---------------------------------------------------------------------------------------- |
| Putih | 60%   | Latar halaman, kartu, input, dialog, ruang kosong                                        |
| Merah | 30%   | Bilah atas, tombol utama, hover/fokus/tekan, tautan, ikon Empty State, sorotan item baru |
| Hitam | 10%   | Teks, ikon, garis kontrol, chip "Sudah Dibaca", snackbar                                 |

Aturan turunan:

1. Tidak ada warna di luar tiga keluarga ini. Variasi (hover, tint, abu) selalu turunan putih, merah, atau hitam.
2. Maksimal **satu** blok merah pekat berukuran besar per layar (bilah atas atau panel samping). Selebihnya merah hanya pada elemen kecil.
3. Merah **tidak** dipakai sebagai teks di atas hitam (kontras hanya 3,4:1).
4. Tombol berisi merah tidak dipakai untuk aksi yang merusak atau menandai kesalahan. Error dibedakan lewat bagian 3.4.

### 3.2 Palet dasar

| Nama         | Hex       | Keluarga | Keterangan                                 |
| ------------ | --------- | -------- | ------------------------------------------ |
| `white`      | `#FFFFFF` | Putih    | Latar halaman, isi input                   |
| `white-soft` | `#F6F3F3` | Putih    | Isi kartu, area sekunder                   |
| `white-line` | `#E4DCDC` | Putih    | Garis tepi dekoratif                       |
| `red-50`     | `#FDECEC` | Merah    | Tint: hover ringan, sorotan, Empty State   |
| `red-600`    | `#C62828` | Merah    | **Merah utama**                            |
| `red-700`    | `#A91E1E` | Merah    | Hover                                      |
| `red-800`    | `#8E1818` | Merah    | Tekan, dan teks error                      |
| `black`      | `#141010` | Hitam    | Teks utama, chip "Sudah Dibaca"            |
| `black-soft` | `#2E2626` | Hitam    | Hover pada elemen hitam                    |
| `gray-600`   | `#5A5050` | Hitam    | Teks sekunder                              |
| `gray-400`   | `#8A8080` | Hitam    | Garis tepi kontrol (input, tombol outline) |

Abu (`gray-*`) dihitung sebagai bagian dari keluarga hitam karena merupakan hitam yang dilunakkan.

### 3.3 Token semantik

Kode tidak boleh memakai hex langsung. Pakai token di bawah. Hanya ada satu tema (terang).

| Token             | Nilai        | Dipakai untuk                              |
| ----------------- | ------------ | ------------------------------------------ |
| `background`      | `white`      | Latar halaman                              |
| `surface`         | `white-soft` | Kartu, dialog, bottom sheet                |
| `outline`         | `white-line` | Garis tepi dekoratif (kartu, pemisah)      |
| `outline-strong`  | `gray-400`   | Garis tepi kontrol (input, tombol outline) |
| `primary`         | `red-600`    | Tombol utama, bilah atas, tautan, fokus    |
| `primary-hover`   | `red-700`    | Hover pada elemen `primary`                |
| `primary-pressed` | `red-800`    | Tekan pada elemen `primary`                |
| `on-primary`      | `white`      | Teks/ikon di atas `primary`                |
| `primary-tint`    | `red-50`     | Hover ringan, sorotan, latar Empty State   |
| `ink`             | `black`      | Chip "Sudah Dibaca", snackbar              |
| `ink-hover`       | `black-soft` | Hover pada elemen `ink`                    |
| `on-ink`          | `white`      | Teks/ikon di atas `ink`                    |
| `text`            | `black`      | Teks utama                                 |
| `text-muted`      | `gray-600`   | Teks sekunder (tanggal, keterangan)        |
| `error`           | `red-800`    | Teks dan garis tepi input yang salah       |

### 3.4 Error dan merah merek

Merah sekarang warna merek, jadi error tidak boleh hanya "merah". Error selalu memakai **semua** penanda berikut:

- warna `error` (`red-800`, lebih gelap dari `primary`);
- ikon peringatan di depan pesan;
- kotak `primary-tint` di belakang pesan pada level layar;
- pada input: garis tepi `error` dan gerak getar singkat (bagian 6.3).

Status sukses tidak diberi warna sendiri: sukses memakai `ink` (snackbar hitam dengan ikon centang).

### 3.5 Aturan kontras (sudah dihitung)

Standar minimal: teks biasa 4,5:1, komponen UI 3:1.

| Kombinasi                       | Rasio  | Boleh untuk                      |
| ------------------------------- | ------ | -------------------------------- |
| `black` di atas `white`         | 18,9:1 | Teks utama                       |
| `black` di atas `white-soft`    | 17,1:1 | Teks di kartu                    |
| `black` di atas `red-50`        | 16,5:1 | Teks di kotak tint               |
| `gray-600` di atas `white`      | 7,8:1  | Teks sekunder                    |
| `gray-600` di atas `white-soft` | 7,1:1  | Teks sekunder di kartu           |
| `white` di atas `red-600`       | 5,6:1  | Tombol utama, bilah atas         |
| `white` di atas `red-700`       | 7,3:1  | Hover tombol utama               |
| `white` di atas `red-800`       | 9,2:1  | Tekan tombol utama               |
| `red-600` di atas `white`       | 5,6:1  | Tautan, tombol teks              |
| `red-600` di atas `white-soft`  | 5,1:1  | Tautan di kartu                  |
| `red-600` di atas `red-50`      | 4,9:1  | Ikon dan teks di kotak tint      |
| `white` di atas `black`         | 18,9:1 | Chip "Sudah Dibaca", snackbar    |
| `red-800` di atas `red-50`      | 8,0:1  | Pesan error                      |
| `gray-400` di atas `white`      | 3,8:1  | Garis tepi kontrol (komponen UI) |

**Dilarang:**

- `white-line` (`#E4DCDC`) sebagai garis kontrol. Rasionya hanya 1,35:1; hanya untuk garis dekoratif.
- `red-600` sebagai teks di atas `black`.
- Menyampaikan informasi hanya lewat warna. Status baca selalu disertai ikon dan teks (bagian 7.4).

### 3.6 Penerapan per layar

| Layar            | Putih (60%)   | Merah (30%)                                        | Hitam (10%)               |
| ---------------- | ------------- | -------------------------------------------------- | ------------------------- |
| Login / Register | Panel form    | Panel samping (desktop) atau pita atas (mobile)    | Teks, ikon                |
| Daftar Ebook     | Latar, kartu  | Bilah atas, tombol Tambah PDF, hover kartu         | Teks, chip "Sudah Dibaca" |
| Upload           | Form, kartu   | Tombol Upload, bilah atas, bar progres             | Teks, ikon                |
| Empty State      | Latar halaman | Bilah atas, ikon dan tombol di atas `primary-tint` | Teks                      |

---

## 4. Tipografi

Memakai **font sistem** (Roboto di Android, `system-ui` di Web). Tidak ada file font yang perlu diunduh, sehingga tidak menambah dependensi.

| Gaya       | Ukuran / tinggi baris | Bobot | Dipakai untuk                 |
| ---------- | --------------------- | ----- | ----------------------------- |
| `title-lg` | 24 / 32               | 600   | Judul layar                   |
| `title-md` | 18 / 24               | 600   | Judul ebook di daftar, dialog |
| `body`     | 16 / 24               | 400   | Teks isi, isian form          |
| `body-sm`  | 14 / 20               | 400   | Tanggal, keterangan           |
| `label`    | 14 / 20               | 500   | Tombol, chip, label input     |
| `caption`  | 12 / 16               | 400   | Pesan bantu di bawah input    |

- Android memakai `sp`, Web memakai `rem` (1rem = 16px).
- Teks tidak boleh lebih kecil dari 12.
- Judul ebook dipotong dua baris dengan elipsis.

---

## 5. Bentuk, Jarak, dan Kedalaman

### 5.1 Radius

| Token         | Nilai | Dipakai untuk                                       |
| ------------- | ----- | --------------------------------------------------- |
| `radius-sm`   | 8     | Checkbox, elemen kecil                              |
| `radius-md`   | 16    | Input, snackbar                                     |
| `radius-lg`   | 24    | Kartu, dialog, bottom sheet                         |
| `radius-full` | pil   | Tombol, chip, indikator progres, tombol aksi tambah |

Elemen di dalam wadah membulat memakai radius yang **lebih kecil** dari wadahnya, agar lekukannya sejajar.

### 5.2 Jarak (kelipatan 4)

`4 · 8 · 12 · 16 · 24 · 32 · 48`

Jarak dibedakan menurut jenis layar. Desktop sengaja lebih rapat.

| Ukuran              | Mobile (Android, Web < 1024) | Desktop (Web ≥ 1024)  |
| ------------------- | ---------------------------- | --------------------- |
| Padding halaman     | 16                           | 24                    |
| Padding dalam kartu | 16                           | 12                    |
| Jarak antar kartu   | 12                           | 12                    |
| Tinggi tombol       | 48                           | 40                    |
| Tinggi input        | 56                           | 44                    |
| Tinggi chip         | 48                           | 36                    |
| Tinggi bilah atas   | 64                           | 56                    |
| Area sentuh minimum | 48 dp / 44 px                | tidak berlaku (mouse) |

Jarak 32 dan 48 hanya dipakai di layar mobile atau di antara bagian halaman yang benar-benar terpisah. Jangan dipakai sebagai padding rutin di desktop.

### 5.3 Kedalaman

- Elevasi dan `box-shadow` **tidak dipakai**.
- Pemisah antar-area memakai `outline` 1px, atau perbedaan isian (`white` dan `white-soft`).
- Keadaan hover, tekan, dan fokus diwujudkan lewat perubahan warna, garis, dan gerak kecil, bukan bayangan.

---

## 6. Gerak sebagai Respons

Animasi di sini adalah cara aplikasi berbicara kepada pengguna. Setiap animasi harus menjawab satu pertanyaan. Animasi yang tidak menjawab pertanyaan apa pun tidak dibuat.

### 6.1 Pertanyaan pengguna dan jawaban lewat gerak

| Pertanyaan pengguna           | Jawaban lewat gerak                                                                |
| ----------------------------- | ---------------------------------------------------------------------------------- |
| Apakah ini bisa diklik?       | **Hover** (Web): warna bergeser satu tingkat, kursor tangan, ikon bergerak sedikit |
| Apakah aplikasi melihat saya? | **Fokus**: garis berubah menjadi merah dengan transisi, cincin fokus muncul        |
| Apakah aksi saya diterima?    | **Tekan**: skala turun ke 0,97, warna bergeser ke `primary-pressed`                |
| Sedang diproses?              | Tombol berubah menjadi indikator, bar progres upload bergerak                      |
| Berhasil?                     | Ikon centang muncul, item baru disorot `primary-tint` yang memudar, snackbar masuk |
| Gagal?                        | Pesan error muncul dan kolom bergetar singkat                                      |

Hover hanya ada di Web. Android menyampaikan pesan yang sama lewat ripple, tekan, dan fokus. Pesannya sama, pemicunya berbeda.

### 6.2 Durasi dan easing

| Token      | ms  | Dipakai untuk                                        |
| ---------- | --- | ---------------------------------------------------- |
| `dur-fast` | 100 | Hover, tekan, ripple                                 |
| `dur-base` | 200 | Perubahan status, fokus input, chip, fade kecil      |
| `dur-slow` | 300 | Elemen daftar muncul, snackbar, getar error, progres |
| `dur-page` | 400 | Dialog, bottom sheet, perpindahan layar              |

Tidak ada animasi lebih dari 400 ms.

| Token           | Kurva                          | Dipakai untuk                       |
| --------------- | ------------------------------ | ----------------------------------- |
| `ease-standard` | `cubic-bezier(0.4, 0, 0.2, 1)` | Perubahan di tempat (warna, ukuran) |
| `ease-out`      | `cubic-bezier(0, 0, 0.2, 1)`   | Elemen **masuk**                    |
| `ease-in`       | `cubic-bezier(0.4, 0, 1, 1)`   | Elemen **keluar**                   |

Hindari gerak linear dan efek memantul (bounce). Keluar sedikit lebih cepat dari masuk.

### 6.3 Pola gerak per keadaan

| Elemen             | Hover (Web)                                                        | Fokus                           | Tekan                             |
| ------------------ | ------------------------------------------------------------------ | ------------------------------- | --------------------------------- |
| Tombol `primary`   | Isi `primary-hover`; ikon di dalamnya bergerak (plus berputar 90°) | Cincin 2px `primary`, jarak 2px | Skala 0,97, isi `primary-pressed` |
| Tombol `secondary` | Isi `black`, teks putih                                            | Cincin                          | Skala 0,97                        |
| Tombol `text`      | Latar `primary-tint`                                               | Cincin                          | Skala 0,97                        |
| Tombol `inverse`   | Isi `primary-tint` (di atas bilah merah)                           | Cincin putih                    | Skala 0,97                        |
| Kartu ebook        | Garis berubah ke `primary`, naik 2px, judul berwarna `primary`     | Cincin                          | Skala 0,98                        |
| Chip belum dibaca  | Isi `primary-tint`, garis `primary`                                | Cincin                          | Skala 0,97                        |
| Chip sudah dibaca  | Isi `ink-hover`                                                    | Cincin                          | Skala 0,97                        |
| Input              | Garis menjadi `black`                                              | Garis `primary` + cincin 2px    | -                                 |
| Tautan             | Garis bawah muncul (warna garis dari transparan ke `primary`)      | Cincin                          | -                                 |
| Tombol ikon        | Lingkaran `primary-tint` (di bilah merah: putih 16%)               | Cincin                          | Skala 0,95                        |

Semua perubahan hover memakai `dur-fast` atau `dur-base` dengan `ease-standard`.

### 6.4 Pola gerak lainnya

| Situasi                | Gerak                                                                    | Durasi / easing      |
| ---------------------- | ------------------------------------------------------------------------ | -------------------- |
| Item daftar muncul     | Fade + naik 8px. Jeda antar item 30 ms, maksimal 6 item pertama          | `slow`, `out`        |
| Status baca berubah    | Warna chip bertransisi, ikon centang muncul dengan fade + skala 0,8 ke 1 | `base`, `standard`   |
| Ebook baru ditambahkan | Latar kartu `primary-tint` lalu memudar ke `surface`                     | `slow`, `standard`   |
| Input salah            | Pesan muncul fade + naik 8px; kolom bergetar ±4px, tiga kali             | `slow`, `standard`   |
| Pindah layar           | Fade + geser 16px pendek, bukan geser penuh                              | `page`, `out` / `in` |
| Dialog, bottom sheet   | Fade + naik 16px; scrim memudar                                          | `page`, `out`        |
| Snackbar               | Fade + naik 8px                                                          | `slow`, `out`        |
| Progres upload         | Bar bertransisi mulus mengikuti nilai terbaru                            | `slow`, `standard`   |
| Memuat (loading)       | Placeholder berdenyut opasitas 0,5 ke 1, siklus 1,2 detik                | `ease-standard`      |

### 6.5 Aturan gerak

1. **Hanya animasikan `transform`, `opacity`, dan warna.** Jangan animasikan `width`, `height`, `margin`, atau `top` pada Web. Pengecualian: bar progres memakai `transform: scaleX`.
2. Satu gerakan utama per layar pada satu waktu. Hover pada satu elemen tidak memicu gerak elemen lain.
3. Gerak tidak boleh menghalangi aksi: user dapat menekan tombol sebelum animasi masuk selesai.
4. **Hover hanya untuk perangkat yang punya hover** agar tidak "menempel" di layar sentuh. Pada Tailwind v4 varian `hover:` sudah otomatis begitu (bagian 10.6).
5. Hormati preferensi **kurangi gerakan**: durasi mendekati 0, gerak spasial (naik, geser, getar, berputar) dihapus, perubahan warna tetap ada agar respons masih terasa. Web memakai `prefers-reduced-motion`; Android otomatis mengikuti skala animasi sistem.

---

## 7. Komponen

Komponen dibatasi pada yang dibutuhkan wireframe di dokumen 05 (Login, Register, Daftar Ebook, Upload, Empty State).

### 7.1 Tombol

| Varian      | Tampilan                                            | Dipakai untuk                                  |
| ----------- | --------------------------------------------------- | ---------------------------------------------- |
| `primary`   | Isi `primary`, teks `on-primary`                    | Satu aksi utama per layar (Masuk, Upload)      |
| `secondary` | Isi transparan, garis `outline-strong`, teks `text` | Aksi sekunder (Coba lagi, Batal)               |
| `text`      | Tanpa isi, teks `primary`                           | Aksi tersier (Daftar akun)                     |
| `inverse`   | Isi `white`, teks `primary`                         | Aksi di atas bilah merah (Tambah PDF di bilah) |

- Bentuk pil. Tinggi dan padding mengikuti tabel 5.2 (padding horizontal 24 di mobile, 20 di desktop).
- Maksimal **satu** tombol `primary` atau `inverse` sebagai aksi utama per layar.
- Disabled: opasitas 0,38, kursor `not-allowed`, tidak bisa ditekan. Saat proses berjalan (misalnya upload), tombol diganti indikator progres dan tidak bisa ditekan dua kali (sejalan dengan C17).

### 7.2 Input teks

- Tinggi mengikuti tabel 5.2, `radius-md`, isi `white`, garis 1px `outline-strong`.
- Hover: garis `black`. Fokus: garis `primary` (tebal tetap 1px, ditambah cincin 2px pada Web).
- Error: garis `error`, pesan di bawah dengan gaya `caption` berwarna `error` dan ikon peringatan.
- Label selalu terlihat di atas input, bukan hanya placeholder.

### 7.3 Kartu ebook

```text
┌────────────────────────────────────────────┐
│  Clean Code                                │  title-md, maks 2 baris
│  3 Okt 2026                                │  body-sm, text-muted
│                                            │
│  ( ✓ Sudah Dibaca )                        │  chip status
└────────────────────────────────────────────┘
   radius-lg · surface · outline 1px · padding 16 (mobile) / 12 (desktop)
```

- Seluruh kartu dapat ditekan untuk membuka PDF (UC-08).
- Chip status ditekan untuk mengubah status baca (UC-09 dan UC-10).
- Hover kartu: lihat tabel 6.3.

### 7.4 Chip status baca

| Status       | Tampilan                                                                   |
| ------------ | -------------------------------------------------------------------------- |
| Belum Dibaca | Isi transparan, garis `outline-strong`, ikon lingkaran kosong, teks `text` |
| Sudah Dibaca | Isi `ink`, ikon centang, teks `on-ink`                                     |

Status dibedakan oleh **isi, ikon, dan teks sekaligus**, tidak hanya warna. "Sudah Dibaca" sengaja hitam, bukan merah, supaya tidak terbaca sebagai error atau aksi.

### 7.5 Tombol aksi tambah ebook

- Pil dengan ikon plus dan teks "Tambah PDF".
- **Web:** varian `inverse` di dalam bilah atas merah, sejajar dengan judul. Tidak mengambang.
- **Android:** varian `primary` mengambang di kanan bawah.
- Ini adalah aksi utama satu-satunya di layar daftar.

### 7.6 Bilah atas

- Latar `primary`, teks dan ikon `on-primary`, tanpa garis bawah dan tanpa bayangan. Tinggi mengikuti tabel 5.2.
- Web desktop: penuh selebar layar; isinya dibatasi wadah yang sama dengan konten (bagian 8). Kiri: nama aplikasi. Kanan: ringkasan "5 dari 12 sudah dibaca" (dari `meta.read_count`), tombol Tambah PDF, nama user, dan tombol ikon logout.
- Android: judul layar, ringkasan di bawahnya dengan `body-sm`, logout berupa ikon 48 dp. Warna status bar sama dengan bilah atas.

### 7.7 Empty state

- Kotak `primary-tint` dengan `radius-lg`, berisi ikon 64 dp berwarna `primary`, satu kalimat berwarna `text`, dan satu tombol `primary` "Tambah PDF".
- Desktop: kotak berada di bawah bilah atas dan lebarnya mengikuti wadah, bukan kartu kecil di tengah halaman kosong. Isi kotak berupa baris: ikon di kiri, teks dan tombol di kanan.
- Mobile: isi ditumpuk ke bawah dan diposisikan di tengah vertikal.
- Tanpa ilustrasi.

### 7.8 Umpan balik

| Jenis                                               | Komponen                                                                                       |
| --------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| Sukses singkat                                      | Snackbar pil, isi `ink`, ikon centang, hilang otomatis 4 detik                                 |
| Error yang bisa dicoba lagi (offline, gagal upload) | Kotak `primary-tint` berisi ikon peringatan, pesan `error`, dan tombol `secondary` "Coba lagi" |
| Error form                                          | Pesan di bawah input yang bermasalah (aturan 3.4)                                              |
| Konfirmasi                                          | Dialog `radius-lg`, dua tombol: `text` (batal) dan `primary` (lanjut)                          |
| Memuat daftar                                       | Placeholder kartu berdenyut (tiga di mobile, enam di desktop)                                  |
| Upload                                              | Bar progres pil tipis, tinggi 8, trek `outline`, isi `primary`                                 |

Pesan memakai bahasa Indonesia yang jelas dan menyebut langkah berikutnya, misalnya "Tidak ada koneksi. Coba lagi saat online." (sesuai dokumen 01 UC-06 dan UC-07).

### 7.9 Ikon

- Satu set: **Material Symbols Rounded**, gaya outline, bobot 400, ukuran 24 (64 untuk empty state).
- Android memakai vector drawable. Web memakai SVG inline, tanpa pustaka ikon eksternal.
- Warna ikon mengikuti warna teks di sampingnya.

---

## 8. Tata Letak

### 8.1 Web

| Lebar           | Wadah                  | Daftar ebook                                          | Padding halaman |
| --------------- | ---------------------- | ----------------------------------------------------- | --------------- |
| < 600 px        | Penuh                  | 1 kolom                                               | 16              |
| 600 sampai 1023 | Penuh                  | 2 kolom                                               | 24              |
| ≥ 1024          | Maks 1200, rata tengah | Grid otomatis, kolom minimal 320 (±3 kolom pada 1200) | 24              |

- Pada desktop, bilah atas selebar layar sedangkan isinya dibatasi wadah 1200.
- Grid: `repeat(auto-fill, minmax(320px, 1fr))`, jarak 12.
- **Login dan Register di desktop:** dua kolom. Kiri panel `primary` (nama aplikasi dan satu kalimat nilai, teks putih), kanan panel putih berisi form selebar sekitar 360 yang menempel pada kolomnya. Bukan kartu kecil melayang di halaman kosong.
- **Login dan Register di mobile:** pita `primary` tipis berisi nama aplikasi di atas, form di bawahnya.
- **Upload di desktop:** dua kolom. Kiri: form (judul, pilih file, tombol, bar progres). Kanan: kotak `primary-tint` berisi aturan upload ("Hanya PDF, maksimal 20 MB", "PDF yang sama tidak ditambahkan dua kali"). Di mobile, kotak aturan berada di atas form.

### 8.2 Android

Satu kolom pada semua lebar (MVP tidak menangani tablet). Padding 16, jarak antar kartu 12.

### 8.3 Urutan layar

Sama di kedua platform: **Login → Daftar Ebook → (Upload / Buka PDF)**.

---

## 9. Aksesibilitas (ringkas)

- Kontras mengikuti tabel 3.5.
- Area sentuh sesuai bagian 5.2 untuk layar sentuh.
- Setiap ikon yang berdiri sendiri punya label (`contentDescription` di Android, `aria-label` di Web).
- Fokus keyboard terlihat jelas di Web: cincin 2px `primary` dengan jarak 2px (`:focus-visible`). Di atas bilah merah, cincin berwarna putih.
- Ukuran teks mengikuti pengaturan ukuran font sistem (sp di Android, rem di Web).
- Preferensi kurangi gerakan dihormati (aturan 6.5).
- Hover tidak pernah menjadi satu-satunya cara mengetahui bahwa sebuah elemen dapat diklik; elemen interaktif tetap punya bentuk atau teks yang jelas.

---

## 10. Implementasi Web (Laravel Blade, Tailwind CSS)

Web memakai **Tailwind CSS lewat Vite dan Node.js** (sudah terpasang). Dokumen ini mengasumsikan **Tailwind v4**, yaitu versi bawaan proyek Laravel baru, dengan token ditulis di CSS lewat `@theme`. Bila proyek ternyata memakai v3, token yang sama dipindah ke `theme.extend` di `tailwind.config.js`, dan hover perlu dibungkus manual (lihat catatan 10.6).

> Keputusan ini menggantikan butir C01 pada dokumen 05 ("tanpa Tailwind, Vite, atau Node.js"). Perubahan itu perlu dicatat pada Log Perubahan Rancangan (dokumen 05 bagian 9).

### 10.1 Berkas yang terlibat

```text
resources/
├── css/app.css                      ← token dan dasar (10.2)
├── js/app.js                        ← sorotan ebook baru (10.5)
└── views/
    ├── layouts/app.blade.php        ← memuat @vite
    └── components/
        ├── button.blade.php         ← 10.4
        ├── card.blade.php
        ├── chip.blade.php
        └── field.blade.php
```

Layout Blade memuat aset dengan:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Saat pengembangan jalankan `npm run dev`. Sebelum demo atau deploy jalankan `npm run build` agar `public/build` ikut terbentuk.

### 10.2 Token (`resources/css/app.css`)

Palet bawaan Tailwind, bayangan, dan ukuran teks bawaan **dikosongkan**, sehingga hanya token design system yang bisa dipakai. Ini menegakkan aturan "tanpa warna di luar putih, merah, hitam" dan "tanpa bayangan" langsung oleh alat.

```css
@import "tailwindcss";
@source "../views/**/*.blade.php";

@theme {
    /* Warna: hanya token semantik (bagian 3.3) */
    --color-*: initial;
    --color-white: #ffffff;
    --color-background: #ffffff;
    --color-surface: #f6f3f3;
    --color-outline: #e4dcdc;
    --color-outline-strong: #8a8080;
    --color-primary: #c62828;
    --color-primary-hover: #a91e1e;
    --color-primary-pressed: #8e1818;
    --color-on-primary: #ffffff;
    --color-primary-tint: #fdecec;
    --color-ink: #141010;
    --color-ink-hover: #2e2626;
    --color-on-ink: #ffffff;
    --color-text: #141010;
    --color-text-muted: #5a5050;
    --color-error: #8e1818;

    /* Bentuk (bagian 5.1) */
    --radius-*: initial;
    --radius-sm: 8px;
    --radius-md: 16px;
    --radius-lg: 24px;
    --radius-full: 999px;

    /* Flat: tanpa bayangan */
    --shadow-*: initial;
    --inset-shadow-*: initial;
    --drop-shadow-*: initial;

    /* Tipografi (bagian 4) */
    --font-sans: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    --text-*: initial;
    --text-title-lg: 1.5rem;
    --text-title-lg--line-height: 2rem;
    --text-title-md: 1.125rem;
    --text-title-md--line-height: 1.5rem;
    --text-body: 1rem;
    --text-body--line-height: 1.5rem;
    --text-body-sm: 0.875rem;
    --text-body-sm--line-height: 1.25rem;
    --text-label: 0.875rem;
    --text-label--line-height: 1.25rem;
    --text-caption: 0.75rem;
    --text-caption--line-height: 1rem;

    /* Titik lebar (bagian 8.1): sm = 600, lg = 1024 */
    --breakpoint-sm: 600px;

    /* Easing (bagian 6.2) */
    --ease-standard: cubic-bezier(0.4, 0, 0.2, 1);
    --ease-out: cubic-bezier(0, 0, 0.2, 1);
    --ease-in: cubic-bezier(0.4, 0, 1, 1);

    /* Animasi bernama (bagian 6.4) */
    --animate-enter: enter 300ms var(--ease-out) both;
    --animate-shake: shake 300ms var(--ease-standard);

    @keyframes enter {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: none;
        }
    }
    @keyframes shake {
        0%,
        100% {
            transform: translateX(0);
        }
        20%,
        60% {
            transform: translateX(-4px);
        }
        40%,
        80% {
            transform: translateX(4px);
        }
    }
}

@layer base {
    body {
        @apply bg-background font-sans text-body text-text antialiased;
    }
    :focus-visible {
        outline: 2px solid var(--color-primary);
        outline-offset: 2px;
    }
    .topbar :focus-visible {
        outline-color: var(--color-white);
    } /* cincin putih di atas bilah merah */
}

/* Kurangi gerakan (aturan 6.5 butir 5): durasi mendekati 0, gerak spasial dihapus */
@media (prefers-reduced-motion: reduce) {
    *,
    ::before,
    ::after {
        animation-duration: 1ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 1ms !important;
    }
    .animate-shake {
        animation: none;
    }
}
```

### 10.3 Pemetaan token ke class

| Token design system              | Class Tailwind                                                                                        |
| -------------------------------- | ----------------------------------------------------------------------------------------------------- |
| Warna                            | `bg-primary`, `text-on-primary`, `border-outline-strong`, `bg-primary-tint`, dst.                     |
| Radius                           | `rounded-sm` · `rounded-md` · `rounded-lg` · `rounded-full`                                           |
| Jarak (kelipatan 4)              | Skala bawaan Tailwind sudah 4 px: `p-1`=4, `p-2`=8, `p-3`=12, `p-4`=16, `p-6`=24, `p-8`=32, `p-12`=48 |
| Tipografi                        | `text-title-lg` · `text-title-md` · `text-body` · `text-body-sm` · `text-label` · `text-caption`      |
| Durasi (bagian 6.2)              | `duration-100` (fast) · `duration-200` (base) · `duration-300` (slow) · `duration-400` (page)         |
| Easing                           | `ease-standard` · `ease-out` · `ease-in`                                                              |
| Animasi bernama                  | `animate-enter` · `animate-shake`                                                                     |
| Mobile atau desktop (bagian 5.2) | Tulis ukuran mobile dulu, lalu `lg:` untuk desktop yang lebih rapat                                   |

Pola ukuran mobile ke desktop (tabel 5.2):

| Ukuran            | Class                  |
| ----------------- | ---------------------- |
| Padding halaman   | `px-4 sm:px-6`         |
| Padding kartu     | `p-4 lg:p-3`           |
| Tinggi tombol     | `min-h-12 lg:min-h-10` |
| Tinggi input      | `min-h-14 lg:min-h-11` |
| Tinggi chip       | `min-h-12 lg:min-h-9`  |
| Tinggi bilah atas | `min-h-16 lg:min-h-14` |

### 10.4 Komponen Blade

Keadaan hover, fokus, tekan, dan nonaktif ditulis **sekali** di komponen ini, bukan diulang di setiap halaman. Tailwind memindai berkas Blade, jadi nama class harus ditulis utuh seperti di bawah (tidak disusun dari potongan string).

**`components/button.blade.php`** (varian: `primary`, `secondary`, `text`, `inverse`)

```blade
@props(['variant' => 'primary', 'type' => 'button'])
@php
$variants = [
    'primary'   => 'border-transparent bg-primary text-on-primary hover:bg-primary-hover active:bg-primary-pressed',
    'secondary' => 'border-outline-strong bg-transparent text-text hover:border-ink hover:bg-ink hover:text-on-ink',
    'text'      => 'border-transparent bg-transparent text-primary hover:bg-primary-tint',
    'inverse'   => 'border-transparent bg-white text-primary hover:bg-primary-tint',
];
@endphp
<button type="{{ $type }}" {{ $attributes->class([
    'group inline-flex min-h-12 items-center justify-center gap-2 rounded-full border px-6 text-label font-medium lg:min-h-10 lg:px-5',
    'cursor-pointer transition duration-100 ease-standard active:scale-[0.97] motion-reduce:active:scale-100',
    'disabled:cursor-not-allowed disabled:opacity-[0.38] disabled:active:scale-100',
    $variants[$variant],
]) }}>{{ $slot }}</button>
```

Ikon plus pada tombol "Tambah PDF" berputar saat hover karena tombol bertanda `group`:

```blade
<x-button variant="inverse" type="submit">
    <svg class="size-6 transition-transform duration-200 ease-standard group-hover:rotate-90 motion-reduce:transform-none" ...>...</svg>
    Tambah PDF
</x-button>
```

**`components/card.blade.php`** (kartu ebook)

```blade
@props(['new' => false])
<article {{ $attributes->class([
    'group grid cursor-pointer gap-2 rounded-lg border border-outline bg-surface p-4 lg:p-3',
    'transition duration-200 ease-standard',
    'hover:-translate-y-0.5 hover:border-primary active:scale-[0.98]',
    'motion-reduce:hover:translate-y-0 motion-reduce:active:scale-100',
    'data-[new]:bg-primary-tint',
]) }} @if($new) data-new @endif>
    {{ $slot }}
</article>
```

Judul di dalam slot memakai `class="line-clamp-2 text-title-md font-semibold transition-colors duration-100 group-hover:text-primary"`, dan tanggal memakai `text-body-sm text-text-muted`.

**`components/chip.blade.php`** (status baca)

```blade
@props(['pressed' => false])
<button type="submit" aria-pressed="{{ $pressed ? 'true' : 'false' }}" {{ $attributes->class([
    'inline-flex min-h-12 cursor-pointer items-center gap-2 rounded-full border border-outline-strong bg-transparent px-4 text-label font-medium text-text lg:min-h-9',
    'transition duration-200 ease-standard active:scale-[0.97] motion-reduce:active:scale-100',
    'not-aria-pressed:hover:border-primary not-aria-pressed:hover:bg-primary-tint',
    'aria-pressed:border-ink aria-pressed:bg-ink aria-pressed:text-on-ink',
    'aria-pressed:hover:border-ink-hover aria-pressed:hover:bg-ink-hover',
]) }}>{{ $slot }}</button>
```

Isi slot selalu berupa ikon dan teks: lingkaran kosong dan "Belum Dibaca", atau centang dan "Sudah Dibaca" (aturan 7.4: status tidak hanya lewat warna).

**`components/field.blade.php`** (input dengan label dan pesan)

```blade
@props(['name', 'label', 'type' => 'text', 'hint' => null])
@php $error = $errors->first($name); @endphp
<div class="grid gap-1">
    <label for="{{ $name }}" class="text-label font-medium">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        @if($error) aria-invalid="true" aria-describedby="{{ $name }}-msg" @endif
        {{ $attributes->class([
            'min-h-14 rounded-md border bg-background px-4 text-body text-text lg:min-h-11',
            'transition-colors duration-200 ease-standard focus:border-primary',
            $error ? 'animate-shake border-error' : 'border-outline-strong not-focus:hover:border-ink',
        ]) }}>
    @if($error)
        <p id="{{ $name }}-msg" class="flex animate-enter items-start gap-1 text-caption text-error">
            <x-icon.warning class="size-4 shrink-0" /> {{ $error }}
        </p>
    @elseif($hint)
        <p class="text-caption text-text-muted">{{ $hint }}</p>
    @endif
</div>
```

`x-icon.warning` adalah komponen SVG inline yang dibuat sendiri (aturan 7.9: tanpa pustaka ikon eksternal).

**Elemen lain: cukup class, tidak perlu komponen**

| Elemen              | Class                                                                                                                                                                                                                    |
| ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Wadah isi           | `mx-auto w-full max-w-[1200px] px-4 sm:px-6`                                                                                                                                                                             |
| Bilah atas          | `topbar bg-primary text-on-primary`, isinya `flex min-h-16 items-center gap-3 lg:min-h-14`                                                                                                                               |
| Grid daftar ebook   | `grid grid-cols-1 gap-3 py-4 sm:grid-cols-2 lg:grid-cols-[repeat(auto-fill,minmax(320px,1fr))]`                                                                                                                          |
| Item daftar muncul  | `animate-enter` pada tiap kartu, ditambah `style="animation-delay: {{ min($loop->index, 5) * 30 }}ms"`                                                                                                                   |
| Tombol ikon         | `inline-grid size-10 place-items-center rounded-full transition duration-100 ease-standard hover:bg-primary-tint active:scale-95 motion-reduce:active:scale-100`; di bilah merah ganti hover menjadi `hover:bg-white/15` |
| Tautan              | `text-primary underline decoration-transparent underline-offset-4 transition-colors duration-100 hover:decoration-current`                                                                                               |
| Kotak error/offline | `flex animate-enter items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error`                                                                                                                                   |
| Snackbar            | `animate-enter rounded-full bg-ink px-6 py-3 text-on-ink`                                                                                                                                                                |
| Empty state         | `flex flex-col items-center gap-4 rounded-lg bg-primary-tint p-6 lg:flex-row`                                                                                                                                            |
| Login/Register      | `grid min-h-dvh lg:grid-cols-2`; panel merah `bg-primary px-4 py-3 text-on-primary lg:flex lg:flex-col lg:justify-center lg:p-8`; panel form `mx-auto w-full max-w-sm p-4 lg:self-center`                                |
| Trek progres upload | `h-2 overflow-hidden rounded-full bg-outline`                                                                                                                                                                            |
| Isi progres upload  | `h-full origin-left bg-primary transition-transform duration-300 ease-standard` dengan `style="transform: scaleX({{ $nilai }})"`                                                                                         |

### 10.5 JavaScript kecil (`resources/js/app.js`)

Sorotan "ebook baru" butuh satu langkah: atribut `data-new` dilepas sesaat setelah halaman tampil agar warna latar memudar ke `surface`.

```js
document.querySelectorAll("[data-new]").forEach((el) => {
    el.style.transitionDuration = "300ms"; // dur-slow, sesuai tabel 6.4
    requestAnimationFrame(() =>
        requestAnimationFrame(() => el.removeAttribute("data-new")),
    );
});
```

Bila JavaScript mati, kartu tetap berfungsi dan hanya tanpa sorotan.

### 10.6 Catatan Web

- **Hover otomatis aman di layar sentuh.** Pada Tailwind v4 varian `hover:` hanya berlaku pada perangkat yang benar-benar punya hover, sehingga aturan 6.5 butir 4 terpenuhi tanpa menulis `@media (hover: hover)` sendiri. Pada v3, aktifkan `future: { hoverOnlyWhenSupported: true }` di `tailwind.config.js`.
- **Gradien tidak bisa dihapus dari Tailwind.** Class `bg-gradient-*` tetap ada, jadi aturan "tanpa gradien" dijaga lewat daftar periksa (bagian 14), bukan oleh alat.
- **Form ubah status** tetap berupa form `PATCH` biasa (C10). Transisi warna chip terlihat bila elemen diperbarui di tempat; pada muat ulang penuh, animasi item daftar yang berlaku.
- **Tombol `secondary`** memakai hover isi hitam. Bila terasa terlalu berat di layar yang ramai, ubah menjadi `hover:bg-primary-tint hover:border-primary` di satu tempat (komponen tombol).

---

## 11. Implementasi Android (Kotlin, XML + ViewBinding)

Tema gelap dihapus, jadi hanya ada satu set resource (tanpa `values-night`).

### 11.1 Warna

`res/values/colors.xml`

```xml
<resources>
    <!-- Palet dasar -->
    <color name="white">#FFFFFF</color>
    <color name="white_soft">#F6F3F3</color>
    <color name="white_line">#E4DCDC</color>
    <color name="red_50">#FDECEC</color>
    <color name="red_600">#C62828</color>
    <color name="red_700">#A91E1E</color>
    <color name="red_800">#8E1818</color>
    <color name="black">#141010</color>
    <color name="black_soft">#2E2626</color>
    <color name="gray_600">#5A5050</color>
    <color name="gray_400">#8A8080</color>

    <!-- Token semantik -->
    <color name="ds_background">@color/white</color>
    <color name="ds_surface">@color/white_soft</color>
    <color name="ds_outline">@color/white_line</color>
    <color name="ds_outline_strong">@color/gray_400</color>
    <color name="ds_primary">@color/red_600</color>
    <color name="ds_primary_pressed">@color/red_800</color>
    <color name="ds_on_primary">@color/white</color>
    <color name="ds_primary_tint">@color/red_50</color>
    <color name="ds_ink">@color/black</color>
    <color name="ds_on_ink">@color/white</color>
    <color name="ds_text">@color/black</color>
    <color name="ds_text_muted">@color/gray_600</color>
    <color name="ds_error">@color/red_800</color>

    <!-- Ripple: merah 12% (respons sentuh, pengganti hover) -->
    <color name="ds_ripple">#1FC62828</color>
</resources>
```

### 11.2 Dimensi

`res/values/dimens.xml`

```xml
<resources>
    <dimen name="radius_sm">8dp</dimen>
    <dimen name="radius_md">16dp</dimen>
    <dimen name="radius_lg">24dp</dimen>

    <dimen name="space_1">4dp</dimen>
    <dimen name="space_2">8dp</dimen>
    <dimen name="space_3">12dp</dimen>
    <dimen name="space_4">16dp</dimen>
    <dimen name="space_5">24dp</dimen>
    <dimen name="space_6">32dp</dimen>
    <dimen name="space_7">48dp</dimen>

    <dimen name="touch_min">48dp</dimen>
    <dimen name="bar_height">64dp</dimen>
</resources>
```

### 11.3 Tema, bentuk, dan gaya komponen

Memakai Material Components sebagai dasar lalu diseragamkan menjadi flat. `res/values/themes.xml`

```xml
<resources>
    <style name="Theme.Ebook" parent="Theme.Material3.Light.NoActionBar">
        <item name="android:colorBackground">@color/ds_background</item>
        <item name="colorSurface">@color/ds_surface</item>
        <item name="colorOnSurface">@color/ds_text</item>
        <item name="colorOnSurfaceVariant">@color/ds_text_muted</item>
        <item name="colorPrimary">@color/ds_primary</item>
        <item name="colorOnPrimary">@color/ds_on_primary</item>
        <item name="colorPrimaryContainer">@color/ds_primary_tint</item>
        <item name="colorOnPrimaryContainer">@color/ds_primary</item>
        <item name="colorSecondaryContainer">@color/ds_ink</item>
        <item name="colorOnSecondaryContainer">@color/ds_on_ink</item>
        <item name="colorOutline">@color/ds_outline_strong</item>
        <item name="colorOutlineVariant">@color/ds_outline</item>
        <item name="colorError">@color/ds_error</item>
        <item name="colorControlHighlight">@color/ds_ripple</item>

        <!-- Status bar menyatu dengan bilah atas merah; ikon putih -->
        <item name="android:statusBarColor">@color/ds_primary</item>
        <item name="android:navigationBarColor">@color/ds_background</item>
        <item name="android:windowLightStatusBar">false</item>
        <item name="android:windowLightNavigationBar">true</item>

        <item name="shapeAppearanceSmallComponent">@style/Shape.Ebook.Sm</item>
        <item name="shapeAppearanceMediumComponent">@style/Shape.Ebook.Md</item>
        <item name="shapeAppearanceLargeComponent">@style/Shape.Ebook.Lg</item>

        <item name="materialButtonStyle">@style/Widget.Ebook.Button</item>
        <item name="materialCardViewStyle">@style/Widget.Ebook.Card</item>
    </style>

    <!-- Bentuk -->
    <style name="Shape.Ebook.Sm" parent="">
        <item name="cornerFamily">rounded</item>
        <item name="cornerSize">@dimen/radius_sm</item>
    </style>
    <style name="Shape.Ebook.Md" parent="">
        <item name="cornerFamily">rounded</item>
        <item name="cornerSize">@dimen/radius_md</item>
    </style>
    <style name="Shape.Ebook.Lg" parent="">
        <item name="cornerFamily">rounded</item>
        <item name="cornerSize">@dimen/radius_lg</item>
    </style>
    <style name="Shape.Ebook.Full" parent="">
        <item name="cornerFamily">rounded</item>
        <item name="cornerSize">50%</item>
    </style>

    <!-- Tombol utama: pil, merah, tanpa elevasi -->
    <style name="Widget.Ebook.Button" parent="Widget.Material3.Button">
        <item name="shapeAppearance">@style/Shape.Ebook.Full</item>
        <item name="android:minHeight">@dimen/touch_min</item>
        <item name="android:insetTop">0dp</item>
        <item name="android:insetBottom">0dp</item>
        <item name="android:paddingStart">@dimen/space_5</item>
        <item name="android:paddingEnd">@dimen/space_5</item>
        <item name="android:textAllCaps">false</item>
        <item name="android:stateListAnimator">@null</item>
        <item name="elevation">0dp</item>
    </style>

    <!-- Tombol secondary: garis abu, teks hitam -->
    <style name="Widget.Ebook.Button.Secondary" parent="Widget.Material3.Button.OutlinedButton">
        <item name="shapeAppearance">@style/Shape.Ebook.Full</item>
        <item name="android:minHeight">@dimen/touch_min</item>
        <item name="android:textColor">@color/ds_text</item>
        <item name="strokeColor">@color/ds_outline_strong</item>
        <item name="android:textAllCaps">false</item>
    </style>

    <!-- Tombol text: teks merah -->
    <style name="Widget.Ebook.Button.Text" parent="Widget.Material3.Button.TextButton">
        <item name="shapeAppearance">@style/Shape.Ebook.Full</item>
        <item name="android:textColor">@color/ds_primary</item>
        <item name="android:textAllCaps">false</item>
    </style>

    <!-- Kartu: tanpa bayangan, garis tepi 1dp -->
    <style name="Widget.Ebook.Card" parent="Widget.Material3.CardView.Outlined">
        <item name="shapeAppearance">@style/Shape.Ebook.Lg</item>
        <item name="cardBackgroundColor">@color/ds_surface</item>
        <item name="strokeColor">@color/ds_outline</item>
        <item name="strokeWidth">1dp</item>
        <item name="cardElevation">0dp</item>
    </style>
</resources>
```

- Bilah atas memakai `MaterialToolbar` dengan `android:background="@color/ds_primary"` dan tinggi `@dimen/bar_height`.
- Input teks memakai `TextInputLayout` bergaya `OutlinedBox` dengan `boxCornerRadius*` = `radius_md` dan `boxStrokeColor` = `ds_outline_strong` (berubah ke `ds_primary` saat fokus, ke `ds_error` saat salah).
- Chip "Sudah Dibaca" memakai isi `ds_ink` dan teks `ds_on_ink`; "Belum Dibaca" memakai isi transparan dan garis `ds_outline_strong`.

### 11.4 Gerak

`res/interpolator/ease_standard.xml`

```xml
<pathInterpolator xmlns:android="http://schemas.android.com/apk/res/android"
    android:controlX1="0.4" android:controlY1="0"
    android:controlX2="0.2" android:controlY2="1" />
```

`res/interpolator/ease_out.xml` memakai `0, 0, 0.2, 1`, dan `res/interpolator/ease_in.xml` memakai `0.4, 0, 1, 1`.

`ui/Motion.kt`

```kotlin
object Motion {
    const val FAST = 100L
    const val BASE = 200L
    const val SLOW = 300L
    const val PAGE = 400L
}

/** Item muncul: fade + naik 8dp. Panggil dengan index untuk jeda antar item. */
fun View.enter(index: Int = 0) {
    val rise = 8 * resources.displayMetrics.density
    alpha = 0f
    translationY = rise
    animate()
        .alpha(1f)
        .translationY(0f)
        .setStartDelay(minOf(index, 5) * 30L)
        .setDuration(Motion.SLOW)
        .setInterpolator(AnimationUtils.loadInterpolator(context, R.interpolator.ease_out))
        .start()
}

/** Respons tekan: skala 0,97 lalu kembali. */
@SuppressLint("ClickableViewAccessibility")
fun View.pressFeedback(scale: Float = 0.97f) {
    setOnTouchListener { v, e ->
        when (e.actionMasked) {
            MotionEvent.ACTION_DOWN -> v.animate().scaleX(scale).scaleY(scale)
                .setDuration(Motion.FAST).start()
            MotionEvent.ACTION_UP, MotionEvent.ACTION_CANCEL -> v.animate().scaleX(1f).scaleY(1f)
                .setDuration(Motion.FAST).start()
        }
        false // jangan menelan event agar klik tetap berjalan
    }
}

/** Respons gagal: getar ±4dp tiga kali. Dipanggil bersamaan dengan pesan error. */
fun View.shake() {
    val d = 4 * resources.displayMetrics.density
    ObjectAnimator.ofFloat(this, View.TRANSLATION_X, 0f, -d, d, -d, d, 0f).apply {
        duration = Motion.SLOW
        interpolator = AnimationUtils.loadInterpolator(context, R.interpolator.ease_standard)
    }.start()
}

/** Respons berhasil: sorot item baru dengan tint merah lalu pudar ke warna kartu. */
fun MaterialCardView.highlightNew() {
    val from = ContextCompat.getColor(context, R.color.ds_primary_tint)
    val to = ContextCompat.getColor(context, R.color.ds_surface)
    ValueAnimator.ofArgb(from, to).apply {
        duration = Motion.SLOW
        addUpdateListener { setCardBackgroundColor(it.animatedValue as Int) }
    }.start()
}
```

Catatan Android:

- Android tidak punya hover; pesan "ini bisa disentuh" disampaikan oleh ripple merah `ds_ripple` dan `pressFeedback`.
- `ViewPropertyAnimator` dan `ObjectAnimator` otomatis mengikuti skala animasi sistem; bila user mematikan animasi, gerak ikut hilang.
- `enter()` hanya dipanggil pada **pemuatan pertama** daftar, bukan setiap kali `RecyclerView` menggulir ulang, agar tidak berkedip. Gunakan flag di adapter.
- Perpindahan antar-Activity memakai fade 200 ms (`overrideActivityTransition`), bukan geser penuh.

---

## 12. Pemetaan Layar ke Komponen

Dipakai saat membuat wireframe (C10, C13, C14, C16) dan layar sebenarnya.

| Layar         | Komponen                                                                                                | Aksi utama |
| ------------- | ------------------------------------------------------------------------------------------------------- | ---------- |
| Login         | Panel merah (desktop) / pita merah (mobile), judul, 2 input, tombol `primary`, tautan ke Register       | Masuk      |
| Register      | Sama dengan Login, 4 input, tautan ke Login                                                             | Daftar     |
| Daftar Ebook  | Bilah atas merah (nama/judul, ringkasan, tombol Tambah PDF, logout), grid kartu ebook                   | Tambah PDF |
| Upload        | Bilah atas, form, bar progres, kotak aturan `primary-tint`, pesan error                                 | Upload     |
| Empty State   | Bilah atas, kotak `primary-tint` berisi ikon, satu kalimat, tombol                                      | Tambah PDF |
| Offline/Error | Kotak `primary-tint` dengan pesan `error` + tombol `secondary` "Coba lagi"                              | -          |
| Buka PDF      | Indikator memuat selama mengunduh, lalu diserahkan ke penampil eksternal (tidak ada layar baca sendiri) | -          |

---

## 13. Yang Sengaja Tidak Dilakukan

Agar desain tetap sederhana dan sesuai keputusan awal:

- Bayangan, gradien, glassmorphism, tekstur.
- Ilustrasi atau gambar dekoratif.
- Font kustom dan pustaka ikon eksternal.
- Tema gelap dan tombol ganti tema.
- Animasi berulang yang menarik perhatian (kecuali placeholder memuat).
- Warna di luar putih, merah, dan hitam (termasuk warna peringatan, info, atau sukses hijau).
- Kolom tunggal sempit di tengah pada desktop.

Bila nanti ada kebutuhan baru, tambahkan di dokumen ini dulu, lalu di kode.

---

## 14. Daftar Periksa Sebelum Layar Dianggap Selesai

- [ ] Hanya memakai token, tidak ada hex atau angka radius langsung.
- [ ] Tidak ada bayangan atau gradien.
- [ ] Semua sudut membulat sesuai skala radius.
- [ ] Komposisi layar kira-kira 60% putih, 30% merah, 10% hitam; hanya satu blok merah besar.
- [ ] Maksimal satu aksi utama (`primary` atau `inverse`).
- [ ] Teks memenuhi kontras (tabel 3.5); tidak ada teks merah di atas hitam.
- [ ] Error memakai warna `error`, ikon, dan kotak tint, bukan hanya merah.
- [ ] Status tidak disampaikan hanya lewat warna.
- [ ] Setiap elemen interaktif punya respons: hover (Web), fokus, dan tekan.
- [ ] Hover hanya berlaku pada perangkat yang punya hover (otomatis pada Tailwind v4).
- [ ] Web hanya memakai class token (tidak ada warna atau ukuran arbitrary seperti `bg-[#...]`).
- [ ] Gerak memakai token durasi dan easing, tidak lebih dari 400 ms, hanya `transform`, `opacity`, dan warna.
- [ ] Layar tetap nyaman dipakai dengan "kurangi gerakan" aktif.
- [ ] Desktop memakai grid dan jarak desktop (tabel 5.2), bukan tata letak mobile yang diperlebar.
- [ ] Layar Android dan Web untuk fungsi yang sama tampak setara.

---

## 15. Catatan Perubahan

### Dari versi 1.0 ke 1.1

| Bagian | Perubahan                                                                                                                                                  |
| ------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 2, 3   | Palet hijau diganti aturan 60-30-10 putih, merah, hitam. Tema gelap dihapus. Merah dipakai sebagai warna merek, sehingga error dibedakan lewat aturan 3.4. |
| 6      | Gerak diperluas menjadi respons ke pengguna: hover (Web), fokus, tekan, sukses, gagal. Ditambah getar error dan sorotan item baru.                         |
| 5.2, 8 | Ditambah ukuran dan jarak khusus desktop (lebih rapat), tiga titik lebar, dan grid. Aturan "tanpa multi-kolom di Web" dicabut.                             |
| 7      | Varian tombol `tonal` diganti `secondary` dan ditambah `inverse` untuk bilah merah. Bilah atas, chip "Sudah Dibaca", dan Empty State disesuaikan.          |
| 10, 11 | Token CSS dan resource Android ditulis ulang. `values-night` dan `bools.xml` dihapus.                                                                      |

### Dari versi 1.1 ke 1.2

| Bagian     | Perubahan                                                                                                                                                                                                              |
| ---------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Header, 10 | Implementasi Web berpindah dari CSS biasa ke **Tailwind CSS lewat Vite dan Node.js**. Token di `@theme`, komponen berupa Blade component. Menggantikan butir C01 dokumen 05 yang melarang Tailwind, Vite, dan Node.js. |
| 6.5, 14    | Aturan hover disesuaikan: pada Tailwind v4 pembungkus `@media (hover: hover)` sudah otomatis. Ditambah butir daftar periksa untuk class arbitrary.                                                                     |
| 1-9, 11-13 | Tidak berubah. Android tetap memakai XML dan ViewBinding.                                                                                                                                                              |
