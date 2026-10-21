# Wireframe 5 dari 5: Empty State (revisi 5)

Dokumen ini adalah wireframe tampilan **Empty State** (koleksi kosong) versi **Web** untuk proyek Ebook Storage & To-Do List. Tampilan ini muncul pada halaman Daftar Ebook (`GET /ebooks`) ketika user belum memiliki ebook (02-User-Flow bagian 15; 01-Use-Case UC-04: koleksi kosong). Dikerjakan pada chunk C10. Versi Android disesuaikan pada chunk C14.

**Aturan acuan.** Warna, tipografi, radius, jarak, gerak, dan komponen mengikuti **06-Design-System versi 1.2**. Wireframe ini **memadukan** desainnya sendiri dengan dokumen 06: bagian yang tidak bertentangan mengikuti dokumen 06, dan perluasan yang dibuat wireframe tercatat di bagian 1. Bila ada perbedaan lain yang tidak tercatat, dokumen 06 yang berlaku. Dokumen 06 tidak diubah.

Halaman tujuan tombol Upload dirancang di Wireframe 4 (Upload); di dokumen ini tombol hanya digambar sebagai titik masuk.

---

## 1. Perpaduan dengan dokumen 06

### 1.1 Perluasan dari wireframe (di luar tulisan dokumen 06)

| #   | Dokumen 06                                              | Wireframe (dipadukan)                                                                                   | Cara dipadukan                                                                                                                            |
| --- | ------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | 7.7: tombol Empty State berlabel "Tambah PDF"           | Label **`+ Tambah PDF Pertama ke Koleksi`**                                                             | Gaya tombol `primary` tetap dari 06; hanya teks label yang diperluas agar menyebut tujuan. Di mobile boleh `+ Tambah PDF Pertama`         |
| 2   | 7.7: "satu kalimat berwarna `text`"                     | **Judul pendek + satu kalimat penjelasan**                                                              | Warna `text` dan kotak `primary-tint` dari 06 dipakai; judul `title-md` memberi konteks, kalimat `body` menyebut manfaat. Tanpa ilustrasi |
| 3   | 7.7: ikon 64 dp; mobile ditumpuk dan di tengah vertikal | **Mobile ringkas**: ikon 48 di kiri teks, padding 16, kalimat `body-sm`, tombol dan catatan di bawahnya | Kotak mobile hemat tempat (±200 px dari ±340 px, perkiraan) agar tidak mendominasi layar kecil. Desktop tetap ikon 64 dan padding 24      |

### 1.2 Perluasan pola 06 ke hal yang belum diatur

| Hal                             | Keputusan                                                                                                                                                                  |
| ------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Bilah atas Web di bawah 1024 px | Memakai **pola Android dokumen 06 bagian 7.6**: nama aplikasi dengan ringkasan `body-sm` di bawahnya di dalam bilah, ikon logout di kanan. Tinggi tetap 64 (06 bagian 5.2) |
| Keadaan error muat koleksi      | Kotak error `primary-tint` dengan `role="alert"` dan tombol `secondary`                                                                                                    |
| Tablet                          | Sama dengan desktop selama muat; ditumpuk bila teks terbungkus tiga baris                                                                                                  |
| Pesan setelah upload pertama    | Snackbar `ink` dan sorotan kartu baru (06 bagian 6.4)                                                                                                                      |

---

## 2. Perubahan dari revisi 1

| #   | Revisi 1                             | Sekarang                                                            | Acuan 06 |
| --- | ------------------------------------ | ------------------------------------------------------------------- | -------- |
| 1   | Header putih biasa                   | Bilah atas merah, tinggi 64 mobile / 56 desktop                     | 7.6, 5.2 |
| 2   | Kartu kecil di tengah halaman kosong | Kotak `primary-tint` selebar wadah (maks 1200), berbaris di desktop | 7.7, 8.1 |
| 3   | Ikon emoji                           | Ikon SVG `library_books` 64, warna `primary`, `aria-hidden`         | 7.9      |
| 4   | Tombol `Keluar` bertulisan           | Tombol ikon logout dengan `aria-label="Keluar"`                     | 7.6      |
| 5   | Tanpa keadaan interaksi              | Hover, fokus, tekan, gerak masuk                                    | 6        |
| 6   | Error hanya digambar sekilas         | Kotak error `primary-tint` + ikon + `role="alert"`                  | 3.4, 7.8 |

