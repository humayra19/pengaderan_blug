# Prompt Bertahap untuk Antigravity: Website UKM BLUG

Satu prompt = satu langkah kecil yang bisa kamu cek sendiri sebelum lanjut.

## Cara pakai

1. Tempel **Prompt 0** sekali di awal. Kalau Antigravity punya fitur rules atau instruksi workspace, taruh di sana supaya berlaku terus. Kalau tidak, tempel ulang di awal tiap sesi atau percakapan baru.
2. Kirim prompt sesuai urutan di bawah, **satu per satu**.
3. Setelah agent melapor, cek hasilnya di browser dengan daftar "Cek manual" di tiap prompt.
4. Kalau sudah benar, **commit manual** lalu kirim prompt berikutnya. Kalau ada yang salah, pakai **Prompt Koreksi** di bagian paling bawah.

## Urutan

| No | Prompt | Isi |
|---|---|---|
| 0 | Konteks dan audit | Aturan kerja, cek kondisi project |
| 1 | Fondasi tampilan | Bootstrap, CSS, layout, navbar, footer |
| 2 | US-01 | Beranda |
| 3 | US-03 | Login |
| 4 | US-02 | Daftar akun |
| 5 | US-04 | Keluar |
| 6 | US-15 | Akses sesuai peran |
| 7 | US-05 | Form pengajuan |
| 8 | US-06 | Status pengajuan |
| 9 | US-07 | Ajukan ulang |
| 10 | US-08 | Kartu ringkasan dashboard |
| 11 | US-09 | Tabel pengajuan |
| 12 | US-10 | Cari dan filter |
| 13 | US-11 | Modal detail |
| 14 | US-12 | Terima atau tolak |
| 15 | US-13 | Profil: data diri |
| 16 | US-14 | Profil: ganti password |
| 17 | Penutup | Bahasa Indonesia, pembersihan, uji akhir |

Login (US-03) sengaja di kerjakan sebelum Daftar (US-02) karena kartu dua tab di buat sekali di login, lalu tab Daftar menyusul.

---

## Prompt 0: Konteks dan audit (kirim sekali di awal)

```
Kamu membantu membangun website pendaftaran anggota UKM BLUG di project Laravel yang sudah berjalan. Kerjakan per tugas kecil, satu per satu.

DOKUMEN ACUAN (baca semuanya sekarang, sebelum menulis kode):
- docs/PRD.md : kebutuhan, halaman, rute, model data, aturan bisnis
- docs/user-stories.md : cerita pengguna dan kriteria penerimaan
- docs/style.md : panduan tampilan (palet kuning dan hitam, tipografi, komponen, CSS dasar)
Dokumen ini sumber kebenaran. Jangan mengubah isinya. Kalau ada yang bertentangan atau kurang jelas, tanya dulu, jangan menebak.

KONDISI PROJECT
- Laravel + Breeze (stack Blade), MySQL database "blug", Laragon di Windows.
- Dijalankan dengan php artisan serve dan npm run dev.
- Tahap 2 PRD seharusnya sudah ada: kolom role/phone/nim/study_program/batch_year di tabel users, AdminSeeder, middleware alias "role", pengalihan setelah login. Verifikasi dulu. Jangan tulis ulang yang sudah benar, perbaiki hanya yang kurang.

ATURAN TETAP (berlaku untuk semua tugas berikutnya)
1. Kerjakan HANYA tugas di prompt yang sedang aktif. Jangan mengerjakan fitur dari user story lain walaupun terlihat gampang.
2. Jangan menjalankan perintah git apa pun (init, add, commit, push). Aku yang mengurusnya.
3. Jangan menjalankan perintah yang menghapus data (migrate:fresh, db:wipe, dan sejenisnya) tanpa izin. Perubahan skema lewat migration baru, jangan menyunting migration lama yang sudah dijalankan.
4. Jangan memasang package di luar yang disebut di prompt. Hak akses cukup dengan middleware dan policy sederhana.
5. Semua teks antarmuka berbahasa Indonesia, termasuk pesan validasi dan flash message.
6. Validasi pakai Form Request. Query pakai Eloquent, tanpa SQL string mentah. Jangan mematikan proteksi CSRF.
7. Ikuti docs/style.md apa adanya. Jangan menambah warna, font, gradien, atau ilustrasi di luar panduan. Kuning tidak boleh jadi warna teks di atas latar terang.
8. Pertahankan nama rute bawaan Breeze (login, register, logout, profile.edit, profile.update, password.update). Ini menggantikan penulisan /masuk dan /profil di PRD, dan keputusan ini sengaja.
9. Rute buatan sendiri: GET / (beranda), GET /pengajuan (pengajuan.index), POST /pengajuan (pengajuan.store), GET /dashboard (dashboard), PATCH /dashboard/pengajuan/{application} (dashboard.pengajuan.update).
10. Verifikasi email tidak dipakai. Tidak boleh ada rute yang memakai middleware "verified".
11. Teks yang belum ada (kepanjangan BLUG, deskripsi, kegiatan) tulis sebagai placeholder dalam kurung siku, misalnya [Deskripsi resmi BLUG]. Jangan mengarang fakta tentang BLUG.
12. Status pengajuan di database memakai nilai: pending, approved, rejected. Label di antarmuka: Menunggu, Diterima, Ditolak.

FORMAT LAPORAN (setelah tiap tugas selesai, lalu BERHENTI dan tunggu aku)
- File yang dibuat atau diubah
- Cara menguji di browser, langkah demi langkah
- Hal yang belum beres atau keputusan yang kamu ambil sendiri
- Pertanyaan, bila ada

TUGAS SEKARANG: AUDIT SAJA
Jalankan php artisan route:list, cek kolom tabel users, cek apakah AdminSeeder, middleware "role", dan pengalihan setelah login sudah ada dan benar, dan cek apakah Tailwind bawaan Breeze masih terpasang. Laporkan temuanmu. Jangan menulis fitur baru di tugas ini.
```

