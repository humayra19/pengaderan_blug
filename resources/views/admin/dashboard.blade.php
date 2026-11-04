<x-app-layout title="Dashboard">
    <div class="container py-5">

        {{-- Judul --}}
        <h1 class="h2 fw-bold mb-5">Dashboard</h1>

        {{-- ============================================================
             Kartu ringkasan: Total | Menunggu | Diterima | Ditolak
        ================================================================ --}}
        <div class="row g-3 mb-5">

            {{-- Total: latar hitam, angka kuning --}}
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-total h-100">
                    <div class="stat-label">Total</div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>

            {{-- Menunggu: garis atas kuning --}}
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-pending h-100">
                    <div class="stat-label">Menunggu</div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                </div>
            </div>

            {{-- Diterima: garis atas zaitun --}}
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-approved h-100">
                    <div class="stat-label">Diterima</div>
                    <div class="stat-value">{{ $stats['approved'] }}</div>
                </div>
            </div>

            {{-- Ditolak: garis atas merah bata --}}
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-rejected h-100">
                    <div class="stat-label">Ditolak</div>
                    <div class="stat-value">{{ $stats['rejected'] }}</div>
                </div>
            </div>

        </div>

        {{-- ============================================================
             Pencarian & filter (US-10)
        ================================================================ --}}
        <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <input
                    type="text"
                    name="q"
                    value="{{ $q }}"
                    class="form-control"
                    style="max-width: 240px;"
                    placeholder="Cari nama atau NIM…"
                    autocomplete="off"
                >

                <select name="status" class="form-select" style="max-width: 180px;">
                    <option value="" {{ $status === '' ? 'selected' : '' }}>Semua status</option>
                    <option value="pending"  {{ $status === 'pending'  ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <button type="submit" class="btn btn-primary">Terapkan</button>

                @if ($q !== '' || $status !== '')
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-dark">Reset</a>
                @endif
            </div>
        </form>

        {{-- ============================================================
             Tabel pengajuan (US-09)
        ================================================================ --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Bidang minat</th>
                            <th>Tanggal masuk</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            <tr>
                                <td>{{ $app->user->name }}</td>
                                <td>
                                    <span style="font-family: var(--font-mono); font-size: 0.875rem;">
                                        {{ $app->user->nim ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $app->user->study_program ?? '—' }}</td>
                                <td>{{ $app->interest }}</td>
                                <td>
                                    <span style="font-size: 0.875rem; white-space: nowrap;">
                                        {{ $app->created_at->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status status-{{ $app->status }}">
                                        @if ($app->status === 'pending')   Menunggu
                                        @elseif ($app->status === 'approved') Diterima
                                        @else Ditolak
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    {{-- Tombol Lihat — fungsionalitas modal dikerjakan di US-11 --}}
                                    <button type="button"
                                        class="btn btn-outline-dark btn-sm"
                                        data-application-id="{{ $app->id }}"
                                        disabled>
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">
                                    @if ($q !== '' || $status !== '')
                                        Tidak ada pengajuan yang cocok.
                                    @else
                                        Belum ada pengajuan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($applications->hasPages())
                <div class="px-3 py-3 border-top" style="border-color: var(--blug-line) !important;">
                    {{ $applications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
