<x-app-layout title="Pengajuan Keanggotaan">
    <div class="container py-5">
        <div class="container-narrow">

            {{-- Judul dan Penjelasan --}}
            <div class="mb-4">
                <h1 class="h2 fw-bold mb-1">Pengajuan Keanggotaan</h1>
                <p class="text-secondary mb-0">Isi formulir pendaftaran untuk bergabung sebagai anggota UKM BLUG.</p>
            </div>

            {{-- Flash: sukses --}}
            @if (session('success'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Flash: galat (penolakan server saat pending/approved) --}}
            @if (session('error'))
                <div class="alert alert-danger mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @php $status = $latestApplication?->status; @endphp

            {{-- ============================================================
                 KEADAAN 1: belum pernah mengajukan → tampilkan form
            ================================================================ --}}
            @if (! $latestApplication)
                <div class="card p-4">
                    {{-- Informasi Akun (Hanya Baca) --}}
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label text-secondary small mb-1">Nama Lengkap</label>
                            <div class="fw-semibold text-dark">{{ $user->name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-secondary small mb-1">Email</label>
                            <div class="fw-semibold text-dark">{{ $user->email }}</div>
                        </div>
                    </div>

                    <hr class="my-3" style="border-color: var(--blug-line);">

                    <form method="POST" action="{{ route('pengajuan.store') }}" novalidate>
                        @csrf

                        {{-- NIM --}}
                        <div class="mb-3">
                            <label for="nim" class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                            <input id="nim" type="text" name="nim"
                                value="{{ old('nim', $user->nim) }}"
                                class="form-control @error('nim') is-invalid @enderror"
                                placeholder="8 - 15 digit angka" required>
                            @error('nim')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Program Studi --}}
                        <div class="mb-3">
                            <label for="study_program" class="form-label">Program Studi</label>
                            <input id="study_program" type="text" name="study_program"
                                value="{{ old('study_program', $user->study_program) }}"
                                class="form-control @error('study_program') is-invalid @enderror"
                                placeholder="Contoh: Teknik Informatika" required>
                            @error('study_program')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Angkatan --}}
                        <div class="mb-3">
                            <label for="batch_year" class="form-label">Angkatan</label>
                            <select id="batch_year" name="batch_year"
                                class="form-select @error('batch_year') is-invalid @enderror" required>
                                <option value="">Pilih Tahun Angkatan</option>
                                @foreach ($batchYears as $year)
                                    <option value="{{ $year }}"
                                        {{ (string) old('batch_year', $user->batch_year) === (string) $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('batch_year')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor WhatsApp</label>
                            <input id="phone" type="text" name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror"
                                placeholder="Format: 08xx atau 628xx" required>
                            @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Bidang Minat --}}
                        <div class="mb-3">
                            <label for="interest" class="form-label">Bidang Minat</label>
                            <select id="interest" name="interest"
                                class="form-select @error('interest') is-invalid @enderror" required>
                                <option value="">Pilih Bidang Minat</option>
                                @foreach ($interests as $item)
                                    <option value="{{ $item }}" {{ old('interest') === $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                            @error('interest')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Motivasi Bergabung --}}
                        <div class="mb-3">
                            <label for="motivation" class="form-label">Motivasi Bergabung</label>
                            <textarea id="motivation" name="motivation" rows="4"
                                class="form-control @error('motivation') is-invalid @enderror"
                                placeholder="Jelaskan alasan dan motivasimu ingin bergabung dengan BLUG (50 - 1000 karakter)"
                                required>{{ old('motivation') }}</textarea>
                            <div class="form-text text-secondary small">50 sampai 1000 karakter</div>
                            @error('motivation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pengalaman atau Skill --}}
                        <div class="mb-4">
                            <label for="experience" class="form-label">
                                Pengalaman atau Skill
                                <span class="text-secondary fw-normal">(Opsional)</span>
                            </label>
                            <textarea id="experience" name="experience" rows="3"
                                class="form-control @error('experience') is-invalid @enderror"
                                placeholder="Ceritakan pengalaman teknis, organisasi, atau keterampilan yang kamu miliki"
                            >{{ old('experience') }}</textarea>
                            <div class="form-text text-secondary small">Maksimal 1000 karakter</div>
                            @error('experience')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tombol Kirim --}}
                        <div>
                            <button type="submit" class="btn btn-primary w-100">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>

            {{-- ============================================================
                 KEADAAN 2: pending → kartu "Menunggu" + ringkasan isian
            ================================================================ --}}
            @elseif ($status === 'pending')
                <div class="card-status card-status-pending">
                    {{-- Header kartu --}}
                    <div class="mb-1">
                        <span class="status status-pending">Menunggu</span>
                    </div>
                    <h2 class="h5 fw-bold mb-1 mt-2">Pengajuan sedang ditinjau</h2>
                    <p class="text-secondary mb-0">
                        Pengajuan keanggotaan Anda telah diterima dan sedang menunggu
                        keputusan dari admin. Halaman ini akan diperbarui saat keputusan sudah ada.
                    </p>

                    <hr class="my-4" style="border-color: var(--blug-line);">

                    @include('pengajuan._ringkasan')
                </div>

            {{-- ============================================================
                 KEADAAN 3: approved → kartu "Diterima"
            ================================================================ --}}
            @elseif ($status === 'approved')
                <div class="card-status card-status-approved">
                    <div class="mb-1">
                        <span class="status status-approved">Diterima</span>
                    </div>
                    <h2 class="h5 fw-bold mb-1 mt-2">Selamat, pengajuan Anda diterima!</h2>
                    <p class="text-secondary mb-0">
                        Anda kini resmi menjadi anggota UKM BLUG. Pantau informasi selanjutnya dari pengurus.
                    </p>

                    {{-- Catatan admin --}}
                    <dl class="detail-list mt-4">
                        <div class="detail-row detail-row-block">
                            <dt>Catatan admin</dt>
                            <dd>
                                @if ($latestApplication->admin_note)
                                    <div class="text-block wrap-text mt-1">{{ $latestApplication->admin_note }}</div>
                                @else
                                    <p class="text-secondary fst-italic mb-0 mt-1" style="font-size: 0.9375rem;">Tidak ada catatan.</p>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if ($latestApplication->reviewed_at)
                        <p class="text-secondary mt-3 mb-0" style="font-size: 0.875rem;">
                            Diputuskan pada {{ $latestApplication->reviewed_at->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    @endif
                </div>

            {{-- ============================================================
                 KEADAAN 4: rejected → kartu "Ditolak"
                 (Tombol "Ajukan Ulang" dikerjakan di US-07)
            ================================================================ --}}
            @elseif ($status === 'rejected')
                <div class="card-status card-status-rejected">
                    <div class="mb-1">
                        <span class="status status-rejected">Ditolak</span>
                    </div>
                    <h2 class="h5 fw-bold mb-1 mt-2">Pengajuan tidak dapat diterima saat ini</h2>
                    <p class="text-secondary mb-0">
                        Pengajuan keanggotaan Anda telah ditinjau dan tidak dapat diterima
                        pada periode ini. Baca catatan admin di bawah untuk informasi lebih lanjut.
                    </p>

                    {{-- Catatan admin --}}
                    <dl class="detail-list mt-4">
                        <div class="detail-row detail-row-block">
                            <dt>Catatan admin</dt>
                            <dd>
                                @if ($latestApplication->admin_note)
                                    <div class="text-block wrap-text mt-1">{{ $latestApplication->admin_note }}</div>
                                @else
                                    <p class="text-secondary fst-italic mb-0 mt-1" style="font-size: 0.9375rem;">Tidak ada catatan.</p>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if ($latestApplication->reviewed_at)
                        <p class="text-secondary mt-3 mb-0" style="font-size: 0.875rem;">
                            Diputuskan pada {{ $latestApplication->reviewed_at->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    @endif

                    {{-- Tombol "Ajukan Ulang" dikerjakan di US-07, tidak dibuat di sini --}}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
