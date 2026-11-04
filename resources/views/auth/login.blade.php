<x-app-layout title="Masuk">
    <div class="container py-5">
        <div class="auth-card">
            {{-- Tabs Masuk / Daftar --}}
            <div class="tabs-blug">
                <a href="{{ route('login') }}" class="tab-link active">Masuk</a>
                <a href="{{ route('register') }}" class="tab-link">Daftar</a>
            </div>

            {{-- Kartu Form Login --}}
            <div class="card p-4">
                @include('auth._form', ['tab' => 'login'])
            </div>
        </div>
    </div>
</x-app-layout>
