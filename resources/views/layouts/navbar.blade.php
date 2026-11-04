<nav class="navbar navbar-expand-md navbar-dark navbar-blug" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand brand-blug fs-4" href="{{ url('/') }}">blug_</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarBlug" aria-controls="navbarBlug" aria-expanded="false" aria-label="Navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarBlug">
            <ul class="navbar-nav ms-auto align-items-md-center gap-2 my-2 my-md-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a>
                </li>

                @auth
                    @if (auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('pengajuan*') ? 'active' : '' }}" href="{{ Route::has('pengajuan.index') ? route('pengajuan.index') : url('/pengajuan') }}">Pengajuan</a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('profile*') ? 'active' : '' }}" href="{{ Route::has('profile.edit') ? route('profile.edit') : url('/profile') }}">Profil</a>
                    </li>

                    <li class="nav-item ms-md-2">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-light w-100 w-md-auto">Keluar</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item ms-md-2">
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 w-md-auto">Masuk</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

