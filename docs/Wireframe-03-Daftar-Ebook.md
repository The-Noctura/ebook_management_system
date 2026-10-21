# Wireframe 3 dari 5: Daftar Ebook (revisi 4)

Dokumen ini adalah wireframe halaman **Daftar Ebook** versi **Web** (`GET /ebooks`) untuk proyek Ebook Storage & To-Do List. Halaman ini adalah pusat aktivitas setelah login (02-User-Flow bagian 6) dan mencakup UC-04 (lihat koleksi), UC-08 (buka PDF), UC-09 dan UC-10 (ubah status baca). Dikerjakan pada chunk C10. Versi Android disesuaikan pada chunk C14.

**Aturan acuan.** Warna, tipografi, radius, jarak, gerak, dan komponen mengikuti **06-Design-System versi 1.2**. Wireframe ini **memadukan** desainnya sendiri dengan dokumen 06: bagian yang tidak bertentangan mengikuti dokumen 06, dan perluasan yang dibuat wireframe tercatat di bagian 1. Bila ada perbedaan lain yang tidak tercatat, dokumen 06 yang berlaku. Dokumen 06 tidak diubah.

> **Catatan sumber.** Berkas wireframe Daftar Ebook revisi 1 tidak tersedia saat revisi ini dibuat. Isi disusun dari dokumen 01, 02, 04, 06, wireframe Empty State, dan catatan status C10 ("tombol hapus Segera hadir, tanggal standar"). Bandingkan dengan berkas lama sebelum menimpanya.

---

## 1. Perpaduan dengan dokumen 06

### 1.1 Perluasan dari wireframe (di luar tulisan dokumen 06)

| #   | Dokumen 06                                                 | Wireframe (dipadukan)                                                                                                                                  | Cara dipadukan                                                                                                                                                                                                                                                                             |
| --- | ---------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| 1   | 10.4: chip memakai `aria-pressed`, teks chip ikut berganti | Chip tetap berupa komponen `chip` dengan prop `pressed`, tetapi **nama aksesibelnya** dibuat lewat `aria-label` (`Belum dibaca. Tandai sudah dibaca.`) | Tampilan, ukuran, ikon, teks, dan varian chip seluruhnya dari 06 (7.4). Hanya mekanisme penanda status yang berbeda: gaya "Sudah Dibaca" dipicu `data-pressed`, bukan `aria-pressed`, karena `aria-pressed` ditambah label yang berganti membuat pembaca layar mengucapkan status dua kali |
| 2   | 7.3: kartu berisi judul, tanggal, chip                     | Kartu juga memuat **tombol hapus nonaktif**                                                                                                            | Tombol ikon bergaya 06 (6.3), keadaan nonaktif mengikuti 7.1 (opasitas 0,38, kursor `not-allowed`). Tidak mengirim request apa pun. Opsi: dibuang dari MVP (bagian 5)                                                                                                                      |

### 1.2 Perluasan pola 06 ke hal yang belum diatur

| Hal                              | Keputusan                                                                                                                                                                                                            |
| -------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Bilah atas Web di bawah 1024 px  | Memakai **pola Android dokumen 06 bagian 7.6**: nama aplikasi dengan ringkasan `body-sm` di bawahnya di dalam bilah; `Tambah PDF` dan ikon logout di kanan. Tinggi tetap 64 (06 bagian 5.2). Nama user disembunyikan |
| Seluruh kartu membuka PDF        | Lewat satu tautan di judul (stretched link). Chip dan tombol hapus berada di atas tautan, sehingga tidak ada tombol di dalam tautan                                                                                  |
| PDF dibuka di tab baru           | `target="_blank" rel="noopener"` agar posisi di daftar tidak hilang                                                                                                                                                  |
| Posisi gulir setelah ubah status | Redirect membawa fragmen `#ebook-{id}`                                                                                                                                                                               |
| Pesan setelah ubah status        | Snackbar `ink` (sukses) atau kotak error `primary-tint` (gagal)                                                                                                                                                      |
| Tablet                           | 2 kolom, aturan 06 bagian 8.1                                                                                                                                                                                        |

