# Design System

## Ebook Storage & To-Do List

| Item     | Detail                                                                 |
| -------- | ---------------------------------------------------------------------- |
| Versi    | 1.0                                                                    |
| Tanggal  | 18 Oktober 2026                                                        |
| Platform | Android (Kotlin, XML + ViewBinding) dan Web (Laravel Blade, CSS biasa) |
| Acuan    | 00-Project-Overview, 05-Backlog-dan-Jadwal-Chunking                    |

---

## 1. Tujuan

Dokumen ini menetapkan satu bahasa visual yang dipakai bersama oleh Android dan Web, sehingga kedua platform terasa seperti satu produk. Dokumen ini hanya mengatur **tampilan dan gerak**. Scope fitur, prioritas, dan alur tetap mengikuti dokumen 00 sampai 05.

Dokumen ini sengaja ringkas: setiap aturan di sini harus bisa langsung dipakai saat membuat layar, bukan dekorasi.

---

## 2. Prinsip

Empat keputusan awal, dan turunannya:

| Keputusan          | Artinya dalam praktik                                                                                                                               |
| ------------------ | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Flat design**    | Tanpa bayangan, tanpa gradien, tanpa tekstur. Kedalaman dibuat dengan **lapisan warna** (tonal) dan garis tepi 1dp.                                 |
| **Rounded corner** | Semua elemen bersudut membulat dari satu skala radius (bagian 5). Tidak ada sudut tajam. Tombol dan chip berbentuk pil, sesuai bentuk pada palet.   |
| **Skema warna**    | Enam warna hijau dari palet terlampir. Hanya dua tambahan: satu warna error per tema dan satu tint terang (bagian 3).                               |
| **Animasi smooth** | Gerak pendek, lembut, dan punya tujuan: memberi umpan balik atau menjelaskan perubahan. Tidak ada animasi yang hanya hiasan (bagian 6).             |

Satu prinsip tambahan: **satu aksen**. Warna utama dipakai hemat agar aksi terpenting di layar langsung terlihat.

---

## 3. Warna

### 3.1 Palet dasar

| Nama         | Hex       | Keterangan                          |
| ------------ | --------- | ----------------------------------- |
| `forest-950` | `#051F20` | Paling gelap                        |
| `forest-900` | `#0B2B26` |                                     |
| `forest-800` | `#163832` |                                     |
| `forest-700` | `#235347` |                                     |
| `sage-400`   | `#8EB69B` |                                     |
| `mint-100`   | `#DAF1DE` | Paling terang                       |
| `mint-50`    | `#EEF9F0` | **Tambahan.** Tint untuk kartu terang |
| `error-light`| `#9E2B25` | **Tambahan.** Error di tema terang  |
| `error-dark` | `#F0A39A` | **Tambahan.** Error di tema gelap   |

Palet tidak memiliki warna error, sedangkan error perlu dibedakan dari konten biasa. Karena itu hanya warna error yang ditambahkan, dan keduanya sudah diuji kontrasnya. Status sukses tidak diberi warna sendiri; sukses memakai warna utama.

### 3.2 Token semantik

Kode tidak boleh memakai hex langsung. Pakai token di bawah.

| Token              | Tema terang   | Tema gelap    | Dipakai untuk                                  |
| ------------------ | ------------- | ------------- | ---------------------------------------------- |
| `background`       | `mint-100`    | `forest-950`  | Latar halaman                                  |
| `surface`          | `mint-50`     | `forest-900`  | Kartu, input, dialog, bottom sheet             |
| `outline`          | `sage-400`    | `forest-800`  | Garis tepi dekoratif (kartu, pemisah)          |
| `outline-strong`   | `forest-700`  | `sage-400`    | Garis tepi kontrol (input, tombol outline)     |
| `primary`          | `forest-700`  | `sage-400`    | Tombol utama, tautan, fokus                    |
| `on-primary`       | `mint-50`     | `forest-950`  | Teks/ikon di atas `primary`                    |
| `container`        | `sage-400`    | `forest-700`  | Chip "Sudah Dibaca", area sorotan              |
| `on-container`     | `forest-950`  | `mint-100`    | Teks/ikon di atas `container`                  |
| `text`             | `forest-950`  | `mint-100`    | Teks utama                                     |
| `text-muted`       | `forest-700`  | `sage-400`    | Teks sekunder (tanggal, keterangan)            |
| `error`            | `error-light` | `error-dark`  | Pesan dan batas input yang salah               |