Judul dan penjelasan dari revisi 1 dipertahankan.

---

## 3. Keadaan default (koleksi kosong), desktop ≥ 1024 px

Bilah atas selebar layar; isinya dan kotak Empty State dibatasi wadah yang sama (maks 1200).

```text
┌────────────────────────────────────────────────────────────────────────┐
│██████████████████████████████ BILAH ATAS MERAH ████████████████████████│  tinggi 56
│█ Ebook Storage                    Belum ada ebook   Noctura   [ ⎋ ]   █│  teks putih
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │  latar putih
│   Koleksi Ebook Saya                                                   │  title-lg
│                                                                        │
│   ┌────────────────────────────────────────────────────────────────┐   │
│   │ (primary-tint, radius-lg, padding 24)                          │   │
│   │                                                                │   │
│   │   ┌────┐   Koleksi Anda masih kosong              ← title-md   │   │
│   │   │ svg│   Tambahkan PDF pertama Anda agar bisa                │   │
│   │   └────┘   dibuka dan ditandai dari Web maupun    ← body       │   │
│   │   ikon 64  Android.                                            │   │
│   │                                                                │   │
│   │            [ ＋ Tambah PDF Pertama ke Koleksi ]  ← primary,pil │   │
│   │            Hanya PDF, maksimal 20 MB.            ← caption     │   │
│   │                                                                │   │
│   └────────────────────────────────────────────────────────────────┘   │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

Catatan komposisi: putih ±60% (latar), merah ±30% (bilah atas, ikon, tombol), hitam ±10% (teks). Hanya ada **satu** blok merah pekat besar (bilah atas); kotak Empty State sengaja `primary-tint` yang lembut.

Ringkasan di bilah atas berisi `Belum ada ebook`, bukan `0 dari 0 ebook sudah dibaca`. Tombol `Tambah PDF` **tidak** ditampilkan di bilah atas saat kosong, supaya hanya ada satu aksi utama.

---

## 4. Tablet dan mobile

### 4.1 Tablet (600 sampai 1023 px)

Bilah atas tinggi 64 dengan pola mobile di bawah (ringkasan di bawah nama aplikasi, di dalam bilah). Padding halaman 24, tombol tinggi 48. Kotak memakai tata letak ringkas seperti mobile (ikon di kiri teks) dengan padding 24 dan kalimat `body-sm`; berpindah ke tata letak desktop mulai 1024 px.

### 4.2 Mobile (< 600 px)

Kotak dibuat ringkas: ikon 48 di kiri, judul dan kalimat di kanan, lalu tombol selebar kotak dan catatan di bawahnya. Padding 16, jarak antar bagian 12.

```text
┌──────────────────────────┐
│██████ BILAH ATAS ████████│  tinggi 64
│█ Ebook Storage     [ ⎋ ]█│  title-md, putih
│█ Belum ada ebook         █│  body-sm, di dalam bilah
├──────────────────────────┤
│                          │
│ Koleksi Ebook Saya       │  title-lg
│                          │
│ ┌──────────────────────┐ │
│ │ primary-tint · p 16  │ │
│ │ ┌───┐ Koleksi Anda   │ │
│ │ │svg│ masih kosong   │ │  title-md
│ │ └───┘ Tambahkan PDF  │ │
│ │  48   pertama Anda   │ │
│ │       agar bisa      │ │  body-sm, rata kiri
│ │       dibuka dan     │ │
│ │       ditandai dari  │ │
│ │       Web maupun     │ │
│ │       Android.       │ │
│ │                      │ │
│ │ [＋ Tambah PDF       │ │  primary, lebar kotak,
│ │    Pertama ]         │ │  tinggi 48
│ │ Hanya PDF, maks 20 MB│ │  caption
│ └──────────────────────┘ │
└──────────────────────────┘
```

Gambar di atas sempit; pada lebar 360 px kolom teks sekitar 230 px sehingga kalimat penjelasan biasanya hanya tiga baris dan label tombol muat satu baris (`+ Tambah PDF Pertama`). Perkiraan tinggi kotak sekitar 200 px, dibanding sekitar 340 px pada tata letak ditumpuk sebelumnya.

---

## 5. Keadaan interaksi

Mengikuti dokumen 06 bagian 6.3. Semua perubahan memakai `ease-standard` dan `dur-fast` atau `dur-base`.

| Elemen                              | Hover (Web)                                              | Fokus keyboard                  | Tekan                             |
| ----------------------------------- | -------------------------------------------------------- | ------------------------------- | --------------------------------- |
| Tombol utama                        | Isi `primary-hover`; ikon plus berputar 90° (`dur-base`) | Cincin 2px `primary`, jarak 2px | Skala 0,97, isi `primary-pressed` |
| Tombol ikon logout (di bilah merah) | Lingkaran putih 15%                                      | Cincin **putih** 2px            | Skala 0,95                        |

Kotak Empty State tidak dapat diklik; hanya tombol di dalamnya yang interaktif, jadi kotak tidak punya efek hover.

### 5.1 Gerak masuk

| Situasi        | Gerak                                               | Durasi                 |
| -------------- | --------------------------------------------------- | ---------------------- |
| Halaman dibuka | Kotak Empty State fade + naik 8px (`animate-enter`) | `dur-slow`, `ease-out` |

Gerak hanya `transform` dan `opacity`. Dengan "kurangi gerakan" aktif, kotak langsung tampil tanpa gerak naik dan ikon plus tidak berputar.

---

## 6. Keadaan lain

### 6.1 Koleksi gagal dimuat (bukan kosong)

Empty State **tidak boleh** tampil saat terjadi error, karena menyiratkan data user hilang (UC-04: koleksi belum dapat ditampilkan). Bilah atas tetap tampil; ringkasan **dikosongkan** (tidak boleh menulis `Belum ada ebook`).

```text
┌────────────────────────────────────────────────────────────────┐
│██████ BILAH ATAS (nama aplikasi, user, logout) ████████████████│
├────────────────────────────────────────────────────────────────┤
│  Koleksi Ebook Saya                                            │
│                                                                │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ (primary-tint, role="alert")                             │  │
│  │ ⚠  Koleksi belum dapat ditampilkan.   ← warna error      │  │
│  │    Periksa koneksi Anda, lalu muat ulang halaman.        │  │
│  │                                                          │  │
│  │    [ Muat Ulang Halaman ]            ← secondary, pil    │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────┘
```

Error dibedakan dari merek lewat **warna `error`, ikon peringatan, dan kotak tint** sekaligus (dokumen 06 bagian 3.4).

### 6.2 Setelah register dan login pertama

Sama dengan keadaan default. Tidak ada layar sambutan terpisah.

### 6.3 Setelah upload pertama berhasil (pesan dari C11)

Halaman berganti menjadi Daftar Ebook dengan satu kartu. Empty State tidak tampil lagi.

```text
              ┌──────────────────────────────────────┐
              │ ✓  "Kotlin Basics" ditambahkan.      │   snackbar pil, isi hitam (ink),
              └──────────────────────────────────────┘   hilang otomatis 4 detik
