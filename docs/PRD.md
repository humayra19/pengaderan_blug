# PRD: Website Pendaftaran Anggota UKM BLUG

Versi 1.0 · Framework: Laravel · Cakupan: 5 halaman

---

## 1. Ringkasan

Website ini punya dua fungsi. Pertama, memperkenalkan BLUG ke calon anggota. Kedua, menggantikan pendaftaran manual (form chat atau spreadsheet) dengan alur pengajuan keanggotaan yang tercatat, sehingga admin bisa meninjau, menerima, atau menolak pengajuan dari satu dashboard.

Website sengaja di batasi ke 5 halaman supaya cepat selesai dan mudah di rawat.

## 2. Tujuan dan indikator berhasil

| Tujuan | Indikator |
|---|---|
| Calon anggota paham apa itu BLUG tanpa bertanya ke pengurus | Beranda memuat profil, kegiatan, dan cara bergabung |
| Pendaftaran tidak lagi lewat chat atau spreadsheet | 100% pengajuan masuk lewat form di website |
| Admin bisa memproses pengajuan dengan cepat | Satu pengajuan dapat di putuskan kurang dari 1 menit dari dashboard |
| Pendaftar tahu status pengajuannya | Status terlihat langsung di halaman pengajuan tanpa menghubungi pengurus |

## 3. Peran pengguna

| Peran | Cara mendapatkan akun | Hak akses |
|---|---|---|
| Pengunjung | Tanpa akun | Melihat beranda |
| Pendaftar (`user`) | Daftar sendiri lewat halaman login | Mengisi dan memantau pengajuan, mengelola profil sendiri |
| Admin (`admin`) | Di buat lewat seeder, tidak bisa daftar sendiri | Melihat dan memutuskan semua pengajuan, mengelola profil sendiri |

## 4. Ruang lingkup

**Termasuk (versi 1):**
- Autentikasi: daftar, masuk, keluar
- Pembagian akses berdasarkan peran
- Form pengajuan keanggotaan
- Dashboard admin: ringkasan angka, tabel pengajuan, aksi terima atau tolak
- Profil sederhana: ubah data diri dan password

**Tidak termasuk (versi 1):**
- Verifikasi email dan reset password lewat email
- Notifikasi email atau WhatsApp otomatis
- Manajemen banyak admin lewat UI
- Upload berkas (CV, KTM, dan sejenisnya)
- Halaman berita, galeri, atau agenda kegiatan terpisah
- Ekspor laporan (masuk daftar pengembangan lanjutan)

## 5. Daftar halaman dan rute

Kelima halaman ini adalah seluruh isi website. Pendaftaran akun di gabung ke halaman login sebagai tab, supaya jumlah halaman tetap lima.

| # | Halaman | Rute | Akses | Keterangan |
|---|---|---|---|---|
| 1 | Beranda | `GET /` | Semua | Penjelasan tentang BLUG |
| 2 | Login dan Daftar | `GET /masuk`, `POST /masuk`, `POST /daftar`, `POST /keluar` | Tamu (belum login) | Dua tab: Masuk dan Daftar |
| 3 | Pengajuan Keanggotaan | `GET /pengajuan`, `POST /pengajuan` | `user` | Form dan status pengajuan |
| 4 | Dashboard | `GET /dashboard`, `PATCH /dashboard/pengajuan/{id}` | `admin` | Data pengajuan dan keputusan |
| 5 | Profil | `GET /profil`, `PUT /profil`, `PUT /profil/password` | Login | Data diri dan ganti password |

**Aturan pengalihan setelah login:** `admin` ke `/dashboard`, `user` ke `/pengajuan`. Pengguna yang membuka halaman di luar perannya di arahkan ke halaman miliknya (bukan error 500).

## 6. Kebutuhan fungsional per halaman

### 6.1 Beranda
- Hero: nama, satu kalimat tentang BLUG, tombol "Daftar Sekarang" (menuju `/masuk` bila belum login, `/pengajuan` bila sudah).
- Bagian "Apa itu BLUG": penjelasan singkat, 2 sampai 3 paragraf.
- Bagian "Yang kami lakukan": kegiatan utama, tampil sebagai daftar singkat.
- Bagian "Cara bergabung": langkah bernomor (Buat akun, Isi pengajuan, Tunggu keputusan).
- Footer: kontak atau media sosial.
- Seluruh teks bersifat statis di Blade. Tidak butuh database.

### 6.2 Login dan Daftar
- Tab **Masuk**: email, password, ingat saya.
- Tab **Daftar**: nama lengkap, email, password, konfirmasi password. Akun baru otomatis berperan `user` dan langsung login.
- Pesan galat tampil di bawah field terkait, dalam bahasa Indonesia.
- Pembatasan percobaan login (throttle) bawaan Laravel.
- Pengguna yang sudah login tidak bisa membuka halaman ini.

### 6.3 Pengajuan Keanggotaan (pendaftar)
Halaman punya empat keadaan, tergantung pengajuan terakhir milik pengguna:

| Keadaan | Tampilan |
|---|---|
| Belum pernah mengajukan | Form pengajuan |
| `pending` | Kartu status "Menunggu", ringkasan isian (hanya baca) |
| `approved` | Kartu status "Diterima" beserta catatan admin bila ada |
| `rejected` | Kartu status "Ditolak", catatan admin, dan tombol "Ajukan Ulang" yang membuka form kembali |