---

## 2. Keadaan default (ada ebook), desktop ≥ 1024 px

Bilah atas selebar layar; isi bilah atas dan konten dibatasi wadah yang sama (maks 1200).

```text
┌──────────────────────────────────────────────────────────────────────────┐
│█████████████████████████████ BILAH ATAS MERAH ███████████████████████████│ tinggi 56
│█ Ebook Storage     5 dari 12 sudah dibaca  [＋ Tambah PDF]  Noctura  [⎋] █│
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │ latar putih
│  Koleksi Ebook Saya                                                      │ title-lg
│                                                                          │
│  ┌─────────────────────┐ ┌─────────────────────┐ ┌─────────────────────┐ │
│  │ Clean Code          │ │ Atomic Habits       │ │ Kotlin Basics       │ │ kartu:
│  │ 3 Okt 2026          │ │ 2 Okt 2026          │ │ 1 Okt 2026          │ │ surface,
│  │                     │ │                     │ │                     │ │ outline 1px,
│  │ ( ○ Belum Dibaca )🗑│ │ ( ✓ Sudah Dibaca )🗑│ │ ( ✓ Sudah Dibaca )🗑│ │ radius-lg,
│  └─────────────────────┘ └─────────────────────┘ └─────────────────────┘ │ padding 12
│  ┌─────────────────────┐ ┌─────────────────────┐                         │
│  │ Designing Data-     │ │ …                   │        grid otomatis,   │
│  │ Intensive Applica…  │ │                     │        kolom min 320,   │
│  │ 28 Sep 2026         │ │                     │        jarak 12         │
│  │ ( ○ Belum Dibaca )🗑│ │                     │                         │
│  └─────────────────────┘ └─────────────────────┘                         │
└──────────────────────────────────────────────────────────────────────────┘
```

Keterangan:

- `[＋ Tambah PDF]` adalah **satu-satunya aksi utama** (varian `inverse`, putih di atas bilah merah).
- `🗑` adalah tombol hapus **nonaktif** (bagian 5).
- Chip hitam (`ink`) untuk "Sudah Dibaca", garis abu untuk "Belum Dibaca". Status dibedakan oleh isi, ikon, dan teks sekaligus, tidak hanya warna.
- Urutan kartu: `created_at` menurun (terbaru di kiri atas), sama dengan `GET /api/ebooks`.
- Komposisi: putih ±60%, merah ±30% (bilah atas, hover), hitam ±10% (teks, chip). Hanya satu blok merah pekat besar.

### 2.1 Anatomi kartu

```text
┌────────────────────────────────────────────┐
│ Clean Code                                 │ ← title-md, maks 2 baris + elipsis
│ 3 Okt 2026                                 │ ← body-sm, text-muted
│                                            │
│ ( ○ Belum Dibaca )                    [🗑] │ ← chip kiri, hapus nonaktif kanan
└────────────────────────────────────────────┘
   radius-lg · surface · outline 1px · padding 12 (desktop) / 16 (mobile)
```

Aturan interaksi kartu:

1. **Judul adalah tautan** ke `GET /ebooks/{id}/file` (UC-08), diperluas ke seluruh kartu (stretched link).
2. **Chip dan tombol hapus berada di atas tautan** (`relative z-10`), sehingga menekannya tidak membuka PDF.
3. Tidak ada tombol di dalam tautan.
4. Urutan fokus keyboard: judul, chip, hapus.

---

## 3. Tablet dan mobile

### 3.1 Tablet (600 sampai 1023 px): 2 kolom

Bilah atas tinggi 64 dengan pola mobile di bawah (ringkasan di bawah nama aplikasi, di dalam bilah). `Tambah PDF` menampilkan label lengkap. Padding halaman 24, tombol dan chip tinggi 48.

