# User Stories: Website Pendaftaran Anggota UKM BLUG

Prioritas: **Must** = wajib ada di versi 1, **Should** = di kerjakan bila waktu cukup.

Peran: **Pengunjung** (belum login), **Pendaftar** (`user`), **Admin** (`admin`).

---

## Epic A. Beranda

### US-01 · Memahami BLUG · Must
**Sebagai** pengunjung, **saya ingin** membaca penjelasan tentang BLUG, **supaya** saya tahu apakah UKM ini cocok untuk saya.

Kriteria penerimaan:
- Beranda menampilkan penjelasan BLUG, kegiatan utama, dan cara bergabung.
- Halaman bisa di buka tanpa login.
- Tombol "Daftar Sekarang" ada di bagian atas dan di bagian cara bergabung.
- Bila pengunjung sudah login, tombol itu menuju `/pengajuan`. Bila belum, menuju `/masuk`.

---

## Epic B. Akun

### US-02 · Membuat akun · Must
**Sebagai** pengunjung, **saya ingin** membuat akun, **supaya** saya bisa mengisi pengajuan keanggotaan.

Kriteria penerimaan:
- Tab Daftar meminta nama, email, password, dan konfirmasi password.
- Email yang sudah terpakai menampilkan pesan galat di bawah field email.
- Password minimal 8 karakter.
- Setelah berhasil, saya langsung login dan di arahkan ke `/pengajuan`.
- Akun baru selalu berperan `user`.

### US-03 · Masuk ke akun · Must
**Sebagai** pengguna terdaftar, **saya ingin** login dengan email dan password, **supaya** data saya tetap aman.

Kriteria penerimaan:
- Login berhasil mengalihkan `admin` ke `/dashboard` dan `user` ke `/pengajuan`.
- Email atau password salah menampilkan satu pesan umum tanpa membocorkan mana yang salah.
- Setelah beberapa kali gagal beruntun, login di tahan sementara.
- Pengguna yang sudah login tidak bisa membuka `/masuk`.

### US-04 · Keluar dari akun · Must
**Sebagai** pengguna, **saya ingin** keluar dari akun, **supaya** orang lain tidak bisa memakai sesi saya.

Kriteria penerimaan:
- Tombol keluar tersedia di navbar dan di halaman profil.
- Setelah keluar, saya kembali ke Beranda dan halaman yang butuh login tidak bisa di akses lagi.

---

## Epic C. Pengajuan keanggotaan

### US-05 · Mengisi pengajuan · Must
**Sebagai** pendaftar, **saya ingin** mengisi form pengajuan, **supaya** pengurus bisa menilai permohonan saya.

Kriteria penerimaan:
- Form berisi NIM, program studi, angkatan, nomor WhatsApp, bidang minat, motivasi, dan pengalaman (opsional).
- Nama dan email terisi otomatis dari akun dan tidak bisa di ubah di form ini.
- Field wajib yang kosong atau tidak valid menampilkan pesan galat per field, dan isian lain tidak hilang.
- Setelah di kirim, status pengajuan menjadi `pending` dan halaman berganti ke kartu status.

### US-06 · Memantau status · Must
**Sebagai** pendaftar, **saya ingin** melihat status pengajuan saya, **supaya** saya tidak perlu bertanya ke pengurus.

Kriteria penerimaan:
- `pending`: kartu "Menunggu" dan ringkasan isian (hanya baca).
- `approved`: kartu "Diterima" beserta catatan admin bila ada.
- `rejected`: kartu "Ditolak" beserta catatan admin.
- Pendaftar yang masih `pending` atau `approved` tidak bisa mengirim pengajuan kedua, termasuk lewat akses langsung ke rute `POST /pengajuan`.

### US-07 · Mengajukan ulang · Should
**Sebagai** pendaftar yang ditolak, **saya ingin** mengajukan ulang, **supaya** saya punya kesempatan kedua setelah memperbaiki isian.

Kriteria penerimaan:
- Tombol "Ajukan Ulang" hanya muncul saat status `rejected`.
- Form terbuka dengan isian lama sebagai nilai awal.
- Pengajuan baru tercatat sebagai baris baru, riwayat lama tetap ada.

---

## Epic D. Dashboard admin

### US-08 · Melihat ringkasan · Must
**Sebagai** admin, **saya ingin** melihat jumlah pengajuan per status, **supaya** saya tahu seberapa banyak yang menunggu keputusan.

