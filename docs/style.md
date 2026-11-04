# style.md: Panduan Tampilan Website UKM BLUG

Dasar: **Bootstrap 5**, tapi di kustomisasi supaya tidak terlihat seperti template bawaan. Palet utamanya **kuning dan hitam** di atas latar krem hangat. Garis tipis, hampir tanpa bayangan, dan sedikit sentuhan monospace sebagai ciri komunitas teknologi.

Kuning dan hitam itu kontras tinggi dan gampang jadi norak kalau dipakai bersamaan di semua tempat. Karena itu aturannya begini: **hitam yang memegang teks dan struktur, kuning hanya untuk aksi dan penanda**. Kuning tidak pernah dipakai sebagai warna teks di atas latar terang, karena kontrasnya terlalu rendah.

---

## 1. Warna

| Token | Hex | Dipakai untuk |
|---|---|---|
| `--blug-bg` | `#FAF7EC` | Latar halaman (krem, bukan putih murni) |
| `--blug-surface` | `#FFFFFF` | Kartu, form, tabel, modal |
| `--blug-ink` | `#141414` | Teks utama, judul, navbar, tombol sekunder |
| `--blug-muted` | `#5C584B` | Teks pendukung, label, placeholder |
| `--blug-line` | `#E6E0CC` | Garis tepi, pemisah |
| `--blug-accent` | `#F5C518` | Tombol utama, sorotan, penanda aktif |
| `--blug-accent-dark` | `#E0B000` | Hover tombol utama |
| `--blug-accent-soft` | `#FFF4C2` | Latar lembut aksen, status menunggu |
| `--blug-accent-ink` | `#6B5000` | Teks di atas latar kuning lembut |
| `--blug-ok` | `#4F5A14` | Teks status diterima (hijau zaitun) |
| `--blug-ok-soft` | `#EEF1D6` | Latar status diterima |
| `--blug-danger` | `#9C3A1E` | Teks status ditolak dan galat (merah bata) |
| `--blug-danger-soft` | `#F9E3DA` | Latar status ditolak dan alert galat |

Aturan pemakaian:
- **Tombol utama:** latar kuning, teks hitam (kontras sekitar 11:1). Jangan teks putih di atas kuning.
- **Teks dan tautan:** hitam. Tautan di beri garis bawah, hover-nya berlatar kuning tipis.
- **Kuning di atas latar terang** hanya untuk bidang (tombol, sorotan, garis), bukan huruf.
- **Navbar dan footer:** latar hitam, teks krem, nama merek kuning. Ini membuat kuning terasa sebagai "lampu" di atas dasar gelap.
- Warna status sengaja di buat berbeda dari aksen: menunggu memakai kuning lembut, diterima zaitun, ditolak merah bata. Ketiganya tetap serumpun dengan palet hangat.
- Warna tidak boleh jadi satu-satunya pembeda status. Selalu sertakan teks label.
- Semua pasangan teks dan latar di atas lolos kontras 4.5:1.

## 2. Tipografi

| Peran | Font | Catatan |
|---|---|---|
| Teks dan judul | **Plus Jakarta Sans** | 400, 500, 700 |
| Label kecil, nama merek, angka statistik, tag status | **JetBrains Mono** | 500 |
| Cadangan | `system-ui, sans-serif` / `ui-monospace, monospace` | |

Skala ukuran:

| Elemen | Ukuran | Berat |
|---|---|---|
| Judul hero (h1) | 2.5rem (mobile 2rem) | 700 |
| Judul bagian (h2) | 1.75rem | 700 |
| Sub-judul (h3) | 1.125rem | 700 |
| Teks isi | 1rem, tinggi baris 1.65 | 400 |
| Teks kecil | 0.875rem | 400 |
| Label monospace | 0.75rem, huruf kapital, jarak huruf 0.06em | 500 |

Muat font di `<head>` layout utama:

```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,700|jetbrains-mono:500" rel="stylesheet">
```

## 3. Jarak dan tata letak

- Kelipatan 4px. Pakai utilitas spacing Bootstrap (`1` = 4px, `2` = 8px, `3` = 16px, `4` = 24px, `5` = 48px).
- Lebar konten maksimal **1080px**. Halaman form dan profil memakai kolom sempit **560px** di tengah.
- Jarak antar bagian di Beranda: 80px (mobile 56px).
- Radius: **6px** untuk tombol dan input, **10px** untuk kartu dan modal.
- Bayangan: hampir tidak ada. Pemisah di buat dari garis 1px `--blug-line`. Satu-satunya bayangan ada di modal.

## 4. File CSS dasar

Letakkan di `resources/css/app.css`, di muat setelah Bootstrap. File ini menimpa variabel Bootstrap sehingga komponen bawaan ikut berubah warna dan bentuk.

```css
:root {
  --blug-bg: #FAF7EC;
  --blug-surface: #FFFFFF;
  --blug-ink: #141414;
  --blug-muted: #5C584B;
  --blug-line: #E6E0CC;
  --blug-accent: #F5C518;
  --blug-accent-dark: #E0B000;
  --blug-accent-soft: #FFF4C2;
  --blug-accent-ink: #6B5000;
  --blug-ok: #4F5A14;
  --blug-ok-soft: #EEF1D6;
  --blug-danger: #9C3A1E;
  --blug-danger-soft: #F9E3DA;

  --font-sans: "Plus Jakarta Sans", system-ui, sans-serif;
  --font-mono: "JetBrains Mono", ui-monospace, monospace;

  /* timpa variabel Bootstrap */
  --bs-body-font-family: var(--font-sans);
  --bs-body-bg: var(--blug-bg);
  --bs-body-color: var(--blug-ink);
  --bs-secondary-color: var(--blug-muted);
  --bs-border-color: var(--blug-line);
  --bs-border-radius: 6px;
  --bs-border-radius-lg: 10px;
  --bs-link-color: var(--blug-ink);
  --bs-link-hover-color: var(--blug-ink);
  --bs-primary: var(--blug-accent);
  --bs-primary-rgb: 245, 197, 24;
}

body { line-height: 1.65; }
h1, h2, h3, h4 { font-weight: 700; letter-spacing: -0.01em; }

.container { max-width: 1080px; }
.container-narrow { max-width: 560px; margin-inline: auto; }

/* tautan: hitam, garis bawah, hover berlatar kuning */
a { text-decoration: underline; text-underline-offset: 3px; }
a:hover { background: var(--blug-accent); text-decoration-color: transparent; }

/* sorotan kata di judul (marker kuning) */
.mark-yellow {
  background: linear-gradient(transparent 55%, var(--blug-accent) 55%);
  padding: 0 0.1em;
}

/* label kecil gaya terminal */
.label-mono {
  font-family: var(--font-mono);
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--blug-muted);
}

/* tombol */
.btn { border-radius: 6px; font-weight: 700; padding: 0.55rem 1.1rem; }
.btn-primary {
  --bs-btn-color: var(--blug-ink);
  --bs-btn-bg: var(--blug-accent);
  --bs-btn-border-color: var(--blug-ink);
  --bs-btn-hover-color: var(--blug-ink);
  --bs-btn-hover-bg: var(--blug-accent-dark);
  --bs-btn-hover-border-color: var(--blug-ink);
  --bs-btn-active-color: var(--blug-ink);
  --bs-btn-active-bg: var(--blug-accent-dark);
  --bs-btn-active-border-color: var(--blug-ink);
  --bs-btn-focus-shadow-rgb: 20, 20, 20;
}
.btn-outline-dark {
  --bs-btn-color: var(--blug-ink);
  --bs-btn-border-color: var(--blug-ink);
  --bs-btn-hover-color: var(--blug-accent);
  --bs-btn-hover-bg: var(--blug-ink);
  --bs-btn-hover-border-color: var(--blug-ink);
}

/* navbar dan footer gelap */
.navbar-blug, .footer-blug {
  background: var(--blug-ink);
  color: #FAF7EC;
}
.navbar-blug a, .footer-blug a { color: #FAF7EC; text-decoration: none; }
.navbar-blug a:hover, .footer-blug a:hover { background: transparent; color: var(--blug-accent); }
.navbar-blug .nav-link.active { color: var(--blug-accent); box-shadow: inset 0 -2px 0 var(--blug-accent); }
.brand-blug {
  font-family: var(--font-mono);
  font-weight: 500;
  color: var(--blug-accent) !important;
}

/* kartu */
.card {
  background: var(--blug-surface);
  border: 1px solid var(--blug-line);
  border-radius: 10px;
  box-shadow: none;
}

/* form */
.form-label { font-weight: 500; font-size: 0.875rem; margin-bottom: 0.35rem; }
.form-control, .form-select {
  border-color: #CFC8AE;
  border-radius: 6px;
  padding: 0.6rem 0.8rem;
}
.form-control:focus, .form-select:focus {
  border-color: var(--blug-ink);
  box-shadow: 0 0 0 3px rgba(245, 197, 24, 0.55);
}
.btn:focus-visible { outline: 2px solid var(--blug-ink); outline-offset: 2px; }

/* tabel */
.table { --bs-table-bg: transparent; font-size: 0.9375rem; }
.table thead th {
  font-family: var(--font-mono);
  font-size: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--blug-muted);
  border-bottom: 2px solid var(--blug-ink);
}
.table td { vertical-align: middle; border-color: var(--blug-line); }
.table-hover > tbody > tr:hover > * { --bs-table-hover-bg: var(--blug-accent-soft); }

/* badge status */
.status {
  display: inline-block;
  font-family: var(--font-mono);
  font-size: 0.75rem;
  font-weight: 500;
  padding: 0.2rem 0.55rem;
  border-radius: 4px;
}
.status-pending  { background: var(--blug-accent-soft); color: var(--blug-accent-ink); }
.status-approved { background: var(--blug-ok-soft);     color: var(--blug-ok); }
.status-rejected { background: var(--blug-danger-soft); color: var(--blug-danger); }

/* modal */
.modal-content { border: 1px solid var(--blug-ink); border-radius: 10px; }
```

