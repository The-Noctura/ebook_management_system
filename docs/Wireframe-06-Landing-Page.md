# Wireframe 6: Landing Page (revisi 1)

Dokumen ini berisi wireframe dan copywriting halaman **Landing Page** versi Web, alamat `/`. Halaman ini untuk pengunjung yang belum login dan belum tahu apa itu EMS (Ebook Management System). Pengguna yang sudah login tidak melihat halaman ini; `/` langsung mengarah ke `/ebooks`.

**Aturan acuan.** Tampilan mengikuti **06-Design-System versi 1.2**: flat tanpa bayangan dan gradien, sudut membulat, komposisi putih 60%, merah 30%, hitam 10%, serta maksimal satu blok merah pekat besar per layar. Halaman ini tidak ada di dokumen 02 (User Flow, yang memulai perjalanan dari Register atau Login), jadi penambahannya perlu dicatat di Log Perubahan Rancangan (dokumen 05 bagian 9).

---

## 1. Struktur halaman

Urutan dari atas ke bawah:

| #   | Bagian      | Latar                                 | Keterangan                                                                           |
| --- | ----------- | ------------------------------------- | ------------------------------------------------------------------------------------ |
| 1   | Navbar      | Melayang di atas hero                 | 3 kolom: logo, navlink, Masuk/Daftar                                                 |
| 2   | Hero        | `primary` (merah)                     | Satu-satunya blok merah pekat besar. Headline, sub-headline, CTA, ditumpuk di tengah |
| 3   | Tentang     | `background` (putih)                  | Apa itu EMS                                                                          |
| 4   | Keunggulan  | `surface` (putih lembut)              | 4 kartu                                                                              |
| 5   | Cara Pakai  | `background` (putih)                  | 3 langkah                                                                            |
| 6   | FAQ         | `surface`                             | 4 pertanyaan, tanpa JavaScript                                                       |
| 7   | CTA penutup | `background`                          | Kotak `primary-tint`, satu ajakan                                                    |
| 8   | Footer      | `surface` dengan garis atas `outline` | 3 kolom                                                                              |

Bagian 6 dan 7 adalah tambahan dari saya, karena pengunjung asing biasanya masih butuh jawaban cepat sebelum mendaftar.

---

## 2. Wireframe desktop (lebar 1024 ke atas)

Isi dibatasi wadah maksimal 1200, rata tengah.

### 2.1 Navbar dan Hero

```text
┌──────────────────────────────────────────────────────────────────────────┐
│██████████████████████████ HERO (bg primary) ████████████████████████████│
│██                                                                      ██│
│██  ╭────────────────────────────────────────────────────────────────╮  ██│
│██  │ EMS        Tentang   Keunggulan   Cara Pakai     Masuk [Daftar]│  ██│  navbar melayang
│██  ╰────────────────────────────────────────────────────────────────╯  ██│  pil putih, garis outline
│██                                                                      ██│
│██                                                                      ██│
│██              Semua PDF Anda, Rapi di Satu Tempat                     ██│  headline  (title-lg, besar)
│██                                                                      ██│
│██        Unggah ebook PDF, buka dari browser mana pun, dan             ██│  sub-headline (body)
│██        tandai mana yang sudah selesai Anda baca.                     ██│
│██                                                                      ██│
│██                    [ Buat Akun Sekarang ]                            ██│  CTA, varian inverse
│██                  Sudah punya akun? Masuk                             ██│  tautan teks putih
│██                                                                      ██│
│██                                                                      ██│
└──────────────────────────────────────────────────────────────────────────┘
```

Aturan navbar:

- **Tiga kolom** dengan grid `grid-cols-[1fr_auto_1fr]`: logo rata kiri, navlink di tengah, aksi rata kanan. Navlink jadi benar-benar di tengah.
- **Melayang**: pil putih (`radius-full`), garis 1px `outline`, jarak 16 dari tepi atas dan 16 dari kiri-kanan, `sticky` supaya tetap terlihat saat gulir. Tanpa bayangan (dokumen 06 bagian 5.3 melarang `box-shadow`). Garis tepi yang membuatnya terpisah dari latar.
- **Logo**: teks `EMS`, `title-md`, `font-semibold`, warna `primary`. Menaut ke `/`.
- **Navlink**: `Tentang`, `Keunggulan`, `Cara Pakai`, teks `text`, `label`. Hover: latar `primary-tint` dan teks `primary`. Menaut ke anchor `#tentang`, `#keunggulan`, `#cara-pakai`.
- **Aksi kanan**: `Masuk` varian `text`, `Daftar` varian `secondary`. Keduanya bukan aksi utama, karena aksi utama halaman ini adalah CTA di hero (aturan dokumen 06 bagian 7.1: satu aksi utama per layar).

### 2.2 Tentang

```text
┌──────────────────────────────────────────────────────────────────────────┐
│                                                                          │
│                      Apa Itu EMS?                       ← title-lg       │
│                                                                          │
│   ┌────────────────────────────────────┐  ┌──────────────────────────┐   │
│   │ EMS adalah tempat penyimpanan      │  │ (primary-tint, radius-lg)│   │
│   │ ebook pribadi berbasis web. ...    │  │   [ikon library]         │   │
│   │ (2 paragraf, body)                 │  │   Satu koleksi.          │   │
│   │                                    │  │   Satu akun.             │   │
│   └────────────────────────────────────┘  └──────────────────────────┘   │
└──────────────────────────────────────────────────────────────────────────┘
        kiri: teks, maks 576          kanan: kotak ikon, ±360
```

### 2.3 Keunggulan

```text
┌──────────────────────────────────────────────────────────────────────────┐
│                       Kenapa Memakai EMS?                                │
│                                                                          │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐     │
│  │ [ikon]       │ │ [ikon]       │ │ [ikon]       │ │ [ikon]       │     │
│  │ Judul        │ │ Judul        │ │ Judul        │ │ Judul        │     │
│  │ Satu-dua     │ │ Satu-dua     │ │ Satu-dua     │ │ Satu-dua     │     │
│  │ kalimat      │ │ kalimat      │ │ kalimat      │ │ kalimat      │     │
│  └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘     │
└──────────────────────────────────────────────────────────────────────────┘
   kartu: surface atau background, outline 1px, radius-lg, padding 12
```

Latar bagian ini `surface`, jadi kartunya berlatar `background` (putih) supaya tetap terpisah dari latar.

### 2.4 Cara Pakai

```text
┌──────────────────────────────────────────────────────────────────────────┐
│                          Cara Memakai EMS                                │
│                                                                          │
│     (1)                       (2)                       (3)              │
│  Buat akun               Unggah PDF               Baca dan tandai        │
│  Daftar dengan           Pilih file PDF           Buka PDF di browser,   │
│  nama, email,            dari perangkat           lalu tandai sudah      │
│  dan password.           Anda.                    atau belum dibaca.     │
│                                                                          │
│  ○────────────────────────○────────────────────────○     ← garis penghubung 1px outline
└──────────────────────────────────────────────────────────────────────────┘
```

Nomor langkah berupa lingkaran 40 dengan isi `primary` dan teks `on-primary`. Itu elemen kecil, jadi tidak melanggar aturan satu blok merah besar.

### 2.5 FAQ

```text
┌──────────────────────────────────────────────────────────────────────────┐
│                      Pertanyaan yang Sering Muncul                       │
│                                                                          │
│   ┌──────────────────────────────────────────────────────────────┐       │
│   │ Format file apa yang didukung?                            ＋ │       │
│   ├──────────────────────────────────────────────────────────────┤       │
│   │ Apakah file saya bisa dilihat pengguna lain?              ＋ │       │
│   ├──────────────────────────────────────────────────────────────┤       │
│   │ Bagaimana jika saya mengunggah PDF yang sama dua kali?    ＋ │       │
│   ├──────────────────────────────────────────────────────────────┤       │
│   │ Apakah bisa dibuka dari HP?                               ＋ │       │
│   └──────────────────────────────────────────────────────────────┘       │
└──────────────────────────────────────────────────────────────────────────┘
        wadah maks 720, rata tengah; memakai <details> bawaan HTML
```