### 3.3 Lapisan (pengganti bayangan)

```text
TEMA GELAP                         TEMA TERANG

background  #051F20  (paling dalam)   background  #DAF1DE
   └─ surface  #0B2B26 + garis #163832     └─ surface  #EEF9F0 + garis #8EB69B
        └─ container  #235347                   └─ container  #8EB69B
```

Elemen yang lebih penting atau lebih "di depan" memakai lapisan yang lebih terang (gelap) atau lebih pekat (terang), **bukan** bayangan.

### 3.4 Aturan kontras (sudah dihitung)

Standar minimal: teks biasa 4,5:1, komponen UI 3:1.

| Kombinasi                                | Rasio  | Boleh untuk                      |
| ---------------------------------------- | ------ | -------------------------------- |
| `forest-950` di atas `mint-100`          | 14,4:1 | Teks utama terang                |
| `forest-700` di atas `mint-100`          | 7,3:1  | Teks sekunder & tombol terang    |
| `mint-50` di atas `forest-700`           | 8,1:1  | Teks di tombol utama terang      |
| `mint-100` di atas `forest-950`          | 14,4:1 | Teks utama gelap                 |
| `sage-400` di atas `forest-950`          | 7,6:1  | Teks sekunder & tombol gelap     |
| `sage-400` di atas `forest-900`          | 6,7:1  | Teks sekunder di kartu gelap     |
| `forest-950` di atas `sage-400`          | 7,6:1  | Chip "Sudah Dibaca" terang       |
| `mint-100` di atas `forest-700`          | 7,3:1  | Chip "Sudah Dibaca" gelap        |
| `error-light` di atas `mint-100`         | 6,2:1  | Pesan error terang               |
| `error-dark` di atas `forest-900`        | 7,5:1  | Pesan error gelap                |

**Dilarang:**

- `sage-400` sebagai **teks atau garis kontrol** di atas `mint-100` atau `mint-50`. Rasionya hanya sekitar 1,9:1. Di tema terang, `sage-400` hanya untuk isian (`container`) dan garis dekoratif.
- Menyampaikan informasi hanya lewat warna. Status baca selalu disertai ikon dan teks (bagian 7.4).

### 3.5 Tema

Mengikuti pengaturan sistem (terang/gelap). Tidak ada tombol ganti tema di MVP.

---

## 4. Tipografi

Memakai **font sistem** (Roboto di Android, `system-ui` di Web). Tidak ada file font yang perlu diunduh, sehingga tidak menambah dependensi.

| Gaya        | Ukuran / tinggi baris | Bobot | Dipakai untuk                  |
| ----------- | --------------------- | ----- | ------------------------------ |
| `title-lg`  | 24 / 32               | 600   | Judul layar                    |
| `title-md`  | 18 / 24               | 600   | Judul ebook di daftar, dialog  |
| `body`      | 16 / 24               | 400   | Teks isi, isian form           |
| `body-sm`   | 14 / 20               | 400   | Tanggal, keterangan            |
| `label`     | 14 / 20               | 500   | Tombol, chip, label input      |
| `caption`   | 12 / 16               | 400   | Pesan bantu di bawah input     |

- Android memakai `sp`, Web memakai `rem` (1rem = 16px).
- Teks tidak boleh lebih kecil dari 12.
- Judul ebook dipotong dua baris dengan elipsis; jangan dipotong tengah kata tanpa elipsis.

---

## 5. Bentuk, Jarak, dan Kedalaman

### 5.1 Radius

| Token         | Nilai | Dipakai untuk                                         |
| ------------- | ----- | ----------------------------------------------------- |
| `radius-sm`   | 8     | Checkbox, elemen kecil                                |
| `radius-md`   | 16    | Input, snackbar                                       |
| `radius-lg`   | 24    | Kartu, dialog, bottom sheet                           |
| `radius-full` | pil   | Tombol, chip, indikator progres, tombol aksi tambah   |

Elemen di dalam wadah membulat memakai radius yang **lebih kecil** dari wadahnya, agar lekukannya sejajar.

### 5.2 Jarak (kelipatan 4)

`4 · 8 · 12 · 16 · 24 · 32 · 48`

- Padding layar: 16 (Android), 16 sampai 24 (Web).
- Padding dalam kartu: 16.
- Jarak antar kartu: 12.
- Area sentuh minimum 48 × 48 dp (Android) dan 44 × 44 px (Web).