## 5. Komponen

**Navbar**
- Latar hitam `--blug-ink`, tinggi 64px, tanpa bayangan.
- Kiri: nama merek `blug_` dalam JetBrains Mono, huruf kecil, warna kuning. Tanda `_` seperti kursor terminal. Ini satu-satunya "ornamen" di seluruh situs.
- Kanan: tautan krem sesuai peran (Beranda, Pengajuan atau Dashboard, Profil) dan tombol Masuk atau Keluar.
- Tautan aktif: teks kuning dengan garis bawah 2px kuning.
- Mobile: collapse bawaan Bootstrap.

**Tombol**
- Utama: isi kuning, teks dan garis tepi hitam. Maksimal satu tombol utama per tampilan.
- Sekunder: `btn-outline-dark`, garis hitam tanpa isi. Saat hover jadi hitam dengan teks kuning.
- Aksi berbahaya (Tolak): `btn-outline-danger` dengan warna `--blug-danger`. Jangan di isi penuh.

**Form**
- Satu kolom. Label di atas input. Teks bantu (`.form-text`) memakai `--blug-muted`.
- Fokus input: garis tepi hitam dengan cincin kuning, jadi terlihat jelas.
- Galat tampil di bawah field dengan teks `--blug-danger`.

**Kartu status pengajuan**
- Kartu putih dengan garis kiri 4px sesuai warna status (kuning, zaitun, atau merah bata). Label status memakai `.status`.
- Isi: judul status, satu kalimat penjelasan, catatan admin (bila ada) di dalam blok berlatar `--blug-bg`.

