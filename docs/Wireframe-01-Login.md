# Wireframe 1 dari 4: Login (revisi 2)

Dokumen ini adalah wireframe halaman Login versi **Web** untuk proyek Ebook Storage & To-Do List. Halaman Login sudah dibuat pada chunk C01 (setup, register, login, logout Web); wireframe ini mendokumentasikan dan merapikannya sebagai bagian dari chunk C10 (rancangan empat layar: Login, Daftar Ebook, Upload, Empty State). Versi Android disesuaikan nanti pada chunk C13 (login dan register Android).

Revisi 2 menggabungkan seluruh isi revisi 1 dengan perubahan placeholder menjadi kalimat ajakan.

---

## 1. Keadaan default

```text
┌──────────────────────────────────────────────┐
│  Ebook Storage                               │  ← header: nama aplikasi saja,
├──────────────────────────────────────────────┤    tanpa menu (belum login)
│                                              │
│          ┌──────────────────────────┐        │
│          │  Masuk ke Koleksi Anda   │        │
│          │                          │        │
│          │  Email                   │        │
│          │  ┌────────────────────┐  │        │
│          │  │ Ketik email akun   │  │ ← ajakan, abu-abu
│          │  │ Anda               │  │        │
│          │  └────────────────────┘  │        │
│          │                          │        │
│          │  Password                │        │
│          │  ┌──────────────┬─────┐  │        │
│          │  │ Masukkan     │ 👁  │  │ ← tombol tampilkan/sembunyikan
│          │  │ password Anda│     │  │        │
│          │  └──────────────┴─────┘  │        │
│          │                          │        │
│          │  [ Masuk ke Koleksi Ebook]│       │ ← label menyebut tujuan
│          │                          │        │
│          │  Belum punya akun?       │        │
│          │  Buat akun baru          │        │ ← tautan ke /register
│          └──────────────────────────┘        │
└──────────────────────────────────────────────┘
```

---

## 2. Keadaan interaksi

### 2.1 Kolom password difokuskan (dropdown info muncul)

```text
  Password
  ┌──────────────────────┬─────┐
  │ ••••••               │ 👁‍🗨 │
  └──────────────────────┴─────┘
  ┌──────────────────────────────┐   ← turun dari bawah kolom
  │ ℹ Password akun Anda minimal │     (animasi slide-down + fade,
  │   8 karakter.                │      sekitar 200 ms)
  └──────────────────────────────┘
```

### 2.2 Caps Lock menyala (hanya saat fokus di password)

```text
  Password
  ┌──────────────────────┬─────┐
  │ ••••••               │ 👁  │
  └──────────────────────┴─────┘
  ⚠ Caps Lock aktif             ← kuning/oranye, tepat di bawah kolom,
                                  hilang otomatis saat Caps Lock mati
                                  atau kolom kehilangan fokus
```

Jika dropdown dan peringatan Caps Lock muncul bersamaan, peringatan Caps Lock **ditampilkan di atas** dropdown karena lebih mendesak.

### 2.3 Password ditampilkan

```text
  ┌──────────────────────┬─────┐
  │ rahasia123           │ 🙈  │   ← ikon berganti; teks terbaca
  └──────────────────────┴─────┘
```

### 2.4 Tombol saat proses berjalan

```text
  [ Memeriksa akun… ]   ← nonaktif, mencegah klik ganda
```

---

## 3. Keadaan pesan

**A. Setelah register berhasil** (sesuai 02-User-Flow bagian 5.1: akun dibuat, lalu login)

```text
┌────────────────────────────────────┐
│ ✓ Akun berhasil dibuat.            │  ← banner sukses di atas form
│   Silakan masuk.                   │
└────────────────────────────────────┘
   (form kosong)
```

**B. Isian kosong**

```text
  Email
  ┌──────────────┐
  └──────────────┘
  Email wajib diisi.            ← merah, di bawah kolom

  Password
  ┌──────────────┐
  └──────────────┘
  Password wajib diisi.
```

**C. Kredensial salah**

```text
┌────────────────────────────────────┐
│ ✗ Email atau password salah.       │  ← satu pesan, tidak membedakan
└────────────────────────────────────┘    mana yang salah
  Email: [user@example.com]  ← tetap terisi
  Password: [          ]     ← dikosongkan
```

**D. Terlalu banyak percobaan** (batas 5 per menit)