### 5.3 Kedalaman

- Elevasi dan `box-shadow` **tidak dipakai**.
- Pemisah antar-area memakai `outline` 1dp, atau perbedaan lapisan.
- Keadaan tekan/hover diwujudkan lewat perubahan warna dan skala kecil, bukan bayangan.

---

## 6. Gerak (Animasi Smooth)

### 6.1 Durasi

| Token       | ms  | Dipakai untuk                                              |
| ----------- | --- | ---------------------------------------------------------- |
| `dur-fast`  | 100 | Umpan balik tekan, ripple                                  |
| `dur-base`  | 200 | Perubahan status, fokus input, chip, fade kecil            |
| `dur-slow`  | 300 | Elemen daftar muncul, snackbar, progres                    |
| `dur-page`  | 400 | Dialog, bottom sheet, perpindahan layar                    |

Tidak ada animasi lebih dari 400 ms.

### 6.2 Easing

| Token           | Kurva                          | Dipakai untuk                         |
| --------------- | ------------------------------ | ------------------------------------- |
| `ease-standard` | `cubic-bezier(0.4, 0, 0.2, 1)` | Perubahan di tempat (warna, ukuran)   |
| `ease-out`      | `cubic-bezier(0, 0, 0.2, 1)`   | Elemen **masuk**                      |
| `ease-in`       | `cubic-bezier(0.4, 0, 1, 1)`   | Elemen **keluar**                     |

Hindari gerak linear dan efek memantul (bounce). Keluar sedikit lebih cepat dari masuk.

### 6.3 Pola gerak

| Situasi                     | Gerak                                                                        | Durasi / easing           |
| --------------------------- | ---------------------------------------------------------------------------- | ------------------------- |
| Tombol/kartu ditekan        | Skala ke 0,97, kembali saat dilepas                                          | `fast`, `standard`        |
| Item daftar muncul          | Fade + naik 8 dp. Jeda antar item 30 ms, maksimal 6 item pertama            | `slow`, `out`             |
| Status baca berubah         | Warna chip bertransisi, ikon centang muncul dengan fade + skala 0,8 ke 1     | `base`, `standard`        |
| Input fokus                 | Warna garis tepi bertransisi (tebal garis tetap, agar tata letak tidak bergeser) | `base`, `standard`    |
| Pindah layar                | Fade + geser 16 dp pendek, bukan geser penuh                                 | `page`, `out` / `in`      |
| Dialog, bottom sheet        | Fade + naik 16 dp; scrim memudar                                             | `page`, `out`             |
| Snackbar / pesan error      | Fade + naik 8 dp                                                             | `slow`, `out`             |
| Progres upload              | Lebar bar bertransisi mulus mengikuti nilai terbaru                          | `slow`, `standard`        |
| Memuat (loading)            | Placeholder berdenyut opasitas 0,5 ke 1, siklus 1,2 detik                    | `ease-standard`           |

### 6.4 Aturan gerak

1. **Hanya animasikan `transform`, `opacity`, dan warna.** Jangan animasikan `width`, `height`, `margin`, atau `top` pada Web (memicu kerja tata letak dan terasa patah). Pengecualian: lebar bar progres, yang memakai `transform: scaleX` pada Web.
2. Satu gerakan utama per layar pada satu waktu.
3. Gerak tidak boleh menghalangi aksi: user dapat menekan tombol sebelum animasi masuk selesai.
4. Hormati preferensi sistem **kurangi gerakan**: durasi mendekati 0, gerak spasial dihapus, hanya fade singkat yang tersisa. Web memakai `prefers-reduced-motion`; Android otomatis mengikuti pengaturan skala animasi sistem.

---

## 7. Komponen

Komponen sengaja dibatasi pada yang dibutuhkan wireframe di dokumen 05 (Login, Register, Daftar Ebook, Upload, Empty State).

### 7.1 Tombol

| Varian    | Tampilan                                                   | Dipakai untuk                          |
| --------- | ---------------------------------------------------------- | -------------------------------------- |
| `primary` | Isi `primary`, teks `on-primary`                           | Satu aksi utama per layar (Masuk, Upload) |
| `tonal`   | Isi `container`, teks `on-container`                       | Aksi sekunder                          |
| `text`    | Tanpa isi, teks `primary`                                  | Aksi tersier (Batal, Daftar akun)      |

