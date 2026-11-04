<x-app-layout title="Beranda">
    @php
        $daftarUrl = route('register');
        if (auth()->check()) {
            $daftarUrl = auth()->user()->role === 'admin' ? route('dashboard') : route('pengajuan.index');
        }
    @endphp

    {{-- Hero Section --}}
    <section class="section-spacing pt-5">
        <div class="container">
            <div class="label-mono mb-3">[Komunitas Linux &amp; Sumber Terbuka]</div>
            <h1 class="display-4 fw-bold mb-3" style="max-width: 820px;">
                Wadah Kolaborasi dan Belajar Teknologi Bersama <span class="mark-yellow">BLUG</span>
            </h1>
            <p class="lead text-secondary mb-4" style="max-width: 680px;">
                [Deskripsi singkat satu kalimat mengenai semangat belajar, eksplorasi sistem operasi, dan teknologi terbuka di lingkungan kampus.]
            </p>
            <div>
                <a href="{{ $daftarUrl }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
            </div>
        </div>
    </section>

    {{-- Section: Apa itu BLUG? --}}
    <section class="bg-white border-top border-bottom section-spacing">
        <div class="container">
            <div class="label-mono mb-2">[Profil Organisasi]</div>
            <h2 class="h2 fw-bold mb-4">Apa itu BLUG?</h2>
            <div class="text-secondary" style="max-width: 760px;">
                <p class="mb-3">
                    [Paragraf pertama: penjelasan resmi mengenai profil, kepanjangan, dan sejarah singkat berdirinya BLUG sebagai unit kegiatan mahasiswa di bidang teknologi.]
                </p>
                <p class="mb-0">
                    [Paragraf kedua: penjelasan mengenai fokus keilmuan, lingkungan belajar yang inklusif, serta manfaat bergabung bagi pengembangan keterampilan anggota.]
                </p>
            </div>
        </div>
    </section>

    {{-- Section: Yang kami lakukan --}}
    <section class="section-spacing">
        <div class="container">
            <div class="label-mono mb-2">[Aktivitas &amp; Program]</div>
            <h2 class="h2 fw-bold mb-4">Yang kami lakukan</h2>
            
            <div class="border-top" style="border-color: var(--blug-line) !important;">
                <div class="py-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2" style="border-color: var(--blug-line) !important;">
                    <div>
                        <h3 class="h5 fw-bold mb-1">[Workshop &amp; Pelatihan Rutin]</h3>
                        <p class="text-secondary mb-0">[Deskripsi singkat kegiatan workshop teknis seputar Linux, pemrograman, dan infrastruktur sistem.]</p>
                    </div>
                    <span class="label-mono text-nowrap">[Rutin Bulanan]</span>
                </div>

                <div class="py-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2" style="border-color: var(--blug-line) !important;">
                    <div>
                        <h3 class="h5 fw-bold mb-1">[Sharing Session &amp; Diskusi Teknologi]</h3>
                        <p class="text-secondary mb-0">[Deskripsi sesi berbagi pengalaman anggota seputar tren teknologi terkini dan problem solving.]</p>
                    </div>
                    <span class="label-mono text-nowrap">[Dua Mingguan]</span>
                </div>

                <div class="py-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2" style="border-color: var(--blug-line) !important;">
                    <div>
                        <h3 class="h5 fw-bold mb-1">[Proyek Kolaborasi Open Source]</h3>
                        <p class="text-secondary mb-0">[Deskripsi pengerjaan proyek piranti lunak terbuka bersama untuk portofolio dan kerja tim anggota.]</p>
                    </div>
                    <span class="label-mono text-nowrap">[Per Semester]</span>
                </div>

                <div class="py-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2" style="border-color: var(--blug-line) !important;">
                    <div>
                        <h3 class="h5 fw-bold mb-1">[Seminar &amp; Acara Tahunan]</h3>
                        <p class="text-secondary mb-0">[Deskripsi kegiatan besar tahunan yang menghadirkan praktisi industri untuk wawasan karier teknologi.]</p>
                    </div>
                    <span class="label-mono text-nowrap">[Tahunan]</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Cara bergabung --}}
    <section class="section-spacing text-light" style="background: var(--blug-ink);">
        <div class="container">
            <div class="label-mono text-warning mb-2">[Alur Pendaftaran]</div>
            <h2 class="h2 fw-bold text-light mb-5">Cara bergabung</h2>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="step-number mb-2">01</div>
                    <h3 class="h5 fw-bold text-light mb-2">Buat akun</h3>
                    <p class="text-secondary mb-0">[Buat akun baru melalui menu pendaftaran dengan mengisi nama lengkap, email, dan kata sandi.]</p>
                </div>
                <div class="col-md-4">
                    <div class="step-number mb-2">02</div>
                    <h3 class="h5 fw-bold text-light mb-2">Isi pengajuan</h3>
                    <p class="text-secondary mb-0">[Lengkapi formulir pengajuan keanggotaan dengan data diri, bidang minat, motivasi, dan pengalaman.]</p>
                </div>
                <div class="col-md-4">
                    <div class="step-number mb-2">03</div>
                    <h3 class="h5 fw-bold text-light mb-2">Tunggu keputusan</h3>
                    <p class="text-secondary mb-0">[Pengurus akan meninjau berkas pengajuanmu. Pantau pembaruan status langsung melalui halaman pengajuan.]</p>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ $daftarUrl }}" class="btn btn-primary btn-lg">Daftar Sekarang</a>
            </div>
        </div>
    </section>
</x-app-layout>
