# Wireframe 5 dari 5: Empty State

Dokumen ini adalah wireframe tampilan **Empty State** (koleksi kosong) versi **Web** untuk proyek Ebook Storage & To-Do List. Tampilan ini muncul pada halaman Daftar Ebook (`GET /ebooks`) ketika user belum memiliki ebook (02-User-Flow bagian 15; 01-Use-Case UC-04: koleksi kosong). Wireframe ini dikerjakan sebagai bagian dari chunk C10 (rancangan empat layar: Login, Daftar Ebook, Upload, Empty State). Versi Android disesuaikan nanti pada chunk C14.

Wireframe Upload belum final dan akan direview pada C11. Di dokumen ini, tombol menuju Upload hanya digambar sebagai titik masuk.

---

## 1. Keadaan default (koleksi kosong)

```text
┌────────────────────────────────────────────────────────┐
│  Ebook Storage                    Noctura  [ Keluar ]  │  ← header sama dengan
├────────────────────────────────────────────────────────┤    Daftar Ebook
│                                                        │
│  Koleksi Ebook Saya                                    │
│  Belum ada ebook                                       │  ← pengganti "0 dari 0"
│                                                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │                                                  │  │
│  │                      📚                          │  │  ← dekoratif
│  │                                                  │  │
│  │          Koleksi Anda masih kosong               │  │
│  │                                                  │  │
│  │   Tambahkan PDF pertama Anda agar bisa dibuka    │  │
│  │   dan ditandai dari Web maupun Android.          │  │
│  │                                                  │  │
│  │        [ + Tambah PDF Pertama ke Koleksi ]       │  │  ← satu-satunya
│  │                                                  │  │    tombol utama
│  │        Hanya PDF, maksimal 20 MB.                │  │
│  │                                                  │  │
│  └──────────────────────────────────────────────────┘  │
│                                                        │
└────────────────────────────────────────────────────────┘
```

---

## 2. Keadaan lain

### 2.1 Setelah register dan login pertama

Sama dengan keadaan default. Tidak ada layar sambutan terpisah.

### 2.2 Koleksi gagal dimuat (bukan kosong)

Empty State **tidak boleh** tampil saat terjadi error, karena akan menyiratkan data user hilang. Pesan error memakai komponen yang sama dengan Daftar Ebook (UC-04: koleksi belum dapat ditampilkan).

```text
┌──────────────────────────────────────────┐
│ ✗ Koleksi belum dapat ditampilkan.       │
│   [ Muat Ulang Halaman ]                 │
└──────────────────────────────────────────┘
```

### 2.3 Layar sempit

```text
┌────────────────────────┐
│ Ebook Storage  [Keluar]│
├────────────────────────┤
│ Koleksi Ebook Saya     │
│ Belum ada ebook        │
│                        │
│          📚            │
│ Koleksi Anda masih     │
│ kosong                 │
│                        │
│ Tambahkan PDF pertama  │
│ Anda agar bisa dibuka  │
│ dan ditandai dari Web  │
│ maupun Android.        │
│                        │
│ [+ Tambah PDF Pertama] │
│ Hanya PDF, maks 20 MB. │
└────────────────────────┘
```

---

## 3. Keadaan pesan

**A. Setelah upload pertama berhasil** (pesan dari C11)

Halaman berganti menjadi Daftar Ebook dengan satu item dan banner sukses. Empty State tidak tampil lagi.

```text
┌──────────────────────────────────────────┐
│ ✓ "Kotlin Basics" ditambahkan ke         │
│   koleksi.                               │
└──────────────────────────────────────────┘
```

---

## 4. Daftar elemen

| Elemen                                          | Aturan                                                                                                                                                   |
| ----------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Header                                          | Sama dengan Daftar Ebook: nama user dan tombol `Keluar` (form `POST /logout`)                                                                            |
| Judul halaman                                   | `Koleksi Ebook Saya`                                                                                                                                     |
| Ringkasan                                       | `Belum ada ebook`. Teks `0 dari 0 ebook sudah dibaca` tidak dipakai karena terasa kaku. `fn_count_read_ebooks` tidak perlu dipanggil pada keadaan kosong |
| Ikon                                            | Emoji atau ikon dekoratif, `aria-hidden="true"`                                                                                                          |
| Judul Empty State                               | `Koleksi Anda masih kosong`                                                                                                                              |
| Penjelasan                                      | Satu kalimat yang menyebut manfaat: PDF dapat dibuka dan ditandai dari Web maupun Android                                                                |
| Tombol utama                                    | Label `+ Tambah PDF Pertama ke Koleksi`, menuju halaman Upload (`GET /ebooks/create` atau route yang ditetapkan pada C11). Label menyebut tujuan         |
| Catatan batas                                   | `Hanya PDF, maksimal 20 MB.` agar user tahu batasnya sebelum memilih file                                                                                |
| Tombol `+ Tambah PDF ke Koleksi` di atas daftar | **Tidak ditampilkan** saat kosong, supaya tidak ada dua tombol yang sama                                                                                 |
| Tombol hapus, Buka PDF, ubah status             | **Tidak ada**, karena belum ada item                                                                                                                     |

## 5. Teks

| Bagian     | Teks                                                                                |
| ---------- | ----------------------------------------------------------------------------------- |
| Ringkasan  | `Belum ada ebook`                                                                   |
| Judul      | `Koleksi Anda masih kosong`                                                         |
| Penjelasan | `Tambahkan PDF pertama Anda agar bisa dibuka dan ditandai dari Web maupun Android.` |
| Tombol     | `+ Tambah PDF Pertama ke Koleksi`                                                   |
| Catatan    | `Hanya PDF, maksimal 20 MB.`                                                        |

---

## 6. Keputusan dan catatan

1. **Satu ajakan, satu tombol.** Empty State hanya menawarkan satu aksi agar user langsung tahu langkah berikutnya.
2. **Tidak menjanjikan fitur yang belum ada.** Teks tidak menyebut discovery atau impor otomatis, karena Local Discovery adalah P2 dan tidak dijadwalkan.
3. **Empty State dan error dibedakan.** Koleksi kosong (data berhasil dimuat, jumlahnya nol) berbeda dari koleksi gagal dimuat. Hanya yang pertama memakai tampilan ini.
4. **Tombol menuju Upload.** Selama C11 belum dikerjakan, tombol dapat menaut ke `#` atau dinonaktifkan sementara. Bila route Upload sudah ada, tombol langsung menaut ke sana.
5. **Android (C14).** Empty State Android memakai teks, label, dan aturan yang sama. Karena Local Discovery belum ada di MVP, Android hanya menawarkan upload lewat pemilih file. Bila discovery kelak tersedia, ajakan kedua dapat ditambahkan di sini.
6. **Aksesibilitas.** Ikon dekoratif disembunyikan dari pembaca layar; judul dan penjelasan berupa teks biasa agar terbaca.

---

## 7. Status wireframe C10

- [x] Login (revisi 2)
- [x] Register
- [x] Daftar Ebook (revisi: tombol hapus "Segera hadir", tanggal standar)
- [x] Upload (draf, direview pada C11)
- [x] Empty State
