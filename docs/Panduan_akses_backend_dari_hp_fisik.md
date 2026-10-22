# Panduan Akses Backend dari HP Fisik

## Ebook Storage & To-Do List

| Item     | Detail                                                                                  |
| -------- | --------------------------------------------------------------------------------------- |
| Tanggal  | 22 Oktober 2026                                                                         |
| Platform | Android (Kotlin) yang dijalankan di HP fisik, dan server Laravel di komputer pengembang |
| Acuan    | 05-Backlog-dan-Jadwal-Chunking (chunk C12, butir "Akses backend dari HP fisik")         |

---

## 1. Tujuan

Dokumen ini menetapkan cara HP fisik menjangkau server Laravel selama pengembangan, dan memberi langkah untuk menyiapkannya. Dokumen ini hanya mengatur koneksi pengembangan, bukan arsitektur sistem.

---

## 2. Keputusan

HP fisik mengakses Laravel melalui **jaringan lokal**: server Laravel berjalan di komputer, dan HP berada pada Wi-Fi yang sama.

Hosting online tidak dipakai karena menambah pekerjaan (pemilihan layanan, konfigurasi server, domain atau HTTPS) yang tidak diperlukan untuk MVP.

---

## 3. Panduan

### 3.1 Sambungkan perangkat

Komputer dan HP harus tersambung ke Wi-Fi yang sama.

### 3.2 Jalankan server

Di folder proyek Laravel:

```text
php artisan serve --host=0.0.0.0
```

Tanpa `--host=0.0.0.0`, server hanya menerima koneksi dari komputer itu sendiri, sehingga HP tidak dapat menjangkaunya. Port bawaannya adalah 8000.

### 3.3 Cari IP komputer

Router membagikan IP secara otomatis (DHCP), sehingga IP komputer dapat berubah. Karena itu IP **diperiksa ulang pada setiap sesi kerja**.

- Windows: jalankan `ipconfig`, lalu lihat `IPv4 Address` pada adapter Wi-Fi.
- macOS atau Linux: jalankan `ifconfig` atau `ip addr`.

Alamat server yang dipakai HP adalah:

```text
http://{IP-komputer}:8000/
```

### 3.4 Izinkan firewall

Izinkan koneksi masuk ke port 8000 pada firewall komputer untuk jaringan **privat**. Tanpa izin ini, HP biasanya gagal tersambung walaupun server berjalan.

### 3.5 Uji dari browser HP

Sebelum menguji aplikasi Android, buka `http://{IP-komputer}:8000/` di browser HP. Bila halaman Laravel tampil, jalur koneksi sudah benar. Bila tidak tampil, aplikasi Android juga tidak akan tersambung, sehingga masalah koneksi dibereskan lebih dulu di sini.

### 3.6 Atur alamat di Android

Alamat dasar server (base URL) untuk Retrofit ditulis di **satu tempat saja** pada proyek Android, sehingga penggantian IP cukup dilakukan sekali.

Format yang dipakai:

```text
http://{IP-komputer}:8000/api/
```

Karena alamat memakai `http` (tanpa enkripsi), `AndroidManifest` perlu mengizinkan lalu lintas tanpa enkripsi khusus untuk pengembangan. Hal ini sudah tercatat sebagai butir tersendiri pada chunk C12.

---

## 4. Bila Koneksi Gagal

| Gejala                                       | Penyebab yang paling mungkin                                                                         | Tindakan                                                                                       |
| -------------------------------------------- | ---------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| Browser HP tidak membuka halaman Laravel     | Firewall memblokir port 8000                                                                         | Izinkan port 8000 untuk jaringan privat (3.4)                                                  |
| Sebelumnya berhasil, sekarang gagal          | IP komputer berubah                                                                                  | Periksa IP lagi (3.3), lalu perbarui base URL di Android                                       |
| Server berjalan tetapi HP tetap tidak sampai | Wi-Fi memblokir komunikasi antar perangkat (client isolation), umum di Wi-Fi kampus atau tempat umum | Hidupkan hotspot di HP, sambungkan komputer ke hotspot tersebut, lalu ulangi 3.3 sampai 3.5    |
| Browser HP berhasil, aplikasi Android gagal  | Lalu lintas tanpa enkripsi belum diizinkan, atau base URL salah                                      | Periksa pengaturan di `AndroidManifest` (3.6) dan penulisan base URL, termasuk akhiran `/api/` |

---

## 5. Batasan

- Cara ini hanya untuk pengembangan dan pengujian. Aplikasi tidak dapat dipakai dari luar jaringan yang sama.
- Server berhenti bila komputer mati atau terminal ditutup.
- Penyimpanan token dan hal teknis Android lainnya tidak dibahas di sini.