### 3.2 Mobile (< 600 px): 1 kolom

```text
┌──────────────────────────┐
│██████ BILAH ATAS ████████│ tinggi 64
│█ Ebook Storage [＋PDF][⎋]│ title-md, putih
│█ 5 dari 12 sudah dibaca  │ body-sm, di dalam bilah
├──────────────────────────┤
│ Koleksi Ebook Saya       │
│                          │
│ ┌──────────────────────┐ │
│ │ Clean Code           │ │ padding 16
│ │ 3 Okt 2026           │ │
│ │                      │ │
│ │ ( ○ Belum Dibaca ) 🗑│ │ chip dan hapus tinggi 48
│ └──────────────────────┘ │ jarak antar kartu 12
│ ┌──────────────────────┐ │
│ │ Atomic Habits        │ │
│ │ 2 Okt 2026           │ │
│ │ ( ✓ Sudah Dibaca ) 🗑│ │
│ └──────────────────────┘ │
└──────────────────────────┘
```

Bilah atas: sisi kiri berisi dua baris (nama aplikasi, lalu ringkasan); sisi kanan berisi `Tambah PDF` dan ikon logout. Pada lebar di bawah sekitar 420 px, `Tambah PDF` hanya menampilkan ikon plus (tetap berbentuk pil, tinggi 48) dengan `aria-label="Tambah PDF"`, agar semuanya muat dalam tinggi 64. Chip dan tombol hapus berjarak minimal 8.

---

## 4. Keadaan interaksi

Mengikuti dokumen 06 bagian 6.3. Perubahan hover memakai `dur-fast` atau `dur-base` dengan `ease-standard`.

| Elemen                  | Hover (Web)                                         | Fokus keyboard                            | Tekan      |
| ----------------------- | --------------------------------------------------- | ----------------------------------------- | ---------- |
| Kartu                   | Garis `primary`, naik 2px, judul berwarna `primary` | Cincin 2px `primary` (lewat tautan judul) | Skala 0,98 |
| Chip "Belum Dibaca"     | Isi `primary-tint`, garis `primary`                 | Cincin                                    | Skala 0,97 |
| Chip "Sudah Dibaca"     | Isi `ink-hover`                                     | Cincin                                    | Skala 0,97 |
| Tombol `＋ Tambah PDF`  | Isi `primary-tint`, ikon plus berputar 90°          | Cincin **putih**                          | Skala 0,97 |
| Tombol hapus (nonaktif) | Tidak ada                                           | Dapat difokus, tanpa efek                 | Tidak ada  |
| Tombol logout           | Lingkaran putih 15%                                 | Cincin putih                              | Skala 0,95 |

### 4.1 Gerak

| Situasi                | Gerak                                                                   | Durasi                 |
| ---------------------- | ----------------------------------------------------------------------- | ---------------------- |
| Daftar muncul          | Kartu fade + naik 8px; jeda antar kartu 30 ms, maksimal 6 kartu pertama | `dur-slow`, `ease-out` |
| Status berubah         | Warna chip bertransisi; ikon centang fade + skala 0,8 ke 1              | `dur-base`             |
| Ebook baru ditambahkan | Latar kartu `primary-tint` lalu memudar ke `surface`                    | `dur-slow`             |

Gerak hanya `transform`, `opacity`, dan warna. Dengan "kurangi gerakan" aktif, gerak naik dan putar dihapus, perubahan warna tetap ada.

---

## 5. Ubah status baca dan tombol hapus

### 5.1 Ubah status (UC-09 dan UC-10)

Chip adalah tombol di dalam form `PATCH /ebooks/{id}` dengan nilai `is_read` kebalikan dari status sekarang.