**Cek manual:** laporan audit sesuai dengan kondisi project-mu. Kalau ada yang di bilang "belum ada" padahal sudah kamu buat, kasih tahu agent sebelum lanjut.

---

## Prompt 1: Fondasi tampilan

```
TUGAS: Fondasi tampilan. Ini bukan user story, tapi prasyarat semua halaman.

Kerjakan:
1. Pastikan Bootstrap 5 terpasang lewat Vite (resources/js/app.js mengimpor 'bootstrap', resources/css/app.css mengimpor CSS Bootstrap). Hapus sisa Tailwind (paket, tailwind.config.js, postcss.config.js, class Tailwind yang masih di pakai di layout bawaan Breeze).
2. Terapkan CSS dasar dari docs/style.md bagian 4 di resources/css/app.css. Satu koreksi: tautan di navbar tidak boleh menimpa warna tombol. Pakai selector `.navbar-blug a:not(.btn)` dan `.navbar-blug a:not(.btn):hover`, supaya tombol Masuk tetap kuning dengan teks hitam.
3. Buat layout utama resources/views/layouts/app.blade.php (komponen <x-app-layout> dengan atribut title), memuat font dari docs/style.md bagian 2, @vite, partial navbar, slot, dan partial footer.
4. Partial navbar sesuai docs/style.md bagian 5: latar hitam, merek "blug_" kuning bergaya monospace, tautan aktif kuning bergaris bawah. Isi menyesuaikan peran: tamu melihat Beranda dan tombol Masuk; admin melihat Beranda, Dashboard, Profil, tombol Keluar; user melihat Beranda, Pengajuan, Profil, tombol Keluar. Tombol Keluar memakai form POST ke route logout. Collapse di layar kecil.
5. Partial footer hitam sesuai panduan.
6. Layout guest bawaan Breeze (layouts/guest) tidak dipakai lagi, hapus bila sudah tidak ada yang merujuk.

Jangan membuat isi halaman apa pun di tugas ini. Cukup pastikan layout bisa di pakai oleh halaman yang ada.
```

**Cek manual:** jalankan `npm run dev` dan buka halaman mana pun. Navbar hitam, `blug_` kuning, footer hitam, tidak ada sisa gaya Tailwind yang berantakan.

---

## Prompt 2: US-01 Beranda