- Tinggi 48 dp, bentuk pil, padding horizontal 24.
- Maksimal **satu** tombol `primary` per layar.
- Disabled: opasitas 0,38, tidak bisa ditekan. Saat proses berjalan (misalnya upload), tombol diganti indikator progres dan tidak bisa ditekan dua kali (sejalan dengan C17).

### 7.2 Input teks

- Tinggi 56, `radius-md`, isi `surface`, garis 1dp `outline-strong`.
- Fokus: garis menjadi `primary` (tebal tetap 1dp, ditambah cincin fokus 2dp pada Web).
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
   radius-lg · surface · outline 1dp · padding 16
```

- Seluruh kartu dapat ditekan untuk membuka PDF (UC-08).
- Chip status ditekan untuk mengubah status baca (UC-09 dan UC-10). Area sentuh chip tetap 48 dp.

### 7.4 Chip status baca

| Status         | Tampilan                                                        |
| -------------- | --------------------------------------------------------------- |
| Belum Dibaca   | Isi transparan, garis `outline-strong`, ikon lingkaran kosong, teks `text` |
| Sudah Dibaca   | Isi `container`, ikon centang, teks `on-container`              |

Status dibedakan oleh **isi, ikon, dan teks sekaligus**, tidak hanya warna.

### 7.5 Tombol aksi tambah ebook

- Pil dengan ikon plus dan teks "Tambah PDF", isi `primary`.
- Android: mengambang di kanan bawah. Web: di pojok kanan atas daftar atau dalam header.
- Ini adalah tombol `primary` satu-satunya di layar daftar.

### 7.6 Bilah atas

- Tinggi 64, latar `background` (menyatu dengan halaman), tanpa garis bawah dan tanpa bayangan.
- Judul layar `title-lg`. Aksi (logout) berupa ikon 48 dp.
- Informasi ringkasan, misalnya "5 dari 12 sudah dibaca" (dari `meta.read_count`), memakai `body-sm` di bawah judul.

### 7.7 Empty state

- Ikon besar 64 dp berwarna `text-muted`, satu kalimat, satu tombol `primary` "Tambah PDF".
- Terletak di tengah vertikal. Tanpa ilustrasi.

### 7.8 Umpan balik

| Jenis             | Komponen                                                                              |
| ----------------- | ------------------------------------------------------------------------------------- |
| Sukses singkat    | Snackbar pil, `container`, hilang otomatis 4 detik                                    |
| Error yang bisa dicoba lagi (offline, gagal upload) | Pesan di layar dengan tombol `tonal` "Coba lagi" |
| Error form        | Pesan di bawah input yang bermasalah                                                  |
| Konfirmasi        | Dialog `radius-lg`, dua tombol: `text` (batal) dan `primary` (lanjut)                 |
| Memuat daftar     | Tiga kartu placeholder berdenyut                                                      |
| Upload            | Bar progres pil tipis di atas kartu/dialog, tinggi 8dp, trek `outline`, isi `primary` |

Pesan memakai bahasa Indonesia yang jelas dan menyebut langkah berikutnya, misalnya "Tidak ada koneksi. Coba lagi saat online." (sesuai dokumen 01 UC-06 dan UC-07).

### 7.9 Ikon

- Satu set: **Material Symbols Rounded**, gaya outline, bobot 400, ukuran 24 (64 untuk empty state).
- Android memakai vector drawable. Web memakai SVG inline, tanpa pustaka ikon eksternal.
- Warna ikon mengikuti warna teks di sampingnya.

---

## 8. Tata Letak

| Lebar          | Android / Web                                                                  |
| -------------- | ------------------------------------------------------------------------------ |
| < 600 dp / px  | Satu kolom penuh                                                               |
| ≥ 600 dp / px  | Konten dibatasi di tengah: daftar maks 720, form (login/register/upload) maks 400 |

- Web tidak memakai grid multi-kolom pada MVP; daftar tetap satu kolom agar sama dengan Android.
- Urutan layar sama di kedua platform: **Login → Daftar Ebook → (Upload / Buka PDF)**.

---

## 9. Aksesibilitas (ringkas)

- Kontras mengikuti tabel 3.4.
- Area sentuh sesuai bagian 5.2.
- Setiap ikon yang berdiri sendiri punya label (`contentDescription` di Android, `aria-label` di Web).
- Fokus keyboard terlihat jelas di Web: cincin 2px `primary` dengan jarak 2px (`:focus-visible`).
- Ukuran teks mengikuti pengaturan ukuran font sistem (sp di Android, rem di Web).
- Preferensi kurangi gerakan dihormati (bagian 6.4).

---

## 10. Implementasi Web (Laravel Blade, CSS biasa)

Cukup satu file, misalnya `public/css/design-system.css`, dimuat oleh layout Blade (C01). Tidak membutuhkan Tailwind, Vite, atau Node.js.

```css
/* ===== Token ===== */
:root {
  color-scheme: light dark;

  /* Warna: tema terang */
  --background: #DAF1DE;
  --surface: #EEF9F0;
  --outline: #8EB69B;
  --outline-strong: #235347;
  --primary: #235347;
  --on-primary: #EEF9F0;
  --container: #8EB69B;
  --on-container: #051F20;
  --text: #051F20;
  --text-muted: #235347;
  --error: #9E2B25;

  /* Bentuk */
  --radius-sm: 8px;
  --radius-md: 16px;
  --radius-lg: 24px;
  --radius-full: 999px;

  /* Jarak */
  --space-1: 4px;  --space-2: 8px;  --space-3: 12px; --space-4: 16px;
  --space-5: 24px; --space-6: 32px; --space-7: 48px;

  /* Gerak */
  --dur-fast: 100ms;
  --dur-base: 200ms;
  --dur-slow: 300ms;
  --dur-page: 400ms;
  --ease-standard: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-out: cubic-bezier(0, 0, 0.2, 1);
  --ease-in: cubic-bezier(0.4, 0, 1, 1);

  /* Tipografi */
  --font: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
}