**Kartu ringkasan dashboard**
- Empat kartu sejajar (2x2 di mobile). Label monospace kecil di atas, angka besar 2rem berat 700 di bawah.
- Kartu total berlatar hitam dengan angka kuning, sebagai pusat perhatian. Tiga kartu lain putih dengan garis atas 3px berwarna status.

**Alert**
- Sukses: latar `--blug-ok-soft`, teks `--blug-ok`.
- Info atau peringatan: latar `--blug-accent-soft`, teks `--blug-accent-ink`.
- Galat: latar `--blug-danger-soft`, teks `--blug-danger`.
- Tanpa garis tepi tebal, radius 6px.

## 6. Arahan per halaman

| Halaman | Arahan |
|---|---|
| Beranda | Hero rata kiri di atas latar krem: judul besar dengan satu frasa di beri `.mark-yellow`, satu paragraf pendek, tombol kuning. Satu bagian di tengah (misalnya "Cara bergabung") berlatar hitam dengan teks krem dan angka langkah kuning supaya ada ritme gelap dan terang. Footer hitam. Tanpa foto stok. |
| Login dan Daftar | Satu kartu putih di tengah (lebar 420px) di atas latar krem. Dua tab teks di atas kartu, tab aktif bergaris bawah kuning tebal. |
| Pengajuan | Kolom sempit 560px. Di atas: judul dan penjelasan singkat. Di bawah: form atau kartu status sesuai keadaan. |
| Dashboard | Lebar penuh container. Urutan dari atas: judul, empat kartu ringkasan, baris cari dan filter, tabel dalam kartu. Detail dan keputusan di dalam modal. |
| Profil | Kolom sempit 560px. Dua kartu bertumpuk: Data diri, lalu Ganti password. Tombol keluar di bagian bawah, gaya sekunder. |

## 7. Responsif

- Titik patah memakai bawaan Bootstrap (`sm` 576, `md` 768, `lg` 992).
- Target utama: 360px sampai 1280px.
- Tabel dashboard di bungkus `.table-responsive`.
- Tombol di form menjadi lebar penuh di bawah 576px.

## 8. Aksesibilitas

- Fokus keyboard selalu terlihat: cincin kuning dengan tepi hitam pada input, outline hitam pada tombol.
- Setiap input punya `<label>` yang terhubung lewat `for`.
- Status tidak hanya dibedakan lewat warna, selalu ada teks.
- Ukuran area ketuk minimal 40px untuk tombol dan tautan navigasi.
- Jangan menaruh teks kuning di atas latar putih atau krem. Teks kuning hanya boleh di atas latar hitam.

## 9. Boleh dan jangan

**Boleh**
- Memakai komponen Bootstrap (grid, modal, tab, collapse, pagination) lalu menimpa variabelnya.
- Memakai whitespace yang lega.
- Memakai kuning sebagai sorotan kecil: marker di judul, garis aktif, tombol utama.

**Jangan**
- Memakai biru Bootstrap bawaan, atau `btn-info` dan `btn-warning`.
- Menambah gradien, bayangan tebal, atau ilustrasi dekoratif.
- Membuat area kuning yang luas. Kuning yang terlalu banyak membuat mata cepat lelah dan menghilangkan fungsi penandanya.
- Memakai lebih dari dua font.
- Menaruh kartu di dalam kartu.
- Menambah ikon di setiap tombol dan judul. Pakai ikon hanya bila memperjelas makna.

## 10. Setup cepat

```bash
npm install bootstrap @popperjs/core
```

`resources/js/app.js`:

```js
import 'bootstrap';
```

`resources/css/app.css` bagian paling atas (sebelum isi bagian 4):

```css
@import 'bootstrap/dist/css/bootstrap.min.css';
```

Layout Blade memuat aset dengan `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
