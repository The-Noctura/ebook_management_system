# Wireframe 4 dari 5: Upload (revisi 4)

Dokumen ini adalah wireframe halaman **Upload** (manual upload PDF) versi **Web** untuk proyek Ebook Storage & To-Do List. Halaman ini mencakup UC-07 (Manual Upload) dan menjadi tujuan tombol `Tambah PDF` di Daftar Ebook serta tombol di Empty State. Rute: `GET /ebooks/create` (form) dan `POST /ebooks` (proses), dikerjakan pada chunk C11. Versi Android disesuaikan pada chunk C16 dan C17.

**Aturan acuan.** Warna, tipografi, radius, jarak, gerak, dan komponen mengikuti **06-Design-System versi 1.2**. Wireframe ini **memadukan** desainnya sendiri dengan dokumen 06: bagian yang tidak bertentangan mengikuti dokumen 06, dan perluasan yang dibuat wireframe tercatat di bagian 1. Bila ada perbedaan lain yang tidak tercatat, dokumen 06 yang berlaku. Dokumen 06 tidak diubah.

> **Catatan sumber.** Berkas wireframe Upload (draf C10) tidak tersedia saat revisi ini dibuat. Isi disusun dari dokumen 01 (UC-07), 02 (bagian 7), 04 (bagian 5.5 dan 7.1), 06 (bagian 7.1, 7.8, 8.1, 12), wireframe Daftar Ebook dan Empty State, serta butir C11 dokumen 05. Bandingkan dengan draf lama sebelum menimpanya. Penomoran berkas (`05`) hanya mengikuti urutan pembuatan; ganti sesuai konvensi Anda.

---

## 1. Perpaduan dengan dokumen 06

### 1.1 Perluasan dan pengurangan dari wireframe

| #   | Dokumen 06                                  | Wireframe (dipadukan)                                                                                                                                             | Cara dipadukan                                                                                                                          |
| --- | ------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | 7.1: aksi utama Upload; 12: tombol "Upload" | Label **`Tambahkan ke Koleksi`**                                                                                                                                  | Gaya `primary` dari 06; label menyebut tujuan, sejalan dengan Empty State dan UC-06 ("Tambah ke Koleksi")                               |
| 2   | 7.8 dan 10.4: bar progres upload pil tipis  | **Versi inti memakai indikator pada tombol** (06 bagian 7.1: tombol diganti indikator progres); bar progres bernilai hanya pada peningkatan opsional (bagian 6.3) | Form HTML biasa tidak bisa melaporkan persentase upload; bar bernilai membutuhkan JavaScript (XHR). Bar tetap sesuai 06 bila dikerjakan |

### 1.2 Perluasan pola 06 ke hal yang belum diatur

| Hal                           | Keputusan                                                                                                                                                                                    |
| ----------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Kolom file                    | Memakai `<input type="file">` asli dengan gaya tombol `secondary` pada bagian `file:`. Tidak ada komponen file di 06; pendekatan ini jalan tanpa JavaScript dan tetap dapat diakses keyboard |
| Bilah atas halaman ini        | Pola yang sama dengan Daftar Ebook, tanpa tombol `Tambah PDF` dan tanpa ringkasan (user sudah berada di halaman tambah). Nama aplikasi menaut ke `/ebooks`                                   |
| Duplikat                      | Pesan error disertai tautan `Lihat di koleksi` ke `/ebooks#ebook-{existing_id}`                                                                                                              |
| Pemeriksaan ukuran di browser | JavaScript kecil menolak file di atas 20 MB sebelum dikirim (bagian 6.4)                                                                                                                     |
| Drag and drop                 | Tidak ada pada MVP                                                                                                                                                                           |
| Tombol Batal                  | Varian `secondary`, kembali ke `/ebooks`. Hanya ada satu tombol `primary`                                                                                                                    |

---

## 2. Keadaan default, desktop ≥ 1024 px

Dua kolom (06 bagian 8.1): kiri form, kanan kotak aturan `primary-tint`. Bilah atas selebar layar; isi dibatasi wadah maks 1200.