```
TUGAS: User story US-01 (Memahami BLUG). Baca bagian itu di docs/user-stories.md dan bagian 6.1 di docs/PRD.md.

Kerjakan Beranda di resources/views/welcome.blade.php (rute GET / bernama "beranda", bisa dibuka tanpa login):
- Hero: label kecil monospace, judul besar dengan satu kata "BLUG" diberi class .mark-yellow, satu paragraf pendek, dan tombol utama "Daftar Sekarang".
- Bagian "Apa itu BLUG?" di atas latar putih, dua paragraf.
- Bagian "Yang kami lakukan": daftar kegiatan sebagai baris dengan garis pemisah (bukan kartu).
- Bagian "Cara bergabung": latar hitam, tiga langkah bernomor 01, 02, 03 (angka kuning monospace): Buat akun, Isi pengajuan, Tunggu keputusan.
- Semua isi tentang BLUG ditulis placeholder dalam kurung siku. Jangan mengarang fakta.
- Tombol "Daftar Sekarang" muncul di hero dan di bagian cara bergabung. Bila belum login menuju route register. Bila sudah login menuju halaman utama perannya (admin ke dashboard, user ke pengajuan.index).
- Ikuti docs/style.md bagian 6 baris Beranda. Tanpa foto stok.
- Rute dashboard dan pengajuan.index mungkin belum punya halaman. Kalau begitu, buat view placeholder paling sederhana (satu judul) supaya tautan tidak error.
```

**Cek manual:** Beranda terbuka tanpa login, ada satu bagian hitam di tengah, tombol kuning bekerja, dan tampilan rapi di lebar 360px.

---

## Prompt 3: US-03 Login

```
TUGAS: User story US-03 (Masuk ke akun). Baca bagian itu di docs/user-stories.md dan bagian 6.2 di docs/PRD.md.

Kerjakan halaman login memakai rute bawaan Breeze (GET dan POST login):
- Satu kartu putih di tengah, lebar maksimal 420px. Di atasnya dua tab berupa TAUTAN biasa ke route login dan route register (tanpa JavaScript), tab aktif bergaris bawah kuning tebal (class .tabs-blug di docs/style.md bagian 6).
- Buat partial resources/views/auth/_form.blade.php yang menerima variabel $tab ('login' atau 'register'). Sekarang isi hanya tab login: email, password, "Ingat saya", tombol utama "Masuk". Halaman login memanggil partial ini. Tab register hanya perlu berisi kerangka kosong, formnya dikerjakan di tugas berikutnya.
- Pesan galat tampil di bawah field terkait (class is-invalid dan invalid-feedback).
- Email atau password salah menampilkan satu pesan umum yang tidak membocorkan mana yang salah.
- Pembatasan percobaan login bawaan Breeze (throttle) tetap aktif.
- Setelah login, admin di arahkan ke dashboard dan user ke pengajuan.index (pakai method homeRoute() di model User kalau ada).
- Pengguna yang sudah login tidak bisa membuka halaman login (di alihkan ke halaman utama perannya).
- Hapus tautan "Lupa password" dari halaman ini.

Jangan mengerjakan form daftar di tugas ini.
```

**Cek manual:** login admin masuk ke `/dashboard`, login user masuk ke `/pengajuan`, password salah menampilkan galat, dan membuka `/login` saat sudah login di alihkan.

---

## Prompt 4: US-02 Daftar akun

```
TUGAS: User story US-02 (Membuat akun). Baca bagian itu di docs/user-stories.md dan bagian 6.2 di docs/PRD.md.

Kerjakan tab Daftar di partial resources/views/auth/_form.blade.php dan halaman register (rute bawaan Breeze):
- Field: nama lengkap, email, password, konfirmasi password. Tombol utama "Buat akun". Teks bantu "Minimal 8 karakter" di bawah password.
- Validasi lewat Form Request atau RegisteredUserController: nama wajib, email valid dan unik (galat tampil di bawah field email), password minimal 8 karakter dan sama dengan konfirmasi.
- Akun baru SELALU berperan user. Pastikan role tidak bisa di kirim dari form (cek bahwa role tidak ada di $fillable dan tidak dibaca dari request).
- Setelah berhasil: pengguna langsung login dan di arahkan ke pengajuan.index.
- Pengguna yang sudah login tidak bisa membuka halaman daftar.
- Karena verifikasi email tidak dipakai, pastikan pendaftaran tidak memicu pengiriman email verifikasi.
- Isian lama (nama, email) tetap terisi saat validasi gagal, password tidak.
```