```text
Klik chip
   │
   ▼
Chip nonaktif sementara (cegah klik ganda)  ← dengan JS; tanpa JS form tetap berfungsi
   │
   ▼
PATCH /ebooks/{id}  → EbookService::setReadStatus (procedure + trigger + transaksi)
   │
   ├── Berhasil ──► redirect ke /ebooks#ebook-{id}
   │                 • halaman kembali ke posisi kartu itu
   │                 • ringkasan diperbarui
   │                 • snackbar: ✓ Status "Clean Code" diubah menjadi Sudah Dibaca.
   │
   └── Gagal ─────► redirect kembali, status lama dipertahankan (UC-09/UC-10)
                     • kotak error di atas daftar (role="alert")
```

Keputusan penting:

- **Posisi gulir dipertahankan.** Form biasa menyebabkan muat ulang penuh; tanpa fragmen, user yang mengubah kartu ke-30 terlempar ke atas daftar. Tiap kartu memiliki `id="ebook-{id}"`.
- **Status tidak hanya lewat warna.** Chip selalu berisi ikon dan teks (06 bagian 7.4).
- **Idempotent.** Menekan chip dua kali dengan nilai sama tidak menambah riwayat (dokumen 04 bagian 5.7).

Komponen `chip` yang dipadukan (visual sama dengan 06 bagian 10.4; hanya pemicu status dan label aksesibel yang berbeda):

```blade
@props(['pressed' => false])
<button type="submit" @if($pressed) data-pressed @endif {{ $attributes->class([
    'inline-flex min-h-12 cursor-pointer items-center gap-2 rounded-full border border-outline-strong bg-transparent px-4 text-label font-medium text-text lg:min-h-9',
    'transition duration-200 ease-standard active:scale-[0.97] motion-reduce:active:scale-100',
    'not-data-[pressed]:hover:border-primary not-data-[pressed]:hover:bg-primary-tint',
    'data-[pressed]:border-ink data-[pressed]:bg-ink data-[pressed]:text-on-ink',
    'data-[pressed]:hover:border-ink-hover data-[pressed]:hover:bg-ink-hover',
]) }}>{{ $slot }}</button>
```

### 5.2 Tombol hapus (belum tersedia)

Hapus ebook di luar MVP (dokumen 04 bagian 10), tetapi C02 mencatat fitur ini dipertimbangkan masuk lebih cepat. Revisi 1 menampilkannya sebagai "Segera hadir"; wireframe ini mempertahankannya.

```text
[🗑]   ← tombol ikon 40 (mobile 48), opasitas 0,38, kursor not-allowed
```

- Memakai `aria-disabled="true"` (bukan atribut `disabled`) agar tetap bisa difokus dan dibacakan, dengan `aria-label="Hapus ebook, segera hadir"`.
- **Tidak mengandalkan tooltip hover**, karena tidak terjangkau keyboard dan layar sentuh.
- Tidak mengirim request apa pun.
- Saat fitur dibuat nanti: wajib ada **dialog konfirmasi** (06 bagian 7.8: dua tombol, `text` untuk batal dan `primary` untuk lanjut), karena menghapus ebook juga menghapus file di R2 dan tidak dapat dibatalkan.
- **Opsi:** dibuang dari MVP dan ditambahkan saat fitur siap. Tombol nonaktif menambah satu target di setiap kartu dan menjanjikan fitur yang belum dijadwalkan. Bila dibuang, hapus baris deviasi nomor 2 di bagian 1.1.

---

## 6. Keadaan lain

### 6.1 Koleksi kosong

Lihat **Wireframe 5: Empty State**. Bilah atas dan judul halaman sama; ringkasan menjadi "Belum ada ebook" dan tombol `Tambah PDF` di bilah atas disembunyikan.

### 6.2 Koleksi gagal dimuat

Bukan Empty State. Kotak error `primary-tint` dengan ikon peringatan, warna `error`, `role="alert"`, dan tombol `secondary` "Muat Ulang Halaman". Ringkasan dikosongkan.

### 6.3 Gagal mengubah status