```text
┌──────────────────────────────────────────────────────────────────────────┐
│█████████████████████████████ BILAH ATAS MERAH ███████████████████████████│ tinggi 56
│█ Ebook Storage                                          Noctura     [⎋]  █│
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Tambah PDF ke Koleksi                                    ← title-lg     │
│                                                                          │
│  File PDF                                   ┌──────────────────────────┐ │
│  ┌────────────────────────────────────┐     │ (primary-tint, radius-lg)│ │
│  │ [ Pilih File ]  Belum ada file     │     │ ⓘ  Aturan upload         │ │
│  └────────────────────────────────────┘     │                          │ │
│  Hanya PDF, maksimal 20 MB.                 │ • Hanya PDF, maksimal    │ │
│                                             │   20 MB.                 │ │
│  Judul (opsional)                           │ • PDF yang sama tidak    │ │
│  ┌────────────────────────────────────┐     │   ditambahkan dua kali.  │ │
│  │ Kosongkan untuk memakai nama file  │     │ • Upload membutuhkan     │ │
│  └────────────────────────────────────┘     │   koneksi internet.      │ │
│                                             └──────────────────────────┘ │
│  [ Tambahkan ke Koleksi ]  [ Batal ]                                     │
│    ↑ primary, pil            ↑ secondary, pil                            │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
   kolom kiri: maks 576 · kolom kanan: ±360 · jarak antar kolom 24
```

Catatan komposisi: putih ±60% (latar, form), merah ±30% (bilah atas, tombol utama), hitam ±10% (teks). Kotak aturan memakai `primary-tint` yang lembut, jadi hanya ada satu blok merah pekat besar (bilah atas).

---

## 3. Tablet dan mobile

### 3.1 Tablet (600 sampai 1023 px)

Satu kolom. Bilah atas tinggi 64. Kotak aturan berada **di atas** form (06 bagian 8.1). Padding halaman 24, input tinggi 56, tombol tinggi 48.

### 3.2 Mobile (< 600 px)

```text
┌──────────────────────────┐
│██████ BILAH ATAS ████████│ tinggi 64
│█ Ebook Storage     [ ⎋ ]█│
├──────────────────────────┤
│ Tambah PDF ke Koleksi    │ title-lg
│                          │
│ ┌──────────────────────┐ │
│ │ primary-tint         │ │
│ │ ⓘ Aturan upload      │ │
│ │ • Hanya PDF, maks    │ │
│ │   20 MB.             │ │
│ │ • PDF yang sama tidak│ │
│ │   ditambahkan dua    │ │
│ │   kali.              │ │
│ │ • Butuh koneksi      │ │
│ │   internet.          │ │
│ └──────────────────────┘ │
│                          │
│ File PDF                 │
│ ┌──────────────────────┐ │
│ │[Pilih File] Belum ada│ │ tinggi 56
│ └──────────────────────┘ │
│ Hanya PDF, maks 20 MB.   │
│                          │
│ Judul (opsional)         │
│ ┌──────────────────────┐ │
│ │ Kosongkan untuk      │ │
│ │ memakai nama file    │ │
│ └──────────────────────┘ │
│                          │
│ [ Tambahkan ke Koleksi ] │ primary, lebar penuh, 48
│ [        Batal        ]  │ secondary, lebar penuh, 48
└──────────────────────────┘
```

Jarak antar dua tombol minimal 8.

---

## 4. Keadaan formulir

### 4.1 File sudah dipilih

Kolom file menampilkan nama file bawaan browser. Dengan JavaScript, placeholder Judul berubah menjadi nama file tanpa ekstensi (contoh `Kotlin Basics`) sebagai pratinjau judul yang akan dipakai bila kolom dikosongkan.

```text
  File PDF
  ┌────────────────────────────────────────────┐
  │ [ Pilih File ]  Kotlin Basics.pdf          │
  └────────────────────────────────────────────┘
  Hanya PDF, maksimal 20 MB.

  Judul (opsional)
  ┌────────────────────────────────────────────┐
  │ Kotlin Basics                              │  ← placeholder (abu), bukan isian
  └────────────────────────────────────────────┘
```