```

Ringkasan berubah menjadi `0 dari 1 sudah dibaca` (dari `fn_count_read_ebooks`).

---

## 7. Daftar elemen dan token

| Elemen                       | Aturan                                                                                                                                                                                                                 | Token / class (dokumen 06)                                                                            |
| ---------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------- |
| Bilah atas                   | Latar merah. Desktop: nama aplikasi di kiri; ringkasan, nama user, ikon logout di kanan. Di bawah 1024 px: nama aplikasi dengan ringkasan di bawahnya (kiri), ikon logout (kanan). Tanpa tombol Tambah PDF saat kosong | `topbar bg-primary text-on-primary`, `min-h-16 lg:min-h-14`                                           |
| Ringkasan                    | `Belum ada ebook`; `body-sm`. Kosong pada keadaan error                                                                                                                                                                | `text-body-sm`                                                                                        |
| Tombol logout                | Tombol ikon, `aria-label="Keluar"`, membungkus form `POST /logout`                                                                                                                                                     | `size-10 hover:bg-white/15`                                                                           |
| Judul halaman                | `Koleksi Ebook Saya`, `h1`                                                                                                                                                                                             | `text-title-lg font-semibold`                                                                         |
| Kotak Empty State            | Latar tint, sudut besar, tanpa bayangan dan tanpa garis tepi                                                                                                                                                           | `rounded-lg bg-primary-tint p-6`                                                                      |
| Tata letak kotak             | Grid dua kolom (ikon, teks); tombol dan catatan selebar kotak di mobile, sejajar kolom teks di desktop                                                                                                                 | `grid grid-cols-[3rem_1fr] items-start gap-x-3 gap-y-3 p-4 sm:p-6 lg:grid-cols-[4rem_1fr] lg:gap-x-4` |
| Ikon                         | SVG `library_books`, `aria-hidden="true"`, warna `primary`; 48 di mobile, 64 di desktop                                                                                                                                | `size-12 text-primary lg:row-span-3 lg:size-16`                                                       |
| Judul kotak                  | `Koleksi Anda masih kosong`, `h2`                                                                                                                                                                                      | `text-title-md font-semibold text-text`                                                               |
| Penjelasan                   | Satu kalimat yang menyebut manfaat; `body-sm` di mobile, `body` di desktop                                                                                                                                             | `text-body-sm text-text lg:text-body`                                                                 |
| Tombol utama                 | Varian `primary`, menuju Upload                                                                                                                                                                                        | `<x-button>`, ikon plus `group-hover:rotate-90`                                                       |
| Catatan batas                | `Hanya PDF, maksimal 20 MB.`                                                                                                                                                                                           | `text-caption text-text-muted`                                                                        |
| Buka PDF, ubah status, hapus | **Tidak ada**, belum ada item                                                                                                                                                                                          | -                                                                                                     |

Kontras `gray-600` (`#5A5050`) di atas `primary-tint` (`#FDECEC`) adalah sekitar **6,8:1**, memenuhi minimal 4,5:1.