### 2.6 CTA penutup

```text
┌──────────────────────────────────────────────────────────────────────────┐
│   ┌──────────────────────────────────────────────────────────────────┐   │
│   │ (primary-tint, radius-lg, padding 24)                            │   │
│   │      Siap merapikan koleksi PDF Anda?                            │   │
│   │      Buat akun dalam satu menit.                                 │   │
│   │                      [ Buat Akun Sekarang ]  ← primary           │   │
│   └──────────────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────────────┘
```

Tombol di sini `primary` karena berada di atas latar terang, bukan di atas blok merah. Ini aksi yang sama dengan CTA hero, jadi tidak menambah aksi utama yang berbeda.

### 2.7 Footer (3 kolom)

```text
┌──────────────────────────────────────────────────────────────────────────┐
│ ──────────────────────────────── outline 1px ────────────────────────── │
│                                                                          │
│  EMS                       Navigasi                  Akun                │
│  Ebook Management          Tentang                   Masuk               │
│  System. Simpan dan        Keunggulan                Buat Akun           │
│  kelola PDF pribadi        Cara Pakai                                    │
│  Anda.                     Pertanyaan                                    │
│                                                                          │
│  ───────────────────────────────────────────────────────────────────     │
│  © 2026 EMS. Dibuat oleh The-Noctura.                                    │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Mobile dan tablet

### 3.1 Tablet (600 sampai 1023)

Struktur sama dengan desktop. Keunggulan menjadi 2 kolom. Cara Pakai tetap 3 kolom bila muat, bila tidak ditumpuk. Footer tetap 3 kolom.

### 3.2 Mobile (di bawah 600)

```text
┌──────────────────────────┐
│█████ HERO (primary) █████│
│█ ╭──────────────────────╮█│
│█ │ EMS     Masuk [Daftar]│█│  navlink disembunyikan
│█ ╰──────────────────────╯█│
│█                         █│
│█  Semua PDF Anda, Rapi   █│
│█  di Satu Tempat         █│
│█                         █│
│█  Unggah ebook PDF, ...  █│
│█                         █│
│█ [ Buat Akun Sekarang  ] █│  lebar penuh, tinggi 48
│█  Sudah punya akun? Masuk█│
│█                         █│
├──────────────────────────┤
│ Apa Itu EMS?             │
│ (teks, lalu kotak ikon)  │
├──────────────────────────┤
│ Kenapa Memakai EMS?      │
│ ┌──────────────────────┐ │  1 kolom
│ │ kartu 1              │ │
│ └──────────────────────┘ │
│ ...                      │
├──────────────────────────┤
│ Cara Memakai EMS         │  langkah ditumpuk,
│ (1) ...                  │  garis penghubung vertikal
│ (2) ...                  │
│ (3) ...                  │
├──────────────────────────┤
│ FAQ · CTA penutup        │
├──────────────────────────┤
│ Footer: 3 blok ditumpuk  │
└──────────────────────────┘
```

Keputusan untuk mobile:

- **Navlink disembunyikan di bawah 1024.** Menu hamburger butuh JavaScript, dan itu di luar kebutuhan sekarang. Pengunjung tetap bisa menggulir, dan footer memuat tautan yang sama.
- Navbar mobile hanya berisi logo, `Masuk`, dan `Daftar`. Tinggi 56, area sentuh tombol minimal 48.
- Padding halaman 16 (mobile) dan 24 (tablet ke atas).

---

## 4. Copywriting

Nada: ringkas, hangat, tanpa janji berlebihan. Hanya fitur yang benar-benar ada yang disebut.

### 4.1 Navbar

| Elemen  | Teks                                    |
| ------- | --------------------------------------- |
| Logo    | `EMS`                                   |
| Navlink | `Tentang` · `Keunggulan` · `Cara Pakai` |
| Aksi    | `Masuk` · `Daftar`                      |

### 4.2 Hero

| Elemen              | Teks                                                                                          |
| ------------------- | --------------------------------------------------------------------------------------------- |
| Headline            | `Semua PDF Anda, Rapi di Satu Tempat`                                                         |
| Sub-headline        | `Unggah ebook PDF, buka dari browser mana pun, dan tandai mana yang sudah selesai Anda baca.` |
| CTA                 | `Buat Akun Sekarang` (menuju `/register`)                                                     |
| Tautan di bawah CTA | `Sudah punya akun? Masuk` (menuju `/login`)                                                   |

### 4.3 Tentang

| Elemen     | Teks                                                                                                                                                                                                       |
| ---------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Judul      | `Apa Itu EMS?`                                                                                                                                                                                             |
| Paragraf 1 | `EMS (Ebook Management System) adalah tempat penyimpanan ebook pribadi berbasis web. Anda mengunggah PDF sekali, lalu koleksi itu tersimpan di akun Anda dan bisa dibuka kapan saja lewat browser.`        |
| Paragraf 2 | `Ebook sering tersebar di banyak folder dan perangkat, dan lama-lama sulit diingat mana yang sudah dibaca. EMS mengumpulkannya dalam satu daftar yang jelas, lengkap dengan status baca untuk setiap PDF.` |
| Kotak ikon | `Satu koleksi.` / `Satu akun.`                                                                                                                                                                             |

### 4.4 Keunggulan

Judul bagian: `Kenapa Memakai EMS?`

| #   | Judul kartu              | Isi                                                                                          | Ikon     |
| --- | ------------------------ | -------------------------------------------------------------------------------------------- | -------- |
| 1   | `Koleksi terpusat`       | `Semua PDF Anda ada dalam satu daftar, diurutkan dari yang terbaru.`                         | library  |
| 2   | `Buka dari browser`      | `Tidak perlu memasang aplikasi. PDF langsung terbuka di penampil bawaan browser Anda.`       | document |
| 3   | `Status baca yang jelas` | `Tandai PDF sebagai sudah atau belum dibaca, dan lihat ringkasannya di bagian atas koleksi.` | check    |
| 4   | `Privat dan aman`        | `File hanya bisa dibuka oleh pemilik akun. Pengguna lain tidak bisa melihat koleksi Anda.`   | info     |

Catatan jujur: kartu 4 benar menurut aturan kepemilikan sistem (ebook milik user lain dijawab 404, dokumen 04 bagian 3.6). Jangan menambah klaim seperti "terenkripsi" karena itu tidak dirancang.

**Opsional, hanya bila fitur hapus sudah jadi sebelum deploy:** ganti kartu 4 atau tambahkan kartu kelima, `Anda yang berkuasa`: `Tidak ingin menyimpan sebuah PDF lagi? Hapus kapan saja, dan Anda bebas mengunggahnya kembali nanti.` Bila hapus belum jadi, jangan ditampilkan.

### 4.5 Cara Pakai

Judul bagian: `Cara Memakai EMS`

| Langkah | Judul             | Isi                                                                   |
| ------- | ----------------- | --------------------------------------------------------------------- |
| 1       | `Buat akun`       | `Daftar dengan nama, email, dan password minimal 8 karakter.`         |
| 2       | `Unggah PDF`      | `Pilih file PDF dari perangkat Anda. Ukuran maksimal 20 MB per file.` |
| 3       | `Baca dan tandai` | `Buka PDF di browser, lalu tandai sebagai sudah atau belum dibaca.`   |

### 4.6 FAQ

Judul bagian: `Pertanyaan yang Sering Muncul`

| Pertanyaan                                               | Jawaban                                                                                                                                           |
| -------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Format file apa yang didukung?`                         | `Saat ini hanya PDF, dengan ukuran maksimal 20 MB per file.`                                                                                      |
| `Apakah file saya bisa dilihat pengguna lain?`           | `Tidak. Setiap PDF hanya bisa dibuka oleh pemilik akun yang mengunggahnya.`                                                                       |
| `Bagaimana jika saya mengunggah PDF yang sama dua kali?` | `Sistem mengenali isi file, bukan namanya. PDF yang sama dengan yang sudah ada di koleksi Anda akan ditolak, jadi koleksi tidak terisi duplikat.` |
| `Apakah bisa dibuka dari HP?`                            | `Bisa, lewat browser di HP. Saat ini belum ada aplikasi Android.`                                                                                 |