### 4.2 Proses berjalan

Mengikuti 06 bagian 7.1: tombol diganti indikator dan tidak bisa ditekan dua kali. Kolom form dikunci selama proses.

```text
  [ ◌  Mengunggah…  ]     ← primary, nonaktif (opasitas 0,38), spinner di kiri
  [ Batal ]               ← tetap aktif; tekan = berhenti menunggu, kembali ke koleksi
```

- Tombol dikunci oleh JavaScript saat `submit`. Tanpa JavaScript, tombol tidak terkunci, tetapi upload ganda tetap aman karena pasangan `(user_id, file_hash)` unik (dokumen 03 bagian 12) dan dijawab sebagai duplikat.
- Spinner berputar (animasi memuat yang diizinkan 06). Dengan "kurangi gerakan" aktif, spinner berhenti dan label `Mengunggah…` tetap terbaca.
- `Batal` tidak membatalkan upload yang sudah sampai ke server; ia hanya meninggalkan halaman. Jika ebook sudah tersimpan, ia akan muncul di koleksi.

### 4.3 Berhasil

Redirect ke `/ebooks?baru={id}`. Daftar Ebook menampilkan snackbar `✓ "Kotlin Basics" ditambahkan ke koleksi.` dan menyorot kartu baru (lihat Wireframe 3 bagian 6.5). Empty State tidak tampil lagi bila ini upload pertama.

---

## 5. Keadaan error

Mengikuti 06 bagian 3.4 dan 7.2: warna `error`, ikon peringatan, kolom bergaris `error` dan bergetar. Pesan tampil di bawah kolom yang bermasalah; kesalahan tingkat halaman memakai kotak `primary-tint` dengan `role="alert"`. File yang dipilih tidak dapat dipertahankan oleh browser setelah muat ulang, sehingga user memilih ulang file; Judul tetap terisi.

### 5.1 File bukan PDF atau melebihi 20 MB (422)

```text
  File PDF
  ┌────────────────────────────────────────────┐
  │ [ Pilih File ]  Belum ada file             │  ← garis error, bergetar
  └────────────────────────────────────────────┘
  ⚠ File harus berformat PDF.
```

### 5.2 PDF duplikat (409 di API, kesalahan pada kolom file di Web)

```text
  File PDF
  ┌────────────────────────────────────────────┐
  │ [ Pilih File ]  Belum ada file             │
  └────────────────────────────────────────────┘
  ⚠ PDF ini sudah ada di koleksi Anda.  Lihat di koleksi →
```

`Lihat di koleksi` menaut ke `/ebooks#ebook-{existing_id}`, memanfaatkan fragmen yang sama dengan ubah status di Daftar Ebook. Tidak ada file yang tersimpan di R2 untuk duplikat (dokumen 04 bagian 5.5).

### 5.3 Gagal di server (500)

```text
┌──────────────────────────────────────────────────────────────┐
│ ⚠  PDF belum bisa ditambahkan. Silakan coba lagi.            │ ← primary-tint, role="alert"
│    [ Coba Lagi ]                                             │ ← secondary
└──────────────────────────────────────────────────────────────┘
```

Sesuai dokumen 04 bagian 5.5: bila penyimpanan ke R2 atau MySQL gagal, ebook tidak masuk koleksi. `Coba Lagi` hanya memfokuskan kolom file.

### 5.4 Tanpa koneksi

```text
┌──────────────────────────────────────────────────────────────┐
│ ⚠  Tidak ada koneksi. Coba lagi saat online.                 │ ← primary-tint, role="alert"
└──────────────────────────────────────────────────────────────┘
```

Fase 1 tidak punya antrean upload (dokumen 01 UC-07). Pemeriksaan memakai `navigator.onLine` saat submit (bagian 6.4); tanpa JavaScript, user melihat halaman error browser.