**Cek manual:** daftar akun baru berhasil dan langsung masuk `/pengajuan`. Coba email yang sudah terpakai, password 5 karakter, dan konfirmasi yang beda. Cek di database bahwa kolom `role` akun baru berisi `user`.

---

## Prompt 5: US-04 Keluar

```
TUGAS: User story US-04 (Keluar dari akun). Baca bagian itu di docs/user-stories.md.

Kerjakan:
- Tombol Keluar di navbar harus berfungsi lewat form POST ke route logout (dengan @csrf). Pastikan hanya berupa tombol di dalam form, bukan tautan GET.
- Setelah keluar: sesi di invalidate dan token di regenerate (bawaan Breeze), pengguna kembali ke Beranda, dan halaman yang butuh login tidak bisa di buka lagi (di alihkan ke login).
- Catatan: tombol Keluar kedua di halaman profil dikerjakan nanti di tugas Profil, jangan dikerjakan sekarang.
- Tulis satu feature test sederhana: setelah logout, GET ke /pengajuan di alihkan ke login.
```

**Cek manual:** klik Keluar dari akun admin dan akun user. Setelah itu tekan tombol Back di browser dan coba buka `/dashboard`, harus di lempar ke login.

---

## Prompt 6: US-15 Akses sesuai peran

```
TUGAS: User story US-15 (Akses sesuai peran). Baca bagian itu di docs/user-stories.md dan bagian 5 serta 9 di docs/PRD.md.

Kerjakan:
1. Audit semua rute buatan di routes/web.php. Pastikan:
   - GET /pengajuan dan POST /pengajuan: middleware auth + role:user
   - GET /dashboard dan PATCH /dashboard/pengajuan/{application}: middleware auth + role:admin
   - rute profile: middleware auth saja
   - tidak ada middleware "verified" di mana pun
2. Perilaku middleware role: tamu di arahkan ke login. Pengguna yang membuka halaman di luar perannya di alihkan ke halaman utama perannya (bukan error 403 atau 500).
3. Untuk rute PATCH dashboard yang belum ada controllernya, cukup daftarkan rute dan controller kosong yang mengembalikan redirect. Isinya dikerjakan nanti.
4. Tulis feature test untuk matriks akses: tamu, user, admin terhadap /pengajuan, /dashboard, /profile (GET), dan POST /pengajuan, PATCH /dashboard/pengajuan/1 sebagai user (harus di tolak/di alihkan, bukan sukses).

Jalankan php artisan test dan pastikan lulus. Jangan menambah package.
```

**Cek manual:** login sebagai user lalu ketik `/dashboard` di address bar, harus balik ke `/pengajuan`. Login sebagai admin lalu buka `/pengajuan`, harus balik ke `/dashboard`.

---

## Prompt 7: US-05 Form pengajuan

```
TUGAS: User story US-05 (Mengisi pengajuan). Baca bagian itu di docs/user-stories.md, bagian 6.3 dan 8 di docs/PRD.md.

Kerjakan:
1. Migration, model MembershipApplication, dan relasi sesuai tabel membership_applications di PRD bagian 8 (user_id, interest, motivation, experience, status default pending, admin_note, reviewed_by, reviewed_at). Relasi: User hasMany applications, Application belongsTo user dan belongsTo reviewer (User, lewat reviewed_by). Tambahkan method di User untuk mengambil pengajuan terbaru.
2. config/blug.php berisi array 'interests' dengan 4 placeholder, misalnya ['[Bidang 1]', '[Bidang 2]', '[Bidang 3]', '[Bidang 4]']. Form membaca pilihan dari config ini.
3. Form di resources/views/pengajuan/index.blade.php, kolom sempit 560px di tengah, judul dan satu kalimat penjelasan di atasnya. Field:
   - NIM: angka, 8 sampai 15 digit
   - Program studi: teks, wajib
   - Angkatan: pilihan tahun (dari tahun ini mundur 10 tahun)
   - Nomor WhatsApp: format 08xx atau 628xx
   - Bidang minat: pilihan dari config/blug.php
   - Motivasi bergabung: textarea, 50 sampai 1000 karakter
   - Pengalaman atau skill: textarea, opsional, maksimal 1000 karakter
   Nama dan email di tampilkan sebagai teks hanya baca (di ambil dari akun), bukan field input.
4. StoreMembershipApplicationRequest untuk validasi. Saat dikirim: simpan NIM, program studi, angkatan, dan nomor WA ke tabel users (data profil), dan buat baris baru di membership_applications dengan status pending. Lakukan keduanya dalam satu DB transaction.
5. Setelah sukses, redirect ke pengajuan.index dengan flash message sukses. Tampilan status yang rapi dikerjakan di tugas berikutnya, jadi untuk sementara cukup tampilkan alert "Pengajuan terkirim" dan sembunyikan form bila pengguna sudah punya pengajuan.
6. Galat per field di bawah field, isian lain tidak hilang. Ikuti docs/style.md.

Larangan ganda (pengajuan kedua) dikerjakan di tugas berikutnya.
```