Kriteria penerimaan:
- Empat kartu: total, `pending`, `approved`, `rejected`.
- Angka sesuai data terbaru di database setiap halaman di muat.

### US-09 · Melihat daftar pengajuan · Must
**Sebagai** admin, **saya ingin** melihat semua pengajuan dalam satu tabel, **supaya** saya bisa meninjau satu per satu.

Kriteria penerimaan:
- Kolom: nama, NIM, prodi, bidang minat, tanggal masuk, status, aksi.
- Urutan default: terbaru di atas, 10 baris per halaman.
- Di layar kecil, tabel bisa di geser ke samping tanpa merusak tata letak.
- `user` biasa yang membuka `/dashboard` di alihkan ke `/pengajuan`.

### US-10 · Mencari dan menyaring · Should
**Sebagai** admin, **saya ingin** mencari nama atau NIM dan menyaring status, **supaya** saya cepat menemukan pengajuan tertentu.

Kriteria penerimaan:
- Kolom cari memfilter berdasarkan nama atau NIM.
- Filter status berisi Semua, Menunggu, Diterima, Ditolak.
- Pencarian dan filter bisa di gabung, dan tetap aktif saat pindah halaman.

### US-11 · Membaca detail pengajuan · Must
**Sebagai** admin, **saya ingin** membuka detail pengajuan, **supaya** saya bisa membaca motivasi dan pengalaman pendaftar secara utuh.

Kriteria penerimaan:
- Tombol "Lihat" membuka modal berisi seluruh isian pendaftar dan status saat ini.
- Modal tidak memuat ulang halaman.

### US-12 · Menerima atau menolak · Must
**Sebagai** admin, **saya ingin** memutuskan pengajuan, **supaya** pendaftar mendapat kepastian.

Kriteria penerimaan:
- Modal punya tombol Terima, tombol Tolak, dan kolom catatan.
- Menolak tanpa catatan di tolak oleh validasi dengan pesan jelas.
- Setelah di simpan, status, nama admin, dan waktu keputusan tercatat, dan tabel menampilkan status baru.
- Admin bisa mengubah keputusan yang sudah di buat (untuk koreksi).

---

## Epic E. Profil

### US-13 · Mengubah data diri · Must
**Sebagai** pengguna, **saya ingin** memperbarui data diri saya, **supaya** informasi saya tetap benar.

Kriteria penerimaan:
- Nama, email, dan nomor WhatsApp bisa di ubah. Pendaftar juga bisa mengubah NIM, prodi, dan angkatan.
- Email yang bentrok dengan akun lain di tolak dengan pesan galat.
- Pesan sukses muncul setelah tersimpan.
- Perubahan tidak mengubah pengajuan yang sudah terkirim.

### US-14 · Mengganti password · Must
**Sebagai** pengguna, **saya ingin** mengganti password, **supaya** akun saya tetap aman.

Kriteria penerimaan:
- Wajib memasukkan password lama yang benar.
- Password baru minimal 8 karakter dan harus sama dengan konfirmasinya.
- Setelah berhasil, saya tetap login dan muncul pesan sukses.

---

## Epic F. Keamanan dasar

### US-15 · Akses sesuai peran · Must
**Sebagai** pemilik sistem, **saya ingin** setiap peran hanya bisa mengakses halamannya, **supaya** data pendaftar tidak bocor.

Kriteria penerimaan:
- Pengunjung yang membuka `/pengajuan`, `/dashboard`, atau `/profil` di arahkan ke `/masuk`.
- `user` tidak bisa membaca atau mengubah pengajuan milik pengguna lain.
- `user` tidak bisa memanggil rute keputusan admin, termasuk lewat permintaan langsung.

---

## Ringkasan

| ID | Cerita | Prioritas |
|---|---|---|
| US-01 | Memahami BLUG | Must |
| US-02 | Membuat akun | Must |
| US-03 | Masuk ke akun | Must |
| US-04 | Keluar dari akun | Must |
| US-05 | Mengisi pengajuan | Must |
| US-06 | Memantau status | Must |
| US-07 | Mengajukan ulang | Should |
| US-08 | Melihat ringkasan | Must |
| US-09 | Melihat daftar pengajuan | Must |
| US-10 | Mencari dan menyaring | Should |
| US-11 | Membaca detail pengajuan | Must |
| US-12 | Menerima atau menolak | Must |
| US-13 | Mengubah data diri | Must |
| US-14 | Mengganti password | Must |
| US-15 | Akses sesuai peran | Must |