### 5.5 Kolom kosong atau tidak valid lainnya

| Kondisi                       | Pesan di bawah kolom              |
| ----------------------------- | --------------------------------- |
| File belum dipilih            | `Pilih file PDF terlebih dahulu.` |
| Judul lebih dari 255 karakter | `Judul maksimal 255 karakter.`    |

---

## 6. Interaksi, gerak, dan perilaku

### 6.1 Keadaan interaksi (dokumen 06 bagian 6.3)

| Elemen                               | Hover (Web)                                | Fokus keyboard               | Tekan                             |
| ------------------------------------ | ------------------------------------------ | ---------------------------- | --------------------------------- |
| Kolom file                           | Garis menjadi `black`                      | Garis `primary` + cincin 2px | -                                 |
| Tombol `Pilih File` (bagian `file:`) | Isi `black`, teks putih (gaya `secondary`) | Cincin                       | -                                 |
| Kolom Judul                          | Garis menjadi `black`                      | Garis `primary` + cincin 2px | -                                 |
| Tombol `Tambahkan ke Koleksi`        | Isi `primary-hover`                        | Cincin 2px `primary`         | Skala 0,97, isi `primary-pressed` |
| Tombol `Batal`                       | Isi `black`, teks putih                    | Cincin                       | Skala 0,97                        |
| Tombol logout                        | Lingkaran putih 15%                        | Cincin putih                 | Skala 0,95                        |

### 6.2 Gerak

| Situasi         | Gerak                                                                  | Durasi                 |
| --------------- | ---------------------------------------------------------------------- | ---------------------- |
| Halaman dibuka  | Form dan kotak aturan fade + naik 8px (`animate-enter`)                | `dur-slow`, `ease-out` |
| Kolom salah     | Pesan fade + naik 8px; kolom bergetar ±4px tiga kali (`animate-shake`) | `dur-slow`             |
| Proses berjalan | Tombol berganti indikator; spinner berputar                            | -                      |

Gerak hanya `transform`, `opacity`, dan warna. Dengan "kurangi gerakan" aktif, gerak spasial dihapus.

### 6.3 Peningkatan opsional: bar progres bernilai

Tidak masuk versi inti. Dikerjakan hanya bila waktu C11 tersisa (cut line: korbankan sebelum fungsi upload):

- JavaScript mengirim `FormData` lewat `XMLHttpRequest` dan memakai `upload.onprogress`.
- Bar mengikuti 06 bagian 7.8 dan 10.4: trek `outline`, isi `primary`, tinggi 8, `role="progressbar"` dengan `aria-valuenow`, animasi lewat `transform: scaleX`.
- Controller Web perlu membalas JSON untuk permintaan ini; menambah pekerjaan di luar C11 inti.

### 6.4 JavaScript kecil (semua opsional; form tetap berfungsi tanpanya)

1. Kunci tombol dan ganti label saat submit.
2. Placeholder Judul mengikuti nama file.
3. **Tolak file di atas 20 MB sebelum dikirim**, dengan pesan `Ukuran file maksimal 20 MB.` Ini penting: bila file melebihi `post_max_size` (25 MB, dokumen 04 bagian 7.1), PHP membuang seluruh isi request dan Laravel menampilkan halaman **419 Page Expired** (token CSRF ikut hilang), bukan pesan validasi. Pemeriksaan di browser mencegah kasus ini bagi sebagian besar user; uji sekali file di atas 25 MB pada C18 untuk melihat perilaku sebenarnya.
4. Periksa `navigator.onLine`; bila offline, tampilkan pesan bagian 5.4 dan batalkan submit.

---

## 7. Daftar elemen dan token