**Cek manual:** isi form dengan data valid dan cek di database, baris baru di `membership_applications` berstatus `pending` dan kolom `nim` di `users` terisi. Coba kirim dengan field kosong, NIM berisi huruf, dan motivasi 10 karakter.

---

## Prompt 8: US-06 Status pengajuan

```
TUGAS: User story US-06 (Memantau status). Baca bagian itu di docs/user-stories.md dan bagian 6.3 serta 9 di docs/PRD.md.

Kerjakan halaman pengajuan.index dengan empat keadaan berdasarkan pengajuan TERAKHIR milik pengguna:
- Belum pernah mengajukan: tampilkan form (dari tugas sebelumnya).
- pending: kartu status "Menunggu" beserta ringkasan isian (hanya baca).
- approved: kartu status "Diterima" beserta catatan admin bila ada.
- rejected: kartu status "Ditolak" beserta catatan admin. Tombol "Ajukan Ulang" dikerjakan nanti, jangan di buat sekarang.

Gaya kartu sesuai docs/style.md bagian 5 ("Kartu status pengajuan"): kartu putih, garis kiri 4px sesuai warna status, label memakai class .status (status-pending, status-approved, status-rejected). Catatan admin di dalam blok berlatar --blug-bg.

ATURAN SERVER (wajib di tegakkan di sisi server, bukan hanya menyembunyikan tombol): bila pengguna punya pengajuan pending atau approved, POST ke pengajuan.store di tolak (redirect ke pengajuan.index dengan pesan galat, atau 403) dan TIDAK membuat baris baru. Tulis feature test yang mengirim POST langsung dalam keadaan pending dan approved, lalu memastikan jumlah baris tidak bertambah.
```

**Cek manual:** ubah kolom `status` di database secara manual (lewat HeidiSQL) ke `approved` dan `rejected` lalu muat ulang halaman, tampilannya harus berganti. Coba kirim POST kedua dengan akun yang masih `pending`.

---

## Prompt 9: US-07 Ajukan ulang

```
TUGAS: User story US-07 (Mengajukan ulang). Baca bagian itu di docs/user-stories.md dan bagian 9 di docs/PRD.md.

Kerjakan:
- Pada keadaan rejected di halaman pengajuan.index, tampilkan tombol "Ajukan Ulang" yang menuju GET /pengajuan?ulang=1.
- Parameter ?ulang=1 hanya berlaku bila pengajuan terakhir berstatus rejected. Dalam keadaan lain, parameter di abaikan.
- Form terbuka dengan isian dari pengajuan lama sebagai nilai awal (bidang minat, motivasi, pengalaman) dan data profil dari tabel users.
- Pengiriman membuat baris BARU di membership_applications. Riwayat lama tetap ada, tidak di timpa.
- Setelah terkirim, halaman menampilkan status pending untuk pengajuan yang baru.
- Pastikan aturan server dari tugas sebelumnya tetap berlaku (pengajuan kedua saat pending atau approved tetap di tolak).
- Tulis feature test: rejected lalu ajukan ulang menghasilkan 2 baris dengan status terakhir pending.
```

**Cek manual:** ubah status ke `rejected` di database, klik Ajukan Ulang, kirim, lalu cek bahwa ada dua baris untuk user itu.

---

## Prompt 10: US-08 Kartu ringkasan dashboard

