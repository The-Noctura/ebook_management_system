# Wireframe 2: Register (revisi 3)

Satu spesifikasi **isi** untuk Web dan Android. **Layout, warna, gerak, dan komponen mengikuti dokumen 06**; bila berbeda, dokumen 06 yang berlaku. Ketentuan Login (Wireframe 1) berlaku di sini: layout dua kolom/pita merah, komponen pesan, tombol tampilkan password.

Perubahan dari revisi 2: dropdown daftar centang dan pesan hijau/merah live pada konfirmasi dihapus (diparkir, 05 bagian 8). Sebagai gantinya, hint statis di bawah kolom Password. Semua teks lain dipertahankan.

---

## 1. Layout

Sama dengan Login (Wireframe 1 bagian 1): desktop dua kolom (panel `primary` kiri, form ±360 px kanan), mobile dan Android pita `primary` di atas.

Isi panel form:

```text
  Buat Akun Baru

  Nama
  ┌────────────────────────┐
  │ Ketik nama Anda        │
  └────────────────────────┘

  Email
  ┌────────────────────────┐
  │ Ketik email aktif Anda │
  └────────────────────────┘

  Password
  ┌──────────────────┬─────┐
  │ Buat password baru│ 👁 │
  └──────────────────┴─────┘
  Minimal 8 karakter            ← hint statis, caption, text-muted

  Konfirmasi Password
  ┌──────────────────┬─────┐
  │ Ketik ulang password│👁│
  └──────────────────┴─────┘

  [ Buat Akun Saya ]

  Sudah punya akun?
  Masuk di sini
```

---

## 2. Keadaan dan pesan

| Keadaan                 | Tampilan                                                                                                                                                                          |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Default                 | Seperti bagian 1                                                                                                                                                                  |
| Proses berjalan         | Tombol berganti indikator `Membuat akun…`, nonaktif                                                                                                                               |
| Validasi gagal (server) | Form kembali. Nama dan email tetap terisi; kedua kolom password dikosongkan. Pesan di bawah kolom yang salah: ikon peringatan + teks `error`, kolom bergaris `error` dan bergetar |
| Berhasil                | Tanpa layar khusus. Diarahkan ke Login dengan snackbar `Akun berhasil dibuat. Silakan masuk.` (02-User-Flow bagian 5.1)                                                           |
| Tanpa koneksi (Android) | Kotak `primary-tint`: `Tidak ada koneksi. Coba lagi saat online.`                                                                                                                 |

Contoh email sudah terdaftar:

```text
  Email
  ┌────────────────────────┐
  │ user@example.com       │   ← tetap terisi, garis error
  └────────────────────────┘
  ⚠ Email ini sudah terdaftar.
```

---

## 3. Daftar elemen dan aturan

| Elemen              | Placeholder                                    | Aturan validasi (server, 04-API-Specification bagian 5.1)    |
| ------------------- | ---------------------------------------------- | ------------------------------------------------------------ |
| Nama                | `Ketik nama Anda`                              | Wajib, maksimal 100 karakter                                 |
| Email               | `Ketik email aktif Anda`                       | Wajib, format email, maksimal 255, belum terdaftar           |
| Password            | `Buat password baru`                           | Wajib, minimal 8 karakter. Hint statis: `Minimal 8 karakter` |
| Konfirmasi Password | `Ketik ulang password Anda`                    | Wajib, sama dengan password                                  |
| Tombol              | Label `Buat Akun Saya`; proses `Membuat akun…` | Varian `primary`, satu-satunya aksi utama                    |
| Tautan              | `Masuk di sini`                                | Menuju Login                                                 |

Tombol tampilkan password ada di kedua kolom dan masing-masing independen; keduanya kembali tersembunyi saat form dikirim.

## 4. Pesan error (Bahasa Indonesia)

| Kondisi                      | Pesan                           |
| ---------------------------- | ------------------------------- |
| Nama kosong                  | Nama wajib diisi.               |
| Nama lebih dari 100 karakter | Nama maksimal 100 karakter.     |
| Email kosong                 | Email wajib diisi.              |
| Format email salah           | Format email tidak valid.       |
| Email sudah dipakai          | Email ini sudah terdaftar.      |
| Password kosong              | Password wajib diisi.           |
| Password kurang dari 8       | Password minimal 8 karakter.    |
| Konfirmasi tidak sama        | Konfirmasi password tidak sama. |

---

## 5. Android

Isi sama dengan bagian 2 sampai 4. Tidak ada wireframe terpisah.

| Hal            | Android                                                                                        |
| -------------- | ---------------------------------------------------------------------------------------------- |
| Layout         | Versi mobile: pita `primary`, form di bawah                                                    |
| Kolom          | `TextInputLayout` OutlinedBox; password memakai `endIconMode="password_toggle"` di kedua kolom |
| Hint           | `helperText` = `Minimal 8 karakter` pada kolom Password                                        |
| Error 422      | Dipetakan per field ke `TextInputLayout.error`, lalu `shake()` pada kolom                      |
| Berhasil (201) | Kembali ke Login dengan Snackbar sukses (tanpa token, 04 bagian 5.1)                           |

---

## 6. Keputusan dan catatan

1. Hint statis `Minimal 8 karakter` menggantikan daftar centang live. Hanya satu aturan yang ditegakkan server, jadi tidak ada yang dijanjikan berlebihan.
2. Kesamaan password dan konfirmasi hanya diperiksa server; pesan tampil setelah submit.
3. Diparkir (05 bagian 8): daftar centang live, pesan live pada konfirmasi, indikator Caps Lock. Bila dikerjakan kelak, hijau diganti ikon centang hitam dan kuning diganti ikon peringatan merah (06 bagian 3 dan 13).
4. Jika JavaScript mati, form tetap berfungsi; hanya tombol tampilkan password yang tidak ada.
