# Wireframe 1 dari 4: Login (revisi 3)

Satu spesifikasi **isi** untuk Web dan Android. **Layout, warna, gerak, dan komponen mengikuti dokumen 06 (Design System)**; bila ada perbedaan, dokumen 06 yang berlaku. Wireframe ini menentukan elemen, teks, pesan, dan perilaku.

Perubahan dari revisi 2: layout dua kolom/pita merah (06 bagian 8.1), error dan sukses memakai komponen 06 (bagian 3.4 dan 7.8), dropdown info dan indikator Caps Lock dihapus (diparkir, lihat 05 bagian 8). Semua teks dari revisi 2 dipertahankan.

---

## 1. Layout

### 1.1 Web desktop (lebar 1024 ke atas): dua kolom

```text
┌───────────────────────────┬──────────────────────────────┐
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ │                              │
│ ▓ panel primary          ▓ │   Masuk ke Koleksi Anda      │
│ ▓                        ▓ │                              │
│ ▓ Ebook Storage          ▓ │   Email                      │
│ ▓ Satu koleksi PDF untuk ▓ │   ┌────────────────────────┐ │
│ ▓ Web dan Android.       ▓ │   │ Ketik email akun Anda  │ │
│ ▓                        ▓ │   └────────────────────────┘ │
│ ▓                        ▓ │                              │
│ ▓                        ▓ │   Password                   │
│ ▓                        ▓ │   ┌──────────────────┬─────┐ │
│ ▓                        ▓ │   │ Masukkan password│ 👁  │ │
│ ▓                        ▓ │   └──────────────────┴─────┘ │
│ ▓                        ▓ │                              │
│ ▓                        ▓ │   [ Masuk ke Koleksi Ebook ] │
│ ▓                        ▓ │                              │
│ ▓                        ▓ │   Belum punya akun?          │
│ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ │   Buat akun baru             │
└───────────────────────────┴──────────────────────────────┘
  kiri: bg primary, teks on-primary    kanan: putih, form ±360 px
```

### 1.2 Mobile (Web di bawah 1024 dan Android): pita merah di atas

```text
┌──────────────────────────┐
│▓▓▓ Ebook Storage ▓▓▓▓▓▓▓▓│  ← pita primary tipis
├──────────────────────────┤
│  Masuk ke Koleksi Anda   │
│                          │
│  Email                   │
│  ┌────────────────────┐  │
│  │ Ketik email akun   │  │
│  │ Anda               │  │
│  └────────────────────┘  │
│  Password                │
│  ┌──────────────┬─────┐  │
│  │ Masukkan     │ 👁  │  │
│  │ password Anda│     │  │
│  └──────────────┴─────┘  │
│  [ Masuk ke Koleksi Ebook]│
│  Belum punya akun?       │
│  Buat akun baru          │
└──────────────────────────┘
```

---

## 2. Keadaan dan pesan

Semua pesan memakai komponen dokumen 06; tidak ada hijau atau kuning.

| Keadaan                        | Tampilan                                                                                                                                     |
| ------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------- |
| Default                        | Seperti bagian 1                                                                                                                             |
| Password ditampilkan           | Ikon tombol berganti (👁 menjadi 🙈), teks terbaca. Otomatis tersembunyi lagi saat form dikirim                                              |
| Proses berjalan                | Tombol berganti indikator dengan label `Memeriksa akun…`, nonaktif (06 bagian 7.1)                                                           |
| Isian kosong                   | Pesan di bawah kolom: ikon peringatan + teks `error`; kolom bergaris `error` dan bergetar (06 bagian 7.2)                                    |
| Kredensial salah               | Kotak `primary-tint` di atas form: ikon peringatan + `Email atau password salah.` Email tetap terisi, password dikosongkan                   |
| Terlalu banyak percobaan (429) | Kotak `primary-tint`: `Terlalu banyak percobaan. Coba lagi sebentar lagi.` Tanpa hitungan detik karena kontrak API tidak mengirim sisa waktu |
| Setelah register berhasil      | Snackbar hitam bercentang di Login: `Akun berhasil dibuat. Silakan masuk.` (hilang otomatis 4 detik)                                         |
| Tanpa koneksi (Android)        | Kotak `primary-tint`: `Tidak ada koneksi. Coba lagi saat online.`                                                                            |

---

## 3. Daftar elemen

| Elemen                    | Aturan                                                                                                                             |
| ------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- |
| Label Email dan Password  | Selalu terlihat di atas kolom (06 bagian 7.2). Web: `<label for>` terhubung ke input                                               |
| Email                     | Placeholder `Ketik email akun Anda`. Web: `type="email"`, fokus otomatis saat halaman dibuka                                       |
| Password                  | Placeholder `Masukkan password Anda`. Tanpa hint apa pun                                                                           |
| Tombol tampilkan password | Web: tombol ikon di dalam kolom, `aria-label` berganti `Tampilkan password` / `Sembunyikan password`. Android: end icon bawaan     |
| Tombol utama              | `Masuk ke Koleksi Ebook`, varian `primary`, satu-satunya aksi utama. Mengirim `POST /login` (Web) atau `POST /api/login` (Android) |
| Tautan                    | `Buat akun baru` menuju Register, gaya tautan                                                                                      |
| Tidak ada                 | Lupa password, ingat saya, login sosial (di luar MVP)                                                                              |

---

## 4. Android

Isi sama persis dengan bagian 2 dan 3. Tidak ada wireframe terpisah.

| Hal                          | Android                                                                                               |
| ---------------------------- | ----------------------------------------------------------------------------------------------------- |
| Layout                       | Versi mobile (bagian 1.2): pita `primary`, form di bawah, status bar berwarna `primary`               |
| Kolom                        | `TextInputLayout` bergaya OutlinedBox (06 bagian 11.3)                                                |
| Tombol tampilkan password    | `app:endIconMode="password_toggle"`                                                                   |
| Sukses setelah register      | `Snackbar` hitam bercentang                                                                           |
| Error                        | Pesan di bawah kolom (`TextInputLayout.error`) dan kotak `primary-tint`; panggil `shake()` pada kolom |
| 401, 422, 429, tanpa koneksi | Dipetakan ke pesan pada bagian 2                                                                      |

---

## 5. Keputusan dan catatan

1. Email tetap terisi, password selalu dikosongkan saat login gagal.
2. Pesan kredensial hanya satu, sama dengan kontrak API (`invalid_credentials`).
3. Placeholder berupa ajakan; informasi penting tidak ditaruh di placeholder. Bila `Ketik email akun Anda` terpotong di layar sempit, pakai `Ketik email Anda`.
4. Diparkir (05 bagian 8): dropdown info di Login dan indikator Caps Lock. Bila dikerjakan kelak, pakai ikon `info` / ikon peringatan merah, bukan warna kuning.
5. Logika Login dari C01 tidak berubah; hanya tampilan dan satu JS kecil (toggle password).

---

## 6. Status wireframe C10

- [x] Login (revisi 3)
- [x] Register (revisi 3)
- [ ] Daftar Ebook (ditulis ulang menyesuaikan dokumen 06)
- [ ] Upload (ditulis ulang menyesuaikan dokumen 06)
- [ ] Empty State (ditulis ulang menyesuaikan dokumen 06)