```
TUGAS: User story US-08 (Melihat ringkasan). Baca bagian itu di docs/user-stories.md dan bagian 6.4 di docs/PRD.md.

Kerjakan bagian atas halaman dashboard (resources/views/admin/dashboard.blade.php) dengan controller DashboardController:
- Judul halaman "Dashboard".
- Empat kartu sejajar (2x2 di layar kecil): Total, Menunggu, Diterima, Ditolak. Label kecil monospace di atas, angka besar (2rem, tebal) di bawah.
- Kartu Total berlatar hitam dengan angka kuning. Tiga kartu lain putih dengan garis atas 3px berwarna status (kuning, zaitun, merah bata sesuai token di docs/style.md).
- Angka dihitung langsung dari database setiap halaman di muat. Pakai satu query agregat (groupBy status), jangan empat query terpisah.
- Tulis feature test: admin melihat angka yang cocok dengan data yang di buat di test.

Tabel pengajuan dikerjakan di tugas berikutnya. Di bawah kartu cukup beri placeholder kosong.
```

**Cek manual:** buka dashboard sebagai admin. Tambahkan atau ubah baris di database lalu muat ulang, angkanya harus ikut berubah.

---

## Prompt 11: US-09 Tabel pengajuan

```
TUGAS: User story US-09 (Melihat daftar pengajuan). Baca bagian itu di docs/user-stories.md dan bagian 6.4 di docs/PRD.md.

Kerjakan:
1. Tabel pengajuan di dashboard, di dalam kartu, di bungkus .table-responsive. Kolom: Nama, NIM, Prodi, Bidang minat, Tanggal masuk (format 04 Nov 2026), Status (badge .status), Aksi. Urutan terbaru di atas, pagination 10 baris per halaman dengan tampilan pagination Bootstrap. Gunakan eager loading (with('user')) supaya tidak ada N+1.
2. Kolom Aksi berisi tombol "Lihat" (btn-outline-dark kecil). Fungsinya dikerjakan di tugas modal, untuk sekarang tombol belum melakukan apa-apa.
3. Bila belum ada data, tampilkan baris kosong berisi "Belum ada pengajuan."
4. Buat factory untuk MembershipApplication dan seeder terpisah DummyApplicationSeeder yang membuat 25 pengajuan dengan status campuran (user dummy ikut di buat, role user). Seeder ini TIDAK di panggil dari DatabaseSeeder. Aku akan menjalankannya manual dengan: php artisan db:seed --class=DummyApplicationSeeder
5. Role user yang membuka dashboard tetap di alihkan ke pengajuan.index.
```

**Cek manual:** jalankan `php artisan db:seed --class=DummyApplicationSeeder`, lalu buka dashboard. Harus ada 10 baris, pagination ke halaman 2 dan 3, tabel bisa di geser di lebar 360px.

---

## Prompt 12: US-10 Cari dan filter

```
TUGAS: User story US-10 (Mencari dan menyaring). Baca bagian itu di docs/user-stories.md.

Kerjakan di atas tabel dashboard satu baris kontrol:
- Kolom cari (parameter q): mencari pada nama pengguna ATAU NIM, memakai LIKE lewat Eloquent (where dengan binding, bukan string mentah).
- Dropdown filter status (parameter status) dengan pilihan: Semua status, Menunggu, Diterima, Ditolak.
- Tombol "Terapkan" dan tautan "Reset" (muncul bila ada filter aktif).
- Pencarian dan filter bisa digabung. Keduanya tetap aktif saat pindah halaman pagination (withQueryString) dan tetap terisi di kontrolnya setelah halaman di muat.
- Nilai status di luar pending/approved/rejected di abaikan (anggap tanpa filter), tidak boleh error.
- Bila hasil kosong, tampilkan "Tidak ada pengajuan yang cocok."
- Kartu ringkasan tetap menghitung SEMUA data, tidak ikut terpengaruh filter.
- Form memakai method GET.
- Tulis feature test untuk cari nama, cari NIM, filter status, dan gabungannya.
```

**Cek manual:** cari sebagian nama, cari NIM, filter "Menunggu", gabungkan keduanya, lalu pindah ke halaman 2 dan pastikan filter tidak hilang.

---

## Prompt 13: US-11 Modal detail