@media (prefers-color-scheme: dark) {
  :root {
    --background: #051F20;
    --surface: #0B2B26;
    --outline: #163832;
    --outline-strong: #8EB69B;
    --primary: #8EB69B;
    --on-primary: #051F20;
    --container: #235347;
    --on-container: #DAF1DE;
    --text: #DAF1DE;
    --text-muted: #8EB69B;
    --error: #F0A39A;
  }
}

@media (prefers-reduced-motion: reduce) {
  :root { --dur-fast: 1ms; --dur-base: 1ms; --dur-slow: 1ms; --dur-page: 1ms; }
  * { animation-duration: 1ms !important; }
}

/* ===== Dasar ===== */
body {
  margin: 0;
  background: var(--background);
  color: var(--text);
  font: 400 1rem/1.5 var(--font);
}

:focus-visible {
  outline: 2px solid var(--primary);
  outline-offset: 2px;
}

/* ===== Tombol ===== */
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: var(--space-2);
  min-height: 48px; padding: 0 var(--space-5);
  border: 0; border-radius: var(--radius-full);
  font: 500 0.875rem/1.25rem var(--font);
  cursor: pointer;
  transition: transform var(--dur-fast) var(--ease-standard),
              background-color var(--dur-base) var(--ease-standard),
              opacity var(--dur-base) var(--ease-standard);
}
.btn:active { transform: scale(0.97); }
.btn:disabled { opacity: 0.38; cursor: not-allowed; transform: none; }
.btn--primary { background: var(--primary);   color: var(--on-primary); }
.btn--tonal   { background: var(--container); color: var(--on-container); }
.btn--text    { background: transparent;      color: var(--primary); }

/* ===== Input ===== */
.field { display: grid; gap: var(--space-1); }
.field label { font: 500 0.875rem/1.25rem var(--font); }
.field input {
  min-height: 56px; padding: 0 var(--space-4);
  background: var(--surface); color: var(--text);
  border: 1px solid var(--outline-strong); border-radius: var(--radius-md);
  font: inherit;
  transition: border-color var(--dur-base) var(--ease-standard);
}
.field input:focus { border-color: var(--primary); }
.field--error input { border-color: var(--error); }
.field__hint { font-size: 0.75rem; line-height: 1rem; color: var(--text-muted); }
.field--error .field__hint { color: var(--error); }

/* ===== Kartu ebook ===== */
.card {
  display: grid; gap: var(--space-2); padding: var(--space-4);
  background: var(--surface);
  border: 1px solid var(--outline); border-radius: var(--radius-lg);
  transition: transform var(--dur-fast) var(--ease-standard);
}
.card:active { transform: scale(0.98); }
.card__title { font: 600 1.125rem/1.5rem var(--font); }
.card__meta  { font-size: 0.875rem; line-height: 1.25rem; color: var(--text-muted); }

