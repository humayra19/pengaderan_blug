<x-app-layout title="Daftar">
    <div class="container py-5">
        <div class="auth-card">
            {{-- Tabs Masuk / Daftar --}}
            <div class="tabs-blug">
                <a href="{{ route('login') }}" class="tab-link">Masuk</a>
                <a href="{{ route('register') }}" class="tab-link active">Daftar</a>
            </div>

            {{-- Kartu Form Register --}}
            <div class="card p-4">
                @include('auth._form', ['tab' => 'register'])
            </div>
        </div>
    </div>
</x-app-layout>