```
TUGAS: User story US-11 (Membaca detail pengajuan). Baca bagian itu di docs/user-stories.md.

Kerjakan:
- Satu modal Bootstrap bersama (bukan satu modal per baris) yang terbuka saat tombol "Lihat" di klik, tanpa memuat ulang halaman dan tanpa rute baru.
- Data per baris di taruh di atribut data-* tombol (di escape dengan benar lewat Blade) lalu diisi ke modal dengan JavaScript memakai textContent, BUKAN innerHTML, supaya aman dari XSS.
- Isi modal: nama, email, NIM, program studi, angkatan, nomor WhatsApp, bidang minat, motivasi (pertahankan baris baru lewat CSS white-space: pre-line), pengalaman, tanggal masuk, status saat ini (badge .status), dan catatan admin bila ada.
- Gaya modal sesuai docs/style.md (garis tepi hitam, radius 10px). Tombol "Tutup" di bagian bawah.
- Area untuk tombol Terima dan Tolak dikerjakan di tugas berikutnya, jangan di buat sekarang.

JavaScript di taruh di resources/js (di impor dari app.js atau file terpisah yang dimuat Vite), bukan di dalam Blade.
```

**Cek manual:** klik Lihat di beberapa baris, isi modal harus berganti sesuai baris. Isi motivasi sebuah pengajuan dengan teks `<b>coba</b>` di database, tampilannya harus berupa teks biasa, bukan huruf tebal.

---

## Prompt 14: US-12 Terima atau tolak

```
TUGAS: User story US-12 (Menerima atau menolak). Baca bagian itu di docs/user-stories.md dan bagian 6.4 serta 9 di docs/PRD.md.

Kerjakan:
1. Di modal detail, tambahkan form keputusan: kolom catatan (textarea), tombol "Terima" (btn-primary) dan tombol "Tolak" (btn-outline-danger, tidak berisi penuh). Form mengirim PATCH ke route dashboard.pengajuan.update dengan field decision (approved atau rejected) dan admin_note.
2. Buat UpdateApplicationDecisionRequest: decision wajib dan hanya approved atau rejected, admin_note maksimal 1000 karakter dan WAJIB bila decision = rejected (pesan galat Indonesia yang jelas).
3. Controller menyimpan status, admin_note, reviewed_by (id admin yang login), dan reviewed_at (sekarang).
4. Admin boleh mengubah keputusan yang sudah di buat (untuk koreksi). Kolom catatan di modal terisi catatan sebelumnya.
5. Setelah simpan: redirect kembali ke dashboard dengan query pencarian, filter, dan halaman yang sama, plus flash sukses. Bila validasi gagal, modal terbuka lagi dengan galat tampil dan isian tidak hilang.
6. Hanya admin yang bisa. Pastikan user biasa tidak bisa memanggil route ini (sudah di atur middleware, tulis test untuk memastikannya).
7. Feature test: terima, tolak dengan catatan, tolak tanpa catatan (harus gagal), ubah keputusan, dan akses oleh user biasa.

Perhatian: pengajuan yang di ubah statusnya harus langsung terlihat oleh pendaftar di halaman pengajuan.index.
```

**Cek manual:** terima satu pengajuan, tolak satu dengan catatan, coba tolak tanpa catatan. Login sebagai pendaftar yang bersangkutan dan lihat status serta catatannya di `/pengajuan`.

---

## Prompt 15: US-13 Profil: data diri

```
TUGAS: User story US-13 (Mengubah data diri). Baca bagian itu di docs/user-stories.md dan bagian 6.5 di docs/PRD.md.

Tulis ulang halaman profil (route profile.edit dan profile.update) dengan Bootstrap dan docs/style.md. Hapus tampilan Breeze lama yang memakai Tailwind.
- Kolom sempit 560px di tengah. Kartu pertama: "Data diri".
- Field untuk semua peran: nama, email, nomor WhatsApp. Field tambahan hanya untuk role user: NIM, program studi, angkatan. Admin tidak melihat tiga field itu.
- Validasi lewat Form Request: email unik kecuali milik sendiri, format nomor WA dan NIM sama dengan form pengajuan.
- Hapus logika "reset email_verified_at saat email berubah" bawaan Breeze, karena verifikasi email tidak dipakai.
- Pesan sukses tampil setelah simpan (alert sesuai gaya docs/style.md).
- Perubahan profil TIDAK mengubah isi baris membership_applications yang sudah ada.
- Tambahkan tombol "Keluar" bergaya sekunder (btn-outline-dark) di bagian bawah halaman, berupa form POST ke route logout.
- Hapus bagian dan route "Hapus akun" bawaan Breeze (tidak ada di PRD).
- Ganti password dikerjakan di tugas berikutnya, jangan di buat sekarang.
- Feature test: ubah data diri berhasil, email bentrok di tolak, admin tidak bisa mengubah NIM lewat request manual.
```