## 8. Teks

| Bagian            | Teks                                                                                |
| ----------------- | ----------------------------------------------------------------------------------- |
| Ringkasan         | `Belum ada ebook`                                                                   |
| Judul halaman     | `Koleksi Ebook Saya`                                                                |
| Judul kotak       | `Koleksi Anda masih kosong`                                                         |
| Penjelasan        | `Tambahkan PDF pertama Anda agar bisa dibuka dan ditandai dari Web maupun Android.` |
| Tombol            | `+ Tambah PDF Pertama ke Koleksi` (mobile: `+ Tambah PDF Pertama`)                  |
| Catatan           | `Hanya PDF, maksimal 20 MB.`                                                        |
| Error: judul      | `Koleksi belum dapat ditampilkan.`                                                  |
| Error: bantuan    | `Periksa koneksi Anda, lalu muat ulang halaman.`                                    |
| Error: tombol     | `Muat Ulang Halaman`                                                                |
| Aria-label logout | `Keluar`                                                                            |

---

## 9. Contoh implementasi (Blade + Tailwind)

Acuan untuk C10, bukan kode final. Memakai komponen dokumen 06 bagian 10.4. Markup bilah atas lengkap ada di Wireframe 3 (Daftar Ebook) bagian 8.

```blade
@if ($ebooks->isEmpty())
    <section class="animate-enter grid grid-cols-[3rem_1fr] items-start gap-x-3 gap-y-3 rounded-lg bg-primary-tint p-4 sm:p-6 lg:grid-cols-[4rem_1fr] lg:gap-x-4">
        <x-icon.library class="size-12 text-primary lg:row-span-3 lg:size-16" aria-hidden="true" />

        <div class="grid gap-1">
            <h2 class="text-title-md font-semibold text-text">Koleksi Anda masih kosong</h2>
            <p class="text-body-sm text-text lg:text-body">
                Tambahkan PDF pertama Anda agar bisa dibuka dan ditandai dari Web maupun Android.
            </p>
        </div>

        <x-button variant="primary" href="{{ route('ebooks.create') }}"
                  class="col-span-2 w-full lg:col-span-1 lg:col-start-2 lg:mt-1 lg:w-fit">
            <x-icon.plus class="size-6 transition-transform duration-200 ease-standard group-hover:rotate-90 motion-reduce:transform-none" aria-hidden="true" />
            <span class="lg:hidden">Tambah PDF Pertama</span>
            <span class="hidden lg:inline">Tambah PDF Pertama ke Koleksi</span>
        </x-button>

        <p class="col-span-2 text-caption text-text-muted lg:col-span-1 lg:col-start-2">Hanya PDF, maksimal 20 MB.</p>
    </section>
@endif
```