### 4.7 CTA penutup

| Elemen  | Teks                               |
| ------- | ---------------------------------- |
| Judul   | `Siap merapikan koleksi PDF Anda?` |
| Kalimat | `Buat akun dalam satu menit.`      |
| Tombol  | `Buat Akun Sekarang`               |

### 4.8 Footer

| Kolom       | Isi                                                                                     |
| ----------- | --------------------------------------------------------------------------------------- |
| 1. Merek    | `EMS` · `Ebook Management System. Simpan dan kelola PDF pribadi Anda.`                  |
| 2. Navigasi | `Tentang` · `Keunggulan` · `Cara Pakai` · `Pertanyaan` (anchor ke bagian masing-masing) |
| 3. Akun     | `Masuk` · `Buat Akun`                                                                   |
| Baris bawah | `© 2026 EMS. Dibuat oleh [NAMA ANDA].`                                                  |

Isi `[NAMA ANDA]` sendiri. Saya tidak menambahkan tautan Kebijakan Privasi atau Kontak karena halamannya belum ada; tautan mati lebih buruk daripada tidak ada. Kalau kelak ada alamat email kontak, taruh di kolom 3.

---

## 5. Keadaan interaksi

Mengikuti dokumen 06 bagian 6.3.

| Elemen                        | Hover (Web)                          | Fokus keyboard                   | Tekan                       |
| ----------------------------- | ------------------------------------ | -------------------------------- | --------------------------- |
| Navlink                       | Latar `primary-tint`, teks `primary` | Cincin 2px `primary`             | Skala 0,97                  |
| Tombol `Daftar` (`secondary`) | Isi `black`, teks putih              | Cincin                           | Skala 0,97                  |
| Tombol `Masuk` (`text`)       | Latar `primary-tint`                 | Cincin                           | Skala 0,97                  |
| CTA hero (`inverse`)          | Isi `primary-tint`                   | Cincin **putih** (di atas merah) | Skala 0,97                  |
| Tautan `Masuk` di hero        | Garis bawah muncul                   | Cincin putih                     | -                           |
| Kartu keunggulan              | Garis berubah ke `primary`           | -                                | - (kartu tidak bisa diklik) |
| Baris FAQ                     | Latar `primary-tint`                 | Cincin                           | -                           |