/* ===== Chip status baca ===== */
.chip {
  display: inline-flex; align-items: center; gap: var(--space-2);
  min-height: 48px; padding: 0 var(--space-4);
  background: transparent; color: var(--text);
  border: 1px solid var(--outline-strong); border-radius: var(--radius-full);
  font: 500 0.875rem/1.25rem var(--font);
  cursor: pointer;
  transition: background-color var(--dur-base) var(--ease-standard),
              border-color var(--dur-base) var(--ease-standard);
}
.chip[aria-pressed="true"] {
  background: var(--container); color: var(--on-container); border-color: transparent;
}

/* ===== Daftar muncul ===== */
@keyframes enter {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: none; }
}
.list > * { animation: enter var(--dur-slow) var(--ease-out) both; }
.list > :nth-child(2) { animation-delay: 30ms; }
.list > :nth-child(3) { animation-delay: 60ms; }
.list > :nth-child(4) { animation-delay: 90ms; }
.list > :nth-child(5) { animation-delay: 120ms; }
.list > :nth-child(n+6) { animation-delay: 150ms; }

/* ===== Progres upload ===== */
.progress { height: 8px; background: var(--outline); border-radius: var(--radius-full); overflow: hidden; }
.progress__bar {
  height: 100%; background: var(--primary); transform-origin: left;
  transform: scaleX(var(--value, 0));
  transition: transform var(--dur-slow) var(--ease-standard);
}
```

Catatan Web:

- Chip status memakai `aria-pressed="true"` untuk "Sudah Dibaca", dan ikon centang ditampilkan lewat SVG inline.
- Form ubah status tetap berupa form `PATCH` biasa (C10). Transisi warna chip terlihat bila halaman memperbarui elemen yang sama; jika halaman dimuat ulang penuh, animasi item daftar yang berlaku.

---

## 11. Implementasi Android (Kotlin, XML + ViewBinding)

### 11.1 Warna

`res/values/colors.xml`

```xml
<resources>
    <!-- Palet dasar -->
    <color name="forest_950">#051F20</color>
    <color name="forest_900">#0B2B26</color>
    <color name="forest_800">#163832</color>
    <color name="forest_700">#235347</color>
    <color name="sage_400">#8EB69B</color>
    <color name="mint_100">#DAF1DE</color>
    <color name="mint_50">#EEF9F0</color>
    <color name="error_light">#9E2B25</color>
    <color name="error_dark">#F0A39A</color>

    <!-- Token semantik: tema terang -->
    <color name="ds_background">@color/mint_100</color>
    <color name="ds_surface">@color/mint_50</color>
    <color name="ds_outline">@color/sage_400</color>
    <color name="ds_outline_strong">@color/forest_700</color>
    <color name="ds_primary">@color/forest_700</color>
    <color name="ds_on_primary">@color/mint_50</color>
    <color name="ds_container">@color/sage_400</color>
    <color name="ds_on_container">@color/forest_950</color>
    <color name="ds_text">@color/forest_950</color>
    <color name="ds_text_muted">@color/forest_700</color>
    <color name="ds_error">@color/error_light</color>
</resources>
```

`res/values-night/colors.xml` (hanya token semantik yang berubah)

```xml
<resources>
    <color name="ds_background">@color/forest_950</color>
    <color name="ds_surface">@color/forest_900</color>
    <color name="ds_outline">@color/forest_800</color>
    <color name="ds_outline_strong">@color/sage_400</color>
    <color name="ds_primary">@color/sage_400</color>
    <color name="ds_on_primary">@color/forest_950</color>
    <color name="ds_container">@color/forest_700</color>
    <color name="ds_on_container">@color/mint_100</color>
    <color name="ds_text">@color/mint_100</color>
    <color name="ds_text_muted">@color/sage_400</color>
    <color name="ds_error">@color/error_dark</color>
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
</resources>
```

### 11.3 Tema, bentuk, dan gaya komponen

Memakai Material Components sebagai dasar lalu diseragamkan menjadi flat. `res/values/themes.xml`

```xml
<resources>
    <style name="Theme.Ebook" parent="Theme.Material3.DayNight.NoActionBar">
        <item name="android:colorBackground">@color/ds_background</item>
        <item name="colorSurface">@color/ds_surface</item>
        <item name="colorOnSurface">@color/ds_text</item>
        <item name="colorOnSurfaceVariant">@color/ds_text_muted</item>
        <item name="colorPrimary">@color/ds_primary</item>
        <item name="colorOnPrimary">@color/ds_on_primary</item>
        <item name="colorSecondaryContainer">@color/ds_container</item>
        <item name="colorOnSecondaryContainer">@color/ds_on_container</item>
        <item name="colorOutline">@color/ds_outline_strong</item>
        <item name="colorOutlineVariant">@color/ds_outline</item>
        <item name="colorError">@color/ds_error</item>

        <item name="android:statusBarColor">@color/ds_background</item>
        <item name="android:navigationBarColor">@color/ds_background</item>
        <item name="android:windowLightStatusBar">@bool/light_system_bars</item>

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

    <!-- Tombol: pil, tanpa elevasi -->
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