```text
┌──────────────────────────────────────────────────────────────┐
│ ⚠  Status "Clean Code" belum bisa diperbarui. Silakan coba   │ ← primary-tint, role="alert"
│    lagi.                                                     │
└──────────────────────────────────────────────────────────────┘
```

Kotak muncul di atas daftar. Chip pada kartu yang bersangkutan tetap menampilkan status lama.

### 6.4 PDF tidak tersedia (`file_missing`)

Bila recordnya ada tetapi file di R2 tidak ditemukan (dokumen 04 bagian 5.6, UC-08), tampilkan halaman atau kotak error `File PDF tidak tersedia.` dengan tautan kembali ke koleksi. Ebook milik user lain menghasilkan 404 yang sama dengan ebook tidak ada.

### 6.5 Setelah upload berhasil

Redirect ke `/ebooks?baru={id}`. Kartu baru memakai `data-new` (sorotan `primary-tint` yang memudar) dan snackbar hitam `✓ "Kotlin Basics" ditambahkan ke koleksi.` hilang otomatis dalam 4 detik.

### 6.6 Memuat

Halaman dirender di server, sehingga tidak ada keadaan memuat daftar dan tidak ada kedipan Empty State.

---

## 7. Daftar elemen dan token

| Elemen            | Aturan                                                                                                                                                                                          | Token / class                                                                                   |
| ----------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------- |
| Bilah atas        | Desktop: nama aplikasi di kiri; ringkasan, Tambah PDF, nama user, ikon logout di kanan. Di bawah 1024 px: nama aplikasi dengan ringkasan di bawahnya (kiri); Tambah PDF dan ikon logout (kanan) | `topbar bg-primary text-on-primary`, `min-h-16 lg:min-h-14`                                     |
| Ringkasan         | `{read_count} dari {total} sudah dibaca`, dari `fn_count_read_ebooks` dan jumlah koleksi                                                                                                        | `text-body-sm`                                                                                  |
| Tombol Tambah PDF | Varian `inverse`, ikon plus berputar saat hover; di bawah ±420 px hanya ikon dengan `aria-label`                                                                                                | `<x-button variant="inverse">`                                                                  |
| Tombol logout     | Tombol ikon, `aria-label="Keluar"`, membungkus form `POST /logout`                                                                                                                              | `size-10 hover:bg-white/15`                                                                     |
| Judul halaman     | `Koleksi Ebook Saya`, `h1`                                                                                                                                                                      | `text-title-lg font-semibold`                                                                   |
| Grid              | 1, 2, lalu auto-fill kolom min 320                                                                                                                                                              | `grid grid-cols-1 gap-3 py-4 sm:grid-cols-2 lg:grid-cols-[repeat(auto-fill,minmax(320px,1fr))]` |
| Kartu             | Stretched link ke PDF                                                                                                                                                                           | `<x-card>`, `relative`                                                                          |
| Judul ebook       | Maks 2 baris, elipsis                                                                                                                                                                           | `line-clamp-2 text-title-md font-semibold`                                                      |
| Tanggal           | Format `3 Okt 2026` dari `created_at`                                                                                                                                                           | `text-body-sm text-text-muted`                                                                  |
| Chip status       | Form `PATCH`, ikon + teks, `aria-label` aksi                                                                                                                                                    | `<x-chip :pressed="...">`                                                                       |
| Tombol hapus      | Nonaktif (bagian 5.2)                                                                                                                                                                           | `aria-disabled`, `opacity-[0.38]`                                                               |

---

## 8. Contoh implementasi (Blade + Tailwind)

Acuan untuk C10, bukan kode final. Memakai komponen dokumen 06 bagian 10.4 (chip: bagian 5.1).

Bilah atas (ringkasan muncul di kiri di bawah 1024 px dan di kanan pada desktop; elemen yang disembunyikan tidak dibacakan pembaca layar):