### Gerak

| Situasi        | Gerak                                                   | Durasi                 |
| -------------- | ------------------------------------------------------- | ---------------------- |
| Halaman dibuka | Isi hero fade + naik 8px (`animate-enter`)              | `dur-slow`, `ease-out` |
| Klik navlink   | Gulir halus ke bagian yang dituju                       | bawaan browser         |
| Buka baris FAQ | Perubahan ikon plus menjadi minus, tanpa animasi tinggi | `dur-base`             |

Aturan: hanya `transform`, `opacity`, dan warna. Gulir halus (`scroll-behavior: smooth`) dimatikan bila "kurangi gerakan" aktif. Bagian lain tidak dianimasikan, supaya halaman tetap ringan.

---

## 6. Catatan implementasi (Blade + Tailwind)

Acuan untuk butir 5 di jadwal, bukan kode final.

- **File:** `resources/views/landing.blade.php`. Memakai `<x-layout bare title="EMS - Simpan dan Kelola PDF Anda">` (kerangka yang sudah ada, mode `bare` agar halaman mengatur lebarnya sendiri).
- **Route:** di `routes/web.php`, bagian "belum login" pada route `/` diganti dari `redirect()->route('login')` menjadi `view('landing')`. Yang sudah login tetap diarahkan ke `ebooks.index`.
- **Navbar melayang:** `sticky top-4 z-20 mx-4 sm:mx-6`, isinya `mx-auto grid max-w-[1200px] grid-cols-[1fr_auto_1fr] items-center rounded-full border border-outline bg-white px-4`. Navlink: `hidden lg:flex`.
- **Hero:** `bg-primary text-on-primary`, dan navbar diletakkan di dalamnya dengan margin negatif atau di atasnya memakai `-mb-` agar tampak melayang di atas merah. Cara paling sederhana: navbar `sticky` berada di dalam `<section>` hero.
- **Jarak antar anchor:** beri `scroll-mt-24` pada setiap `<section id="...">` supaya judul tidak tertutup navbar saat gulir.
- **Gulir halus:** `html { scroll-behavior: smooth; }` di `app.css`, dibungkus `@media (prefers-reduced-motion: no-preference)`.
- **FAQ:** elemen `<details>` dan `<summary>`, tanpa JavaScript.
- **Tombol:** `x-link-button` dengan varian `inverse`, `primary`, dan `secondary` sudah ada. Varian `text` belum ada di `link-button.blade.php`, jadi untuk tautan `Masuk` di navbar pakai tautan biasa bergaya `text-primary` dengan padding, atau tambahkan satu varian `text` ke komponen itu.
- **Ikon:** `library`, `document`, `check`, `info`, dan `plus` sudah ada sebagai komponen `x-icon.*`.
- **Tag judul:** satu `h1` (headline hero), `h2` untuk judul tiap bagian, `h3` untuk judul kartu.