```text
┌────────────────────────────────────┐
│ ✗ Terlalu banyak percobaan login.  │
│   Coba lagi dalam 45 detik.        │
└────────────────────────────────────┘
```

---

## 4. Daftar elemen

| Elemen | Aturan |
|---|---|
| Label Email dan Password | **Tetap ada** di atas kolom, tidak digantikan placeholder. Setiap kolom memakai `<label for>` yang terhubung ke input |
| Email | `type="email"`, fokus otomatis saat halaman dibuka. Placeholder: `Ketik email akun Anda` |
| Password | `type="password"`. Placeholder: `Masukkan password Anda` |
| Tombol tampilkan password | Tombol sungguhan (bukan ikon dekoratif), `aria-label` berganti "Tampilkan password" / "Sembunyikan password"; password otomatis disembunyikan lagi saat form dikirim |
| Dropdown info | Muncul saat kolom password difokuskan, menutup saat kehilangan fokus; menghormati `prefers-reduced-motion` (tanpa animasi bila diminta) |
| Indikator Caps Lock | Dibaca dari `event.getModifierState('CapsLock')` pada `keydown` dan `keyup` |
| Tombol utama | "Masuk ke Koleksi Ebook" mengirim `POST /login`; saat proses berubah menjadi "Memeriksa akun…" dan nonaktif |
| Tautan | "Buat akun baru" menuju `GET /register` |
| Lupa password | **Tidak ada**, karena di luar MVP |
| Ingat saya | **Tidak ada** (kolom `remember_token` sudah dihapus pada C01) |

## 5. Teks placeholder

| Kolom | Placeholder | Alasan |
|---|---|---|
| Email | `Ketik email akun Anda` | Kata kerja dan objek yang jelas |
| Password | `Masukkan password Anda` | Sama, dan tidak membocorkan aturan apa pun |

---

## 6. Keputusan dan catatan

1. **Email tetap terisi, password selalu dikosongkan** saat login gagal.
2. **Pesan error kredensial hanya satu**, sama seperti kontrak API (kode `invalid_credentials`).
3. **Tanpa elemen di luar MVP:** lupa password, ingat saya, login sosial.
4. **Dropdown di Login berisi info statis, bukan daftar centang interaktif.** Di Login user memasukkan password yang sudah ada, sehingga centang hijau/merah langsung bisa membingungkan ("password saya salah?"). Daftar centang live dipakai di halaman Register, tempat password dibuat. Aturannya hanya **minimal 8 karakter dan sama dengan konfirmasi** (04-API-Specification bagian 5.1); syarat huruf besar atau angka tidak ditambahkan karena validasi server tidak memeriksanya.
5. **Caps Lock hanya untuk Web.** Peringatan baru muncul setelah user menekan satu tombol, karena browser tidak dapat mengetahui status Caps Lock sebelum ada event keyboard. Fitur ini tidak dibawa ke Android (C13) kecuali diminta.
6. **Placeholder berupa ajakan, bukan contoh isian.** Placeholder hilang saat mengetik dan kontrasnya rendah, sehingga informasi penting (misalnya "minimal 8 karakter") ada di dropdown, bukan di placeholder.
7. **Panjang teks placeholder.** Pada layar sempit, `Ketik email akun Anda` bisa terpotong. Bila terjadi saat pengujian, gunakan versi pendek `Ketik email Anda`.
8. **Konsistensi ke Register.** Pola ajakan yang sama dipakai, misalnya `Ketik nama Anda`, `Ketik email aktif Anda`, `Buat password baru`, `Ketik ulang password Anda`.
9. **Teks pesan** perlu dicocokkan dengan teks yang sudah ada di implementasi C01 bila berbeda, supaya wireframe dan implementasi seragam.
10. **Dampak ke jadwal.** Halaman Login dari C01 sudah selesai tanpa fitur ini. Penambahan berupa JavaScript biasa (tanpa Node atau Vite, sesuai aturan C01) dan perlu dicatat sebagai pekerjaan tambahan di C10 serta satu baris di Log Perubahan Rancangan (05-Backlog bagian 9). Perkiraan sekitar 45-60 menit, hanya perkiraan. Bila Gate 7 Oktober terdesak, tombol tampilkan password dan Caps Lock dibuat lebih dulu, animasi dropdown menyusul.

---

## 7. Status wireframe C10

- [x] Login (revisi 2)
- [ ] Register
- [ ] Daftar Ebook
- [ ] Upload
- [ ] Empty State