```blade
<header class="topbar bg-primary text-on-primary">
    <div class="mx-auto flex min-h-16 w-full max-w-[1200px] items-center gap-3 px-4 sm:px-6 lg:min-h-14">
        <div class="grid min-w-0 flex-1 leading-tight">
            <span class="text-title-md font-semibold">Ebook Storage</span>
            <span class="text-body-sm lg:hidden">{{ $readCount }} dari {{ $ebooks->count() }} sudah dibaca</span>
        </div>

        <span class="hidden text-body-sm lg:block">{{ $readCount }} dari {{ $ebooks->count() }} sudah dibaca</span>

        <x-button variant="inverse" href="{{ route('ebooks.create') }}" aria-label="Tambah PDF">
            <x-icon.plus class="size-6 transition-transform duration-200 ease-standard group-hover:rotate-90 motion-reduce:transform-none" aria-hidden="true" />
            <span class="hidden min-[420px]:inline">Tambah PDF</span>
        </x-button>

        <span class="hidden text-body-sm lg:block">{{ auth()->user()->name }}</span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" aria-label="Keluar"
                class="inline-grid size-10 place-items-center rounded-full transition duration-100 ease-standard hover:bg-white/15 active:scale-95 motion-reduce:active:scale-100">
                <x-icon.logout class="size-6" aria-hidden="true" />
            </button>
        </form>
    </div>
</header>
```

Daftar:

```blade
<section class="grid grid-cols-1 gap-3 py-4 sm:grid-cols-2 lg:grid-cols-[repeat(auto-fill,minmax(320px,1fr))]">
    @foreach ($ebooks as $ebook)
        <x-card id="ebook-{{ $ebook->id }}"
                :new="request('baru') == $ebook->id"
                class="relative animate-enter"
                style="animation-delay: {{ min($loop->index, 5) * 30 }}ms">

            <h2 class="text-title-md font-semibold">
                <a href="{{ route('ebooks.file', $ebook) }}" target="_blank" rel="noopener"
                   class="line-clamp-2 transition-colors duration-100 after:absolute after:inset-0 after:content-[''] group-hover:text-primary">
                    {{ $ebook->title }}
                </a>
            </h2>

            <p class="text-body-sm text-text-muted">
                {{ $ebook->created_at->translatedFormat('j M Y') }}
            </p>

            <div class="relative z-10 flex items-center justify-between gap-2">
                <form method="POST" action="{{ route('ebooks.update', $ebook) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="is_read" value="{{ $ebook->is_read ? 0 : 1 }}">
                    <x-chip :pressed="$ebook->is_read"
                            aria-label="{{ $ebook->is_read
                                ? 'Sudah dibaca. Tandai belum dibaca.'
                                : 'Belum dibaca. Tandai sudah dibaca.' }}">
                        @if ($ebook->is_read)
                            <x-icon.check class="size-4" aria-hidden="true" /> Sudah Dibaca
                        @else
                            <x-icon.circle class="size-4" aria-hidden="true" /> Belum Dibaca
                        @endif
                    </x-chip>
                </form>

                <button type="button" aria-disabled="true" aria-label="Hapus ebook, segera hadir"
                        class="inline-grid size-12 cursor-not-allowed place-items-center rounded-full opacity-[0.38] lg:size-10">
                    <x-icon.trash class="size-6" aria-hidden="true" />
                </button>
            </div>
        </x-card>
    @endforeach
</section>
```

Catatan implementasi:

- Komponen `card` dokumen 06 sudah memuat `group` dan `cursor-pointer`; `relative` ditambahkan untuk stretched link.
- Nama class Tailwind ditulis utuh karena Tailwind memindai berkas Blade.
- Format tanggal bergantung pada zona waktu aplikasi. Database memakai UTC (dokumen 03 bagian 7.1); set `APP_TIMEZONE` agar tanggal tampil sesuai zona user, lalu verifikasi di Browser.
- Tidak ada pagination, filter, atau pengurutan pada MVP (dokumen 04 bagian 10).