---

## 7. Daftar periksa (dokumen 06 bagian 14)

- [ ] Hanya memakai token; tanpa hex atau nilai arbitrary.
- [ ] Tanpa bayangan dan tanpa gradien (navbar melayang dibedakan oleh garis, bukan bayangan).
- [ ] Hanya satu blok merah pekat besar (hero); kotak CTA penutup memakai `primary-tint`.
- [ ] Teks di atas hero memenuhi kontras (putih di atas `red-600`: 5,6:1).
- [ ] Setiap elemen interaktif punya hover (Web), fokus, dan tekan; cincin fokus putih di atas hero.
- [ ] Anchor tidak tertutup navbar (`scroll-mt`).
- [ ] Aman dengan "kurangi gerakan" aktif.
- [ ] Tidak ada klaim fitur yang belum ada (Android, lupa password, enkripsi).
- [ ] Mobile 360 px: navbar muat satu baris, CTA lebar penuh.
- [ ] `[NAMA ANDA]` di footer sudah diganti.

---

## 8. Keputusan dan catatan

1. **Landing page ditambahkan di luar dokumen 02.** Alasannya, sistem ini publik dan pengunjung asing perlu tahu apa itu EMS sebelum mendaftar. Catat di Log Perubahan Rancangan (dokumen 05 bagian 9).
2. **Hero berwarna merah penuh** sebagai satu-satunya blok merah besar. Konsekuensinya, CTA hero memakai varian `inverse` (putih), bukan `primary`.
3. **Navlink disembunyikan di mobile** supaya tidak perlu JavaScript menu.
4. **FAQ menyebut "belum ada aplikasi Android"** dengan jujur, karena aplikasi Android belum dikerjakan.
5. **Kartu hapus ebook bersyarat.** Tampilkan hanya jika fitur hapus sudah berjalan saat deploy.
6. **Tidak ada** halaman kebijakan privasi, kontak, atau lupa password; semuanya belum ada, jadi tidak ditautkan.