Field form:

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| NIM | teks | Ya | Angka, 8 sampai 15 digit |
| Program studi | teks | Ya | |
| Angkatan | pilihan tahun | Ya | |
| Nomor WhatsApp | teks | Ya | Format 08xx atau 628xx |
| Bidang minat | pilihan | Ya | Daftar pilihan di simpan di config (lihat bagian 13) |
| Motivasi bergabung | textarea | Ya | 50 sampai 1000 karakter |
| Pengalaman atau skill | textarea | Tidak | Maksimal 1000 karakter |

Nama dan email di ambil dari akun, tidak di isi ulang.

### 6.4 Dashboard (admin)
- **Kartu ringkasan:** total pengajuan, `pending`, `approved`, `rejected`.
- **Tabel pengajuan:** kolom nama, NIM, prodi, bidang minat, tanggal masuk, status, aksi. Urutan default: terbaru di atas, 10 baris per halaman.
- **Cari dan saring:** kolom cari (nama atau NIM) dan filter status.
- **Detail:** tombol "Lihat" membuka modal berisi seluruh isian pendaftar.
- **Keputusan:** di dalam modal ada tombol Terima dan Tolak, plus kolom catatan (wajib saat menolak, opsional saat menerima). Perubahan di simpan bersama waktu dan nama admin yang memutuskan.
- Pengajuan yang sudah di putuskan masih bisa di ubah keputusannya oleh admin (untuk koreksi salah klik).

### 6.5 Profil
- Bagian **Data diri:** nama, email, nomor WhatsApp. Pendaftar juga bisa mengubah NIM, prodi, dan angkatan.
- Bagian **Ganti password:** password lama, password baru, konfirmasi.
- Tombol keluar.
- Mengubah profil tidak mengubah isi pengajuan yang sudah terkirim.

## 7. Alur utama

**Pendaftar:** buka Beranda → Daftar Sekarang → buat akun → isi form pengajuan → lihat status `pending` → kembali lagi untuk melihat hasil.

**Admin:** masuk → Dashboard → buka detail pengajuan `pending` → terima atau tolak → status berubah dan terlihat oleh pendaftar.

## 8. Model data

**users**

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | PK |
| name | string | |
| email | string | unik |
| password | string | di hash |
| role | enum(`admin`, `user`) | default `user` |
| phone | string, null | |
| nim | string, null | |
| study_program | string, null | |
| batch_year | year, null | angkatan |
| timestamps | | |

**membership_applications**

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | PK |
| user_id | FK ke users | |
| interest | string | bidang minat |
| motivation | text | |
| experience | text, null | |
| status | enum(`pending`, `approved`, `rejected`) | default `pending` |
| admin_note | text, null | |
| reviewed_by | FK ke users, null | |
| reviewed_at | timestamp, null | |
| timestamps | | |

Relasi: satu `user` punya banyak `membership_applications` (karena bisa mengajukan ulang), tapi hanya satu yang berstatus `pending` atau `approved` pada satu waktu.

## 9. Aturan bisnis

1. Pendaftar tidak bisa membuat pengajuan baru bila masih punya yang `pending` atau `approved`.
2. Pendaftar boleh membuat pengajuan baru hanya bila pengajuan terakhir `rejected`.
3. Catatan admin wajib terisi saat status di ubah ke `rejected`.
4. Akun `admin` hanya lahir dari seeder.
5. Semua data pengajuan hanya bisa di baca oleh pemiliknya dan admin.

## 10. Kebutuhan non-fungsional

- **Keamanan:** password di hash (bcrypt bawaan Laravel), proteksi CSRF, validasi di server, middleware peran pada rute admin, query lewat Eloquent (bukan string mentah).
- **Responsif:** nyaman di lebar 360px ke atas. Tabel dashboard bisa di geser horizontal di layar kecil.
- **Performa:** halaman utama termuat di bawah 2 detik di koneksi biasa. Tabel dashboard memakai pagination.
- **Aksesibilitas:** kontras teks minimal 4.5:1, semua input punya label, fokus keyboard terlihat.
- **Bahasa:** seluruh antarmuka berbahasa Indonesia.

## 11. Stack teknis

| Bagian | Pilihan |
|---|---|
| Framework | Laravel 11 atau 12 |
| Autentikasi | Laravel Breeze (stack Blade), rute di sesuaikan ke bahasa Indonesia |
| Tampilan | Blade + Bootstrap 5 (di kustomisasi, lihat `style.md`) |
| Build asset | Vite |
| Database | MySQL |
| Hak akses | Middleware `role` sederhana, tanpa package tambahan |

## 12. Rencana pengerjaan

| Tahap | Isi |
|---|---|
| 1. Fondasi | Install Laravel, Breeze, Bootstrap, setup layout dan tema dari `style.md` |
| 2. Auth dan peran | Kolom `role`, seeder admin, middleware, pengalihan setelah login |
| 3. Beranda | Halaman statis |
| 4. Pengajuan | Migrasi, model, form, empat keadaan halaman |
| 5. Dashboard | Kartu ringkasan, tabel, cari dan filter, modal keputusan |
| 6. Profil | Ubah data dan password |
| 7. Pemolesan | Validasi pesan Indonesia, uji di layar kecil, uji akses antar peran |

## 13. Asumsi dan hal yang perlu di isi

- Kepanjangan dan deskripsi resmi BLUG belum ada, jadi teks Beranda masih placeholder.
- Daftar **bidang minat** belum di tentukan. Simpan di `config/blug.php` supaya mudah di ubah tanpa menyentuh kode lain.
- Cara admin pertama dibuat: lewat `DatabaseSeeder` dengan email dan password dari file `.env`.
- Belum ada kebutuhan notifikasi. Bila nanti di perlukan, tahap berikutnya adalah email lewat queue Laravel.