Catatan implementasi:

- Komponen `button` dokumen 06 merender `<button>`. Untuk tombol yang berpindah halaman, perlu prop `href` yang merender `<a>` dengan class yang sama (tombol di dalam tautan bukan HTML valid). Selama route Upload belum ada (C11), pakai `<x-button disabled>`.
- Keadaan kosong ditentukan **di server** (`$ebooks->isEmpty()`), sehingga tidak ada kedipan "kosong" sebelum data tiba. Keadaan error ditangani terpisah, bukan lewat `isEmpty()` pada hasil yang gagal dimuat.
- Seluruh warna dan ukuran memakai token; tidak ada hex atau nilai arbitrary.

---

## 10. Keputusan dan catatan

1. **Satu ajakan, satu tombol.** Empty State hanya menawarkan satu aksi agar user langsung tahu langkah berikutnya.
2. **Tidak menjanjikan fitur yang belum ada.** Teks tidak menyebut discovery atau impor otomatis (Local Discovery adalah P2 dan tidak dijadwalkan).
3. **Empty State dan error dibedakan.** Hanya data yang berhasil dimuat dengan jumlah nol yang memakai tampilan ini.
4. **Ikon SVG, bukan emoji.** Mengikuti dokumen 06 bagian 7.9.
5. **Tombol menuju Upload.** Selama C11 belum dikerjakan, tombol dinonaktifkan sementara.
6. **Android (C14).** Teks dan aturan sama. Tata letak ringkas seperti bagian 4.2; tombol `primary` berupa pil selebar kotak, bukan tombol aksi mengambang, supaya hanya ada satu aksi utama.
7. **Aksesibilitas.** Ikon dekoratif `aria-hidden`; satu `h1` per halaman dan `h2` untuk kotak; tombol logout berlabel; error memakai `role="alert"`; fokus keyboard terlihat (cincin merah, putih di atas bilah merah); target sentuh ≥ 48 di mobile.
8. **Wireframe lain.** Login, Register, dan Upload masih berasumsi satu kolom di desktop (lihat Parkir dokumen 05).

---

## 11. Daftar periksa (dokumen 06 bagian 14)

- [ ] Hanya memakai token, tanpa hex atau nilai arbitrary.
- [ ] Tanpa bayangan dan tanpa gradien.
- [ ] Hanya satu blok merah besar (bilah atas); hanya satu aksi utama.
- [ ] Error memakai warna `error`, ikon, dan kotak tint, serta `role="alert"`.
- [ ] Tombol punya hover, fokus, dan tekan; hover aman di layar sentuh (Tailwind v4).
- [ ] Gerak hanya `transform` dan `opacity`; ≤ 400 ms; aman dengan "kurangi gerakan".
- [ ] Desktop memakai tata letak berbaris selebar wadah, bukan kartu kecil di tengah.

---

## 12. Status wireframe C10

- [x] Login (revisi 3)
- [x] Register (revisi 3)
- [x] Daftar Ebook (revisi 4)
- [x] Upload (revisi 4)
- [x] Empty State (revisi 5)