`res/values/bools.xml` berisi `light_system_bars` = `true`, dan `res/values-night/bools.xml` berisi `false`, agar ikon status bar terbaca di kedua tema.

Input teks memakai `TextInputLayout` bergaya `OutlinedBox` dengan `boxCornerRadius*` = `radius_md` dan `boxStrokeColor` = `ds_outline_strong` (berubah ke `ds_primary` saat fokus).

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

/** Umpan balik tekan: skala 0,97 lalu kembali. */
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
```

Catatan Android:

- `ViewPropertyAnimator` otomatis mengikuti skala animasi sistem; bila user mematikan animasi, gerak ikut hilang.
- `enter()` hanya dipanggil pada **pemuatan pertama** daftar, bukan setiap kali `RecyclerView` menggulir ulang, agar tidak berkedip. Gunakan flag di adapter.
- Perpindahan antar-Activity memakai fade 200 ms (`overrideActivityTransition`), bukan geser penuh.

---

## 12. Pemetaan Layar ke Komponen

Dipakai saat membuat wireframe (C10, C13, C14, C16) dan layar sebenarnya.

| Layar         | Komponen                                                                                               | Tombol `primary` |
| ------------- | ------------------------------------------------------------------------------------------------------ | ---------------- |
| Login         | Judul, 2 input, tombol, tautan `text` ke Register                                                      | Masuk            |
| Register      | Judul, 4 input, tombol, tautan `text` ke Login                                                         | Daftar           |
| Daftar Ebook  | Bilah atas (judul + ringkasan + logout), daftar kartu ebook, tombol aksi tambah                        | Tambah PDF       |
| Upload        | Nama dan ukuran file terpilih, bar progres, tombol, pesan error                                        | Upload           |
| Empty State   | Ikon, satu kalimat, tombol                                                                             | Tambah PDF       |
| Offline/Error | Pesan di layar + tombol `tonal` "Coba lagi"                                                            | -                |
| Buka PDF      | Indikator memuat selama mengunduh, lalu diserahkan ke penampil eksternal (tidak ada layar baca sendiri) | -                |

---

## 13. Yang Sengaja Tidak Dilakukan

Agar desain tetap sederhana dan sesuai empat keputusan awal:

- Bayangan, gradien, glassmorphism, tekstur.
- Ilustrasi atau gambar dekoratif.
- Font kustom dan pustaka ikon eksternal.
- Tombol ganti tema manual.
- Animasi berulang yang menarik perhatian (kecuali placeholder memuat).
- Warna status tambahan (peringatan, info). Cukup `error` dan `primary`.
- Layout multi-kolom di Web.

Bila nanti ada kebutuhan baru (misalnya warna peringatan), tambahkan di dokumen ini dulu, lalu di kode.

---

## 14. Daftar Periksa Sebelum Layar Dianggap Selesai

- [ ] Hanya memakai token, tidak ada hex atau angka radius langsung.
- [ ] Tidak ada bayangan atau gradien.
- [ ] Semua sudut membulat sesuai skala radius.
- [ ] Maksimal satu tombol `primary`.
- [ ] Teks memenuhi kontras pada kedua tema (terang dan gelap).
- [ ] Status tidak disampaikan hanya lewat warna.
- [ ] Area sentuh minimal 48 dp / 44 px.
- [ ] Gerak memakai token durasi dan easing, tidak lebih dari 400 ms.
- [ ] Layar tetap nyaman dipakai dengan "kurangi gerakan" aktif.
- [ ] Layar Android dan Web untuk fungsi yang sama tampak setara.
