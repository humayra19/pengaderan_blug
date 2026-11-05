<x-app-layout title="Profil">
    <div class="container py-5">
        <div class="container-narrow">

            {{-- Judul dan Penjelasan --}}
            <div class="mb-4">
                <h1 class="h2 fw-bold mb-1">Profil</h1>
                <p class="text-secondary mb-0">Kelola informasi data diri akun Anda.</p>
            </div>

            {{-- Flash: pesan sukses --}}
            @if (session('success'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ============================================================
                 Kartu 1: Data Diri (US-13)
            ================================================================ --}}
            <div class="card p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Data diri</h2>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    {{-- Nama Lengkap --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                            autocomplete="name"
                        >
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor WhatsApp</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="Contoh: 081234567890"
                            required
                            autocomplete="tel"
                        >
                        <div class="form-text text-secondary small">Format: 08xx atau 628xx</div>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ============================================================
                         Field khusus pendaftar (role: user)
                         Admin tidak melihat field NIM, Prodi, dan Angkatan
                    ================================================================ --}}
                    @if ($user->role === 'user')
                        {{-- NIM --}}
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input
                                type="text"
                                id="nim"
                                name="nim"
                                value="{{ old('nim', $user->nim) }}"
                                class="form-control @error('nim') is-invalid @enderror"
                                placeholder="Contoh: 20231001"
                                required
                            >
                            <div class="form-text text-secondary small">8 sampai 15 digit angka</div>
                            @error('nim')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Program Studi --}}
                        <div class="mb-3">
                            <label for="study_program" class="form-label">Program Studi</label>
                            <input
                                type="text"
                                id="study_program"
                                name="study_program"
                                value="{{ old('study_program', $user->study_program) }}"
                                class="form-control @error('study_program') is-invalid @enderror"
                                placeholder="Contoh: Teknik Informatika"
                                required
                            >
                            @error('study_program')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Angkatan --}}
                        @php
                            $currentYear = (int) date('Y');
                            $batchYears  = range($currentYear, $currentYear - 10);
                        @endphp
                        <div class="mb-4">
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
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    {{-- Tombol Simpan Perubahan --}}
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- ============================================================
                 Kartu 2: Ganti Password (US-14)
            ================================================================ --}}
            <div class="card p-4 mb-4">
                <h2 class="h5 fw-bold mb-3">Ganti password</h2>

                {{-- Flash sukses ganti password --}}
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success mb-3" role="alert">
                        Password berhasil diperbarui.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- Password Lama --}}
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Lama</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                            required
                            autocomplete="current-password"
                        >
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                            required
                            autocomplete="new-password"
                        >
                        <div class="form-text text-secondary small">Minimal 8 karakter</div>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                            required
                            autocomplete="new-password"
                        >
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol Perbarui Password --}}
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tombol Keluar --}}
            <div class="pt-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-dark w-100">
                        Keluar
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
