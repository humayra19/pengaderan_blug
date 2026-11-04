<x-app-layout title="Pengajuan Keanggotaan">
    <div class="container py-5">
        <div class="container-narrow">
            {{-- Judul dan Penjelasan --}}
            <div class="mb-4">
                <h1 class="h2 fw-bold mb-1">Pengajuan Keanggotaan</h1>
                <p class="text-secondary mb-0">Isi formulir pendaftaran untuk bergabung sebagai anggota UKM BLUG.</p>
            </div>

            {{-- Flash Message Sukses --}}
            @if (session('success'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($latestApplication)
                {{-- Alert jika sudah punya pengajuan --}}
                <div class="alert alert-info py-4" role="alert">
                    <h2 class="h5 fw-bold mb-1">Pengajuan terkirim</h2>
                    <p class="mb-0 text-secondary">
                        Pengajuan keanggotaan Anda telah berhasil dikirim dan saat ini berstatus <strong>Menunggu</strong> peninjauan admin.
                    </p>
                </div>
            @else
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
                            <input 
                                id="nim" 
                                type="text" 
                                name="nim" 
                                value="{{ old('nim', $user->nim) }}" 
                                class="form-control @error('nim') is-invalid @enderror" 
                                placeholder="8 - 15 digit angka" 
                                required
                            >
                            @error('nim')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Program Studi --}}
                        <div class="mb-3">
                            <label for="study_program" class="form-label">Program Studi</label>
                            <input 
                                id="study_program" 
                                type="text" 
                                name="study_program" 
                                value="{{ old('study_program', $user->study_program) }}" 
                                class="form-control @error('study_program') is-invalid @enderror" 
                                placeholder="Contoh: Teknik Informatika" 
                                required
                            >
                            @error('study_program')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Angkatan --}}
                        <div class="mb-3">
                            <label for="batch_year" class="form-label">Angkatan</label>
                            <select 
                                id="batch_year" 
                                name="batch_year" 
                                class="form-select @error('batch_year') is-invalid @enderror" 
                                required
                            >
                                <option value="">Pilih Tahun Angkatan</option>
                                @foreach ($batchYears as $year)
                                    <option value="{{ $year }}" {{ (string) old('batch_year', $user->batch_year) === (string) $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('batch_year')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Nomor WhatsApp --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor WhatsApp</label>
                            <input 
                                id="phone" 
                                type="text" 
                                name="phone" 
                                value="{{ old('phone', $user->phone) }}" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                placeholder="Format: 08xx atau 628xx" 
                                required
                            >
                            @error('phone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Bidang Minat --}}
                        <div class="mb-3">
                            <label for="interest" class="form-label">Bidang Minat</label>
                            <select 
                                id="interest" 
                                name="interest" 
                                class="form-select @error('interest') is-invalid @enderror" 
                                required
                            >
                                <option value="">Pilih Bidang Minat</option>
                                @foreach ($interests as $item)
                                    <option value="{{ $item }}" {{ old('interest') === $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                            @error('interest')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Motivasi Bergabung --}}
                        <div class="mb-3">
                            <label for="motivation" class="form-label">Motivasi Bergabung</label>
                            <textarea 
                                id="motivation" 
                                name="motivation" 
                                rows="4" 
                                class="form-control @error('motivation') is-invalid @enderror" 
                                placeholder="Jelaskan alasan dan motivasimu ingin bergabung dengan BLUG (50 - 1000 karakter)" 
                                required
                            >{{ old('motivation') }}</textarea>
                            <div class="form-text text-secondary small">50 sampai 1000 karakter</div>
                            @error('motivation')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Pengalaman atau Skill --}}
                        <div class="mb-4">
                            <label for="experience" class="form-label">Pengalaman atau Skill <span class="text-secondary fw-normal">(Opsional)</span></label>
                            <textarea 
                                id="experience" 
                                name="experience" 
                                rows="3" 
                                class="form-control @error('experience') is-invalid @enderror" 
                                placeholder="Ceritakan pengalaman teknis, organisasi, atau keterampilan yang kamu miliki"
                            >{{ old('experience') }}</textarea>
                            <div class="form-text text-secondary small">Maksimal 1000 karakter</div>
                            @error('experience')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
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
            @endif
        </div>
    </div>
</x-app-layout>