| Elemen        | Aturan                                                                                                      | Token / class                                                       |
| ------------- | ----------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------- |
| Bilah atas    | Nama aplikasi (tautan ke `/ebooks`), nama user (desktop), ikon logout. Tanpa Tambah PDF dan tanpa ringkasan | `topbar bg-primary text-on-primary`, `min-h-16 lg:min-h-14`         |
| Judul halaman | `Tambah PDF ke Koleksi`, `h1`                                                                               | `text-title-lg font-semibold`                                       |
| Tata letak    | Mobile satu kolom, kotak aturan di atas; desktop dua kolom, kotak aturan di kanan                           | `grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]`; kotak `lg:order-2` |
| Label         | `File PDF`, `Judul (opsional)`; selalu terlihat                                                             | `text-label font-medium`                                            |
| Kolom file    | Input file asli, bagian `file:` bergaya `secondary`                                                         | `min-h-14 rounded-md border border-outline-strong lg:min-h-11`      |
| Hint file     | `Hanya PDF, maksimal 20 MB.`                                                                                | `text-caption text-text-muted`                                      |
| Kolom Judul   | Opsional, maks 255; placeholder `Kosongkan untuk memakai nama file`                                         | `<x-field name="title">`                                            |
| Tombol utama  | `Tambahkan ke Koleksi`, varian `primary`; saat proses `Mengunggah…`                                         | `<x-button variant="primary" type="submit">`                        |
| Tombol Batal  | Varian `secondary`, menaut ke `/ebooks`                                                                     | `<x-button variant="secondary" href=...>`                           |
| Kotak aturan  | Ikon info, judul `Aturan upload`, tiga butir                                                                | `rounded-lg bg-primary-tint p-4 lg:p-6`                             |
| Kotak error   | Ikon peringatan + pesan, `role="alert"`                                                                     | `rounded-md bg-primary-tint px-4 py-3 text-error`                   |
| Pesan kolom   | Ikon peringatan + teks `caption`                                                                            | `text-caption text-error`                                           |

Kontras `gray-600` di atas `primary-tint` sekitar 6,8:1, memenuhi 4,5:1.

---

## 8. Teks

| Bagian                   | Teks                                                                                                                |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------- |
| Judul halaman            | `Tambah PDF ke Koleksi`                                                                                             |
| Label file               | `File PDF`                                                                                                          |
| Hint file                | `Hanya PDF, maksimal 20 MB.`                                                                                        |
| Label judul              | `Judul (opsional)`                                                                                                  |
| Placeholder judul        | `Kosongkan untuk memakai nama file`                                                                                 |
| Tombol utama             | `Tambahkan ke Koleksi` / proses `Mengunggah…`                                                                       |
| Tombol sekunder          | `Batal`                                                                                                             |
| Kotak aturan: judul      | `Aturan upload`                                                                                                     |
| Kotak aturan: butir      | `Hanya PDF, maksimal 20 MB.` · `PDF yang sama tidak ditambahkan dua kali.` · `Upload membutuhkan koneksi internet.` |
| Error: file kosong       | `Pilih file PDF terlebih dahulu.`                                                                                   |
| Error: bukan PDF         | `File harus berformat PDF.`                                                                                         |
| Error: terlalu besar     | `Ukuran file maksimal 20 MB.`                                                                                       |
| Error: judul panjang     | `Judul maksimal 255 karakter.`                                                                                      |
| Error: duplikat          | `PDF ini sudah ada di koleksi Anda.` + tautan `Lihat di koleksi`                                                    |
| Error: server            | `PDF belum bisa ditambahkan. Silakan coba lagi.` + tombol `Coba Lagi`                                               |
| Error: offline           | `Tidak ada koneksi. Coba lagi saat online.`                                                                         |
| Sukses (di Daftar Ebook) | `"{judul}" ditambahkan ke koleksi.`                                                                                 |
| Aria-label logout        | `Keluar`                                                                                                            |

Pesan validasi Laravel harus diterjemahkan ke bahasa Indonesia (C11, dokumen 05) agar sesuai tabel ini.

---

## 9. Contoh implementasi (Blade + Tailwind)

Acuan untuk C11, bukan kode final. Komponen dokumen 06 bagian 10.4; markup bilah atas ada di Wireframe 3 bagian 8 (tanpa tombol Tambah PDF dan ringkasan).

