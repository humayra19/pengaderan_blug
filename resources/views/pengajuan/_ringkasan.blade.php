{{--
  Partial: ringkasan pengajuan hanya baca.
  Dipakai di keadaan pending, approved, dan rejected.

  Variabel yang dibutuhkan:
    $user               : App\Models\User
    $latestApplication  : App\Models\MembershipApplication
--}}

{{-- ==============================
     Bagian 1: Data pendaftar
================================= --}}
<h3 class="detail-heading">Data pendaftar</h3>

<dl class="detail-list mt-2 mb-0">
    <div class="detail-row">
        <dt>Nama lengkap</dt>
        <dd>{{ $user->name }}</dd>
    </div>
    <div class="detail-row">
        <dt>Email</dt>
        <dd>{{ $user->email }}</dd>
    </div>
    <div class="detail-row">
        <dt>NIM</dt>
        <dd>{{ $user->nim ?? '—' }}</dd>
    </div>
    <div class="detail-row">
        <dt>Program studi</dt>
        <dd>{{ $user->study_program ?? '—' }}</dd>
    </div>
    <div class="detail-row">
        <dt>Angkatan</dt>
        <dd>{{ $user->batch_year ?? '—' }}</dd>
    </div>
    <div class="detail-row">
        <dt>Nomor WhatsApp</dt>
        <dd>{{ $user->phone ?? '—' }}</dd>
    </div>
</dl>

{{-- ==============================
     Bagian 2: Isi pengajuan
================================= --}}
<h3 class="detail-heading detail-heading-next">Isi pengajuan</h3>

<dl class="detail-list mt-2 mb-0">
    {{-- Bidang minat: nilai pendek, cukup dua kolom biasa --}}
    <div class="detail-row">
        <dt>Bidang minat</dt>
        <dd>{{ $latestApplication->interest }}</dd>
    </div>

    {{-- Motivasi: teks bisa panjang, pakai varian satu kolom --}}
    <div class="detail-row detail-row-block">
        <dt>Motivasi bergabung</dt>
        <dd>
            <div class="text-block wrap-text mt-1">{{ $latestApplication->motivation }}</div>
        </dd>
    </div>

    {{-- Pengalaman: teks bisa panjang, pakai varian satu kolom --}}
    <div class="detail-row detail-row-block">
        <dt>Pengalaman atau skill</dt>
        <dd>
            @if ($latestApplication->experience)
                <div class="text-block wrap-text mt-1">{{ $latestApplication->experience }}</div>
            @else
                <p class="text-secondary fst-italic mb-0 mt-1" style="font-size: 0.9375rem;">Tidak diisi</p>
            @endif
        </dd>
    </div>
</dl>

{{-- ---- Tanggal kirim ---- --}}
<p class="text-secondary mt-3 mb-0" style="font-size: 0.875rem;">
    Dikirim pada {{ $latestApplication->created_at->translatedFormat('d F Y, H:i') }} WIB
</p>