---

## 9. Teks

| Bagian            | Teks                                                                        |
| ----------------- | --------------------------------------------------------------------------- |
| Judul halaman     | `Koleksi Ebook Saya`                                                        |
| Ringkasan         | `5 dari 12 sudah dibaca`                                                    |
| Tombol            | `Tambah PDF`                                                                |
| Chip              | `Belum Dibaca` / `Sudah Dibaca`                                             |
| Aria-label chip   | `Belum dibaca. Tandai sudah dibaca.` / `Sudah dibaca. Tandai belum dibaca.` |
| Snackbar status   | `Status "{judul}" diubah menjadi Sudah Dibaca.` / `... Belum Dibaca.`       |
| Error status      | `Status "{judul}" belum bisa diperbarui. Silakan coba lagi.`                |
| Error muat        | `Koleksi belum dapat ditampilkan.` + tombol `Muat Ulang Halaman`            |
| File hilang       | `File PDF tidak tersedia.`                                                  |
| Aria-label hapus  | `Hapus ebook, segera hadir`                                                 |
| Aria-label logout | `Keluar`                                                                    |

---

## 10. Keputusan dan catatan

1. **Satu aksi utama.** `Tambah PDF` di bilah atas; chip dan hapus adalah aksi per kartu, bukan aksi halaman.
2. **Seluruh kartu membuka PDF lewat satu tautan.** Menghindari tombol di dalam tautan dan menjaga urutan fokus tetap logis (judul, chip, hapus).
3. **Pertahankan posisi gulir** lewat fragmen `#ebook-{id}` setelah ubah status.
4. **Chip memakai `data-pressed`.** Tampilan identik dengan dokumen 06; hanya pemicunya yang berbeda (bagian 1.1).
5. **Tombol hapus nonaktif** dipertahankan dari revisi 1, dengan opsi dibuang (bagian 5.2).
6. **Android (C14).** Daftar satu kolom, bilah atas merah dengan ringkasan di bawah judul (pola yang sama), tombol `Tambah PDF` mengambang di kanan bawah (06 bagian 7.5), perubahan status memakai respons API langsung tanpa muat ulang. Hapus tidak dibawa ke Android sampai fitur tersedia.
7. **Local Discovery** tidak muncul di halaman ini (P2, tidak dijadwalkan).
8. **Dampak jadwal.** Posisi gulir dan snackbar menambah sedikit pekerjaan di C10. Bila Gate terdesak, korbankan sorotan ebook baru dan stagger dulu, bukan fungsi ubah status.

---

## 11. Daftar periksa (dokumen 06 bagian 14)

- [ ] Hanya memakai token; tanpa hex atau nilai arbitrary.
- [ ] Tanpa bayangan dan tanpa gradien.
- [ ] Satu blok merah besar (bilah atas); satu aksi utama.
- [ ] Status baca tidak hanya lewat warna (ikon + teks + isi).
- [ ] Setiap elemen interaktif punya hover (Web), fokus, dan tekan.
- [ ] Tidak ada elemen interaktif bersarang; urutan fokus: judul, chip, hapus.
- [ ] Target sentuh ≥ 48 di mobile, jarak ≥ 8.
- [ ] Error memakai warna `error`, ikon, kotak tint, dan `role="alert"`.
- [ ] Gerak hanya `transform`, `opacity`, warna; ≤ 400 ms; aman dengan "kurangi gerakan".
- [ ] Desktop memakai grid dan jarak desktop, bukan tata letak mobile yang diperlebar.
- [ ] Bilah atas mobile muat satu baris 64 pada lebar 360 px (cek tombol ikon-saja).

---

## 12. Status wireframe C10

- [x] Login (revisi 3)
- [x] Register (revisi 3)
- [x] Daftar Ebook (revisi 4)
- [x] Upload (revisi 4)
- [x] Empty State (revisi 5)