```blade
<h1 class="text-title-lg font-semibold">Tambah PDF ke Koleksi</h1>

<div class="grid gap-6 py-4 lg:grid-cols-[minmax(0,1fr)_360px]">

    {{-- kotak aturan: di atas pada mobile, di kanan pada desktop --}}
    <aside class="animate-enter rounded-lg bg-primary-tint p-4 lg:order-2 lg:self-start lg:p-6">
        <h2 class="flex items-center gap-2 text-title-md font-semibold text-text">
            <x-icon.info class="size-6 text-primary" aria-hidden="true" /> Aturan upload
        </h2>
        <ul class="mt-2 grid list-disc gap-1 pl-6 text-body text-text">
            <li>Hanya PDF, maksimal 20 MB.</li>
            <li>PDF yang sama tidak ditambahkan dua kali.</li>
            <li>Upload membutuhkan koneksi internet.</li>
        </ul>
    </aside>

    <form method="POST" action="{{ route('ebooks.store') }}" enctype="multipart/form-data"
          class="animate-enter grid max-w-xl content-start gap-4" data-upload-form novalidate>
        @csrf

        @if (session('upload_failed'))
            <div role="alert" class="flex animate-enter items-start gap-3 rounded-md bg-primary-tint px-4 py-3 text-error">
                <x-icon.warning class="size-6 shrink-0" aria-hidden="true" />
                <div class="grid gap-2">
                    <p>PDF belum bisa ditambahkan. Silakan coba lagi.</p>
                    <x-button variant="secondary" type="button" data-focus="file">Coba Lagi</x-button>
                </div>
            </div>
        @endif

        {{-- kolom file --}}
        @php $fileError = $errors->first('file'); @endphp
        <div class="grid gap-1">
            <label for="file" class="text-label font-medium">File PDF</label>
            <input id="file" name="file" type="file" accept="application/pdf,.pdf"
                   aria-describedby="file-hint {{ $fileError ? 'file-msg' : '' }}"
                   @if($fileError) aria-invalid="true" @endif
                   class="{{ $fileError ? 'animate-shake border-error' : 'border-outline-strong hover:border-ink' }}
                          min-h-14 w-full rounded-md border bg-background px-2 text-body text-text
                          transition-colors duration-200 ease-standard focus:border-primary lg:min-h-11
                          file:mr-4 file:min-h-10 file:cursor-pointer file:rounded-full file:border file:border-outline-strong
                          file:bg-transparent file:px-5 file:text-label file:font-medium file:text-text
                          file:transition file:duration-100 file:ease-standard hover:file:bg-ink hover:file:text-on-ink">
            @if ($fileError)
                <p id="file-msg" class="flex animate-enter items-start gap-1 text-caption text-error">
                    <x-icon.warning class="size-4 shrink-0" aria-hidden="true" /> {{ $fileError }}
                    @if (session('existing_id'))
                        <a href="{{ route('ebooks.index') }}#ebook-{{ session('existing_id') }}"
                           class="ml-1 text-primary underline underline-offset-4">Lihat di koleksi</a>
                    @endif
                </p>
            @endif
            <p id="file-hint" class="text-caption text-text-muted">Hanya PDF, maksimal 20 MB.</p>
        </div>

        <x-field name="title" label="Judul (opsional)" placeholder="Kosongkan untuk memakai nama file" :value="old('title')" />

        <div class="flex flex-col gap-2 pt-2 sm:flex-row">
            <x-button variant="primary" type="submit" class="w-full sm:w-auto" data-submit>
                <span data-label>Tambahkan ke Koleksi</span>
            </x-button>
            <x-button variant="secondary" href="{{ route('ebooks.index') }}" class="w-full sm:w-auto">Batal</x-button>
        </div>
    </form>
</div>
```

Catatan implementasi:

- Pesan duplikat dikirim controller lewat `back()->withErrors(['file' => 'PDF ini sudah ada di koleksi Anda.'])->with('existing_id', $e->existingId)->withInput()`. Kegagalan server memakai `->with('upload_failed', true)`.
- Aturan validasi: `file` wajib, `mimes:pdf`, `mimetypes:application/pdf`, `max:20480`; `title` opsional, `max:255` (dokumen 04 bagian 5.5).
- `x-field` dokumen 06 perlu menerima atribut `placeholder` dan `value`; atribut tambahan sudah diteruskan lewat `$attributes`.
- Komponen `button` perlu prop `href` yang merender `<a>` (lihat catatan di Wireframe 5).
- Nama class Tailwind ditulis utuh karena Tailwind memindai berkas Blade.
- Teks tombol file bawaan browser (`Choose File` atau `Pilih File`) mengikuti bahasa browser, bukan bahasa halaman. Bila ini mengganggu, ganti dengan pola label-sebagai-tombol plus JavaScript untuk menampilkan nama file.

---

## 10. Keputusan dan catatan

1. **Satu aksi utama.** `Tambahkan ke Koleksi`; `Batal` bervarian `secondary`.
2. **Judul opsional.** Bila kosong, server memakai nama file tanpa ekstensi (dokumen 04 bagian 5.5); placeholder menjelaskannya.
3. **Aturan ditampilkan sebelum user memilih file** (kotak aturan dan hint), bukan hanya sebagai pesan error.
4. **Tidak ada antrean offline.** Offline berarti upload dibatalkan dan user mencoba lagi (dokumen 01 UC-07, dokumen 02 bagian 7).
5. **Versi inti tanpa bar progres bernilai** (bagian 1.1 nomor 2). Bar adalah peningkatan opsional.
6. **Risiko 419 pada file besar** (bagian 6.4). Uji pada C18.
7. **Android (C16 dan C17).** Isi sama. Pemilih file memakai `ActivityResultContracts.OpenDocument` (`application/pdf`), menampilkan nama dan ukuran file, menolak file di atas 20 MB sebelum dikirim, memeriksa koneksi sebelum upload, dan memakai bar progres pil (06 bagian 7.8) karena Retrofit dapat melaporkan kemajuan. Tombol utama berupa pil lebar penuh; kotak aturan di atas form.
8. **Local Discovery** tidak muncul di halaman ini (P2, tidak dijadwalkan). Bila kelak tersedia, ajakan kedua dapat ditambahkan di Android saja.
9. **Dampak jadwal.** Penyesuaian tampilan Upload sudah dihitung dalam tambahan 3 sampai 4 jam dokumen 05 bagian 2. Bila C11 terdesak, korbankan JavaScript opsional (bagian 6.4) dan bar progres lebih dulu, bukan validasi dan pesan duplikat.

---

## 11. Daftar periksa (dokumen 06 bagian 14)

- [ ] Hanya memakai token; tanpa hex atau nilai arbitrary selain yang tercatat.
- [ ] Tanpa bayangan dan tanpa gradien.
- [ ] Satu blok merah besar (bilah atas); satu aksi utama (`primary`).
- [ ] Setiap kolom punya label terlihat, hint, dan pesan error di bawahnya.
- [ ] Error memakai warna `error`, ikon, dan getar; kesalahan halaman memakai kotak tint dan `role="alert"`.
- [ ] Tombol utama tidak bisa ditekan dua kali saat proses berjalan.
- [ ] Setiap elemen interaktif punya hover (Web), fokus, dan tekan.
- [ ] Gerak hanya `transform`, `opacity`, warna; ≤ 400 ms; aman dengan "kurangi gerakan".
- [ ] Desktop memakai dua kolom (form kiri, aturan kanan); mobile satu kolom dengan aturan di atas.
- [ ] Upload file kecil, bukan PDF, duplikat, di atas 20 MB, dan di atas 25 MB sudah dicoba di Browser.

---

## 12. Status wireframe C10

- [x] Login (revisi 3)
- [x] Register (revisi 3)
- [ ] Daftar Ebook (revisi 4, menunggu review)
- [ ] Upload (revisi 4, menunggu review)
- [ ] Empty State (revisi 5, menunggu review)
