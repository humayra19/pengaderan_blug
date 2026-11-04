@if ($tab === 'login')
    @if (session('status'))
        <div class="alert alert-info py-2 px-3 mb-3 small">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                class="form-control @error('email') is-invalid @enderror" 
                required 
                autofocus 
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Kata Sandi --}}
        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                class="form-control @error('password') is-invalid @enderror" 
                required 
                autocomplete="current-password"
            >
            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Ingat Saya --}}
        <div class="mb-4 form-check">
            <input 
                id="remember" 
                type="checkbox" 
                name="remember" 
                class="form-check-input"
                {{ old('remember') ? 'checked' : '' }}
            >
            <label for="remember" class="form-check-label small text-secondary">Ingat saya</label>
        </div>

        {{-- Tombol Masuk --}}
        <div>
            <button type="submit" class="btn btn-primary w-100">
                Masuk
            </button>
        </div>
    </form>
@elseif ($tab === 'register')
    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        {{-- Nama Lengkap --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                class="form-control @error('name') is-invalid @enderror" 
                required 
                autofocus 
                autocomplete="name"
            >
            @error('name')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="register_email" class="form-label">Email</label>
            <input 
                id="register_email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                class="form-control @error('email') is-invalid @enderror" 
                required 
                autocomplete="username"
            >
            @error('email')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Kata Sandi --}}
        <div class="mb-3">
            <label for="register_password" class="form-label">Kata Sandi</label>
            <input 
                id="register_password" 
                type="password" 
                name="password" 
                class="form-control @error('password') is-invalid @enderror" 
                required 
                autocomplete="new-password"
            >
            <div class="form-text text-secondary small">
                Minimal 8 karakter
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Konfirmasi Kata Sandi --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                class="form-control @error('password_confirmation') is-invalid @enderror" 
                required 
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Tombol Buat Akun --}}
        <div>
            <button type="submit" class="btn btn-primary w-100">
                Buat akun
            </button>
        </div>
    </form>
@endif