**Cek manual:** ubah nama dan nomor WA, coba pakai email milik akun lain, login sebagai admin dan pastikan tiga field mahasiswa tidak muncul.

---

## Prompt 16: US-14 Profil: ganti password

```
TUGAS: User story US-14 (Mengganti password). Baca bagian itu di docs/user-stories.md.

Tambahkan kartu kedua "Ganti password" di halaman profil (di bawah kartu Data diri, di atas tombol Keluar), memakai route bawaan password.update:
- Field: password lama, password baru, konfirmasi password baru.
- Wajib password lama yang benar (rule current_password). Password baru minimal 8 karakter dan harus sama dengan konfirmasi.
- Setelah berhasil: pengguna tetap login, field di kosongkan, dan muncul pesan sukses.
- Galat tampil di bawah field terkait. Gunakan error bag yang tepat supaya galat password tidak bercampur dengan galat data diri.
- Pastikan password disimpan dalam bentuk hash.
- Feature test: sukses, password lama salah, konfirmasi tidak cocok, password baru kurang dari 8 karakter.
```

**Cek manual:** ganti password lalu logout dan login dengan password baru. Coba password lama yang salah dan konfirmasi yang beda.

---

## Prompt 17: Penutup (bahasa, pembersihan, uji akhir)

```
TUGAS: Pemolesan akhir. Ini kerjakan dalam urutan di bawah dan lapor di tiap bagian.

A. Bahasa Indonesia
- Set locale aplikasi ke 'id' (dan fallback_locale ke 'en') di config/app.php. Publikasikan file bahasa dengan php artisan lang:publish, lalu buat lang/id untuk validation, auth, passwords, dan pagination. Terjemahkan manual, tanpa package tambahan. Isi juga nama atribut (attributes) di validation supaya pesan terbaca natural, misalnya "NIM", "nomor WhatsApp", "program studi".
- Semua teks lain yang masih Inggris (judul, tombol, flash message) di Indonesiakan.

B. Pembersihan
- Hapus rute, controller, dan view bawaan Breeze yang tidak dipakai PRD: forgot-password, reset-password, verify-email, confirm-password, email verification notification. Pastikan tidak ada tautan yang mengarah ke sana.
- Hapus view atau komponen Blade bawaan Breeze yang sudah tidak di pakai (komponen Tailwind lama).

C. Pengecekan tampilan
- Periksa tiap halaman di lebar 360px, 768px, dan 1280px. Perbaiki yang melebar keluar layar, tertimpa, atau tidak terbaca.
- Pastikan tidak ada teks kuning di atas latar terang, dan fokus keyboard terlihat di semua input dan tombol.

D. Uji
- Jalankan php artisan test. Semua harus lulus.
- Pastikan ada test untuk: pengalihan setelah login per role, akses silang antar role, aturan satu pengajuan aktif, ajukan ulang setelah rejected, catatan wajib saat menolak, dan user tidak bisa melihat atau mengubah pengajuan milik orang lain. Tambahkan yang belum ada.

E. Laporan akhir
Tulis daftar hal yang masih harus aku isi manual (teks placeholder di Beranda, daftar bidang minat di config/blug.php, email dan password admin di .env) serta hal di PRD yang belum terpenuhi, kalau ada.
```

**Cek manual:** klik semua halaman satu per satu di HP atau lebar 360px, lalu coba satu alur lengkap dari daftar sampai di terima admin.

---

## Prompt Koreksi (pakai kapan saja hasilnya kurang pas)

```
Ada yang kurang pas di tugas yang baru selesai. Perbaiki HANYA bagian ini, jangan menyentuh hal lain:

[Tulis apa yang salah, misalnya: "Tombol Masuk di navbar teksnya putih, harusnya hitam di atas latar kuning."]

Acuan: [sebut bagian docs, misalnya "docs/style.md bagian 5, Tombol"]
Yang terlihat sekarang: [jelaskan atau tempel pesan error]
Yang seharusnya: [jelaskan]

Setelah selesai, laporkan dengan format yang sama (file yang berubah, cara uji, hal yang belum beres) lalu berhenti.
```
