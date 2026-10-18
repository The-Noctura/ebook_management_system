# Wireframe 2: Register

Dokumen ini adalah wireframe halaman Register versi **Web** untuk proyek Ebook Storage & To-Do List. Halaman Register sudah dibuat pada chunk C01 (setup, register, login, logout Web); wireframe ini mendokumentasikan dan merapikannya sebagai bagian dari chunk C10 (rancangan layar Web). Versi Android disesuaikan nanti pada chunk C13 (login dan register Android).

Ketentuan Login (Wireframe 1) berlaku di sini: label tetap di atas kolom, placeholder berupa ajakan, tombol tampilkan password, dropdown syarat password dengan animasi, indikator Caps Lock, dan label tombol yang menyebut tujuan. Perbedaannya, dropdown di Register berupa **daftar centang yang berubah live**.

---

## 1. Keadaan default

```text
┌──────────────────────────────────────────────┐
│  Ebook Storage                               │
├──────────────────────────────────────────────┤
│                                              │
│          ┌──────────────────────────┐        │
│          │  Buat Akun Baru          │        │
│          │                          │        │
│          │  Nama                    │        │
│          │  ┌────────────────────┐  │        │
│          │  │ Ketik nama Anda    │  │        │
│          │  └────────────────────┘  │        │
│          │                          │        │
│          │  Email                   │        │
│          │  ┌────────────────────┐  │        │
│          │  │ Ketik email aktif  │  │        │
│          │  │ Anda               │  │        │
│          │  └────────────────────┘  │        │
│          │                          │        │
│          │  Password                │        │
│          │  ┌──────────────┬─────┐  │        │
│          │  │ Buat password│ 👁  │  │        │
│          │  │ baru         │     │  │        │
│          │  └──────────────┴─────┘  │        │
│          │                          │        │
│          │  Konfirmasi Password     │        │
│          │  ┌──────────────┬─────┐  │        │
│          │  │ Ketik ulang  │ 👁  │  │        │
│          │  │ password Anda│     │  │        │
│          │  └──────────────┴─────┘  │        │
│          │                          │        │
│          │  [ Buat Akun Saya      ] │        │
│          │                          │        │
│          │  Sudah punya akun?       │        │
│          │  Masuk di sini           │        │
│          └──────────────────────────┘        │
└──────────────────────────────────────────────┘
```

---

## 2. Keadaan interaksi

### 2.1 Kolom password difokuskan (dropdown centang)

```text
  Password
  ┌──────────────────────┬─────┐
  │ rahasia              │ 👁  │
  └──────────────────────┴─────┘
  ┌──────────────────────────────┐   ← slide-down + fade (~200 ms)
  │ Password harus:              │
  │ ✓ Minimal 8 karakter         │   ← hijau saat terpenuhi
  └──────────────────────────────┘

  Sebelum terpenuhi:
  │ ○ Minimal 8 karakter         │   ← abu-abu
```

### 2.2 Kolom konfirmasi password

Konfirmasi password tidak masuk dropdown. Ia punya pesan sendiri di bawah kolomnya:

```text
  Konfirmasi Password
  ┌──────────────────────┬─────┐
  │ rahasia1             │ 👁  │
  └──────────────────────┴─────┘
  ✗ Password belum sama         ← merah, muncul saat mengetik

  ┌──────────────────────┬─────┐
  │ rahasia123           │ 👁  │
  └──────────────────────┴─────┘
  ✓ Password sama               ← hijau
```

### 2.3 Caps Lock

Sama seperti Login. Peringatan `⚠ Caps Lock aktif` muncul di bawah kolom password **dan** kolom konfirmasi saat salah satunya difokuskan, di atas dropdown bila muncul bersamaan.

### 2.4 Tombol saat proses berjalan

```text
  [ Membuat akun… ]   ← nonaktif, mencegah klik ganda
```

---

## 3. Keadaan pesan

**A. Validasi gagal dari server** (form kembali, isian tetap, password dikosongkan)

```text
  Nama
  ┌────────────────────┐
  │ Noctura            │   ← tetap terisi
  └────────────────────┘

  Email
  ┌────────────────────┐
  │ user@example.com   │   ← tetap terisi
  └────────────────────┘
  Email ini sudah terdaftar.    ← merah

  Password
  ┌──────────────┬─────┐
  │              │ 👁  │   ← dikosongkan
  └──────────────┴─────┘
```

**B. Berhasil:** tidak ada layar khusus. User diarahkan ke Login dengan banner `✓ Akun berhasil dibuat. Silakan masuk.` (02-User-Flow bagian 5.1).

---

## 4. Daftar elemen dan aturan

| Elemen | Placeholder | Aturan validasi (server, 04-API-Specification bagian 5.1) |
|---|---|---|
| Nama | `Ketik nama Anda` | Wajib, maksimal 100 karakter |
| Email | `Ketik email aktif Anda` | Wajib, format email, maksimal 255, belum terdaftar |
| Password | `Buat password baru` | Wajib, minimal 8 karakter |
| Konfirmasi Password | `Ketik ulang password Anda` | Wajib, sama dengan password |
| Tombol | Label: `Buat Akun Saya`; proses: `Membuat akun…` | Mengirim `POST /register` |
| Tautan | `Masuk di sini` | Menuju `GET /login` |

## 5. Pesan error (Bahasa Indonesia)

| Kondisi | Pesan |
|---|---|
| Nama kosong | Nama wajib diisi. |
| Nama lebih dari 100 karakter | Nama maksimal 100 karakter. |
| Email kosong | Email wajib diisi. |
| Format email salah | Format email tidak valid. |
| Email sudah dipakai | Email ini sudah terdaftar. |
| Password kosong | Password wajib diisi. |
| Password kurang dari 8 | Password minimal 8 karakter. |
| Konfirmasi tidak sama | Konfirmasi password tidak sama. |

---

## 6. Keputusan dan catatan

1. **Satu syarat saja di dropdown** ("Minimal 8 karakter"), karena hanya itu yang ditegakkan server. Kesamaan dengan konfirmasi ditampilkan di kolom konfirmasi, jadi dropdown tidak menjanjikan aturan yang tidak ada.
2. **Dropdown hanya untuk kolom Password.** Kolom konfirmasi memakai pesan langsung di bawahnya, supaya dua pop-up tidak saling menumpuk.
3. **Pengecekan sisi klien hanya bantuan.** Validasi server tetap yang menentukan. Jika JavaScript mati, form tetap berfungsi dan error dari server tampil seperti biasa.
4. **Tombol tampilkan password dipasang di kedua kolom**, dan masing-masing independen. Keduanya otomatis kembali tersembunyi saat form dikirim.
5. **Komponen dipakai ulang.** Dropdown, tombol tampilkan, dan indikator Caps Lock cukup ditulis sekali dalam satu file JavaScript dan dipakai di Login dan Register, dengan mode info (Login) dan mode centang (Register). Ini menghemat sebagian estimasi 45-60 menit pada Wireframe 1.
6. **Android (C13).** Register Android memakai teks placeholder, label, pesan, dan aturan validasi yang sama. Caps Lock tidak dibawa ke Android.
7. **Cakupan C10.** Dokumen 05 (Backlog dan Jadwal) menyebut empat layar untuk C10: Login, Daftar Ebook, Upload, dan Empty State. Register tidak disebut sebagai layar tersendiri; wireframe ini dianggap tercakup oleh butir "Wireframe Login dan Register berlaku untuk Web dan Android".

---

## 7. Status wireframe C10

- [x] Login (revisi 2)
- [x] Register
- [ ] Daftar Ebook
- [ ] Upload
- [ ] Empty State
