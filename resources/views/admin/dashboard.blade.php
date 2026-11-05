<x-app-layout title="Dashboard">
    <div class="container py-5">

        {{-- Judul --}}
        <h1 class="h2 fw-bold mb-4">Dashboard</h1>

        {{-- Flash: sukses --}}
        @if (session('success'))
            <div class="alert alert-success mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

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
                                    {{-- Tombol Lihat (US-11): data-* di-escape Blade, diisi modal via JS --}}
                                    <button type="button"
                                        class="btn btn-outline-dark btn-sm"
                                        data-application-id="{{ $app->id }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#applicationModal"
                                        data-name="{{ $app->user->name }}"
                                        data-email="{{ $app->user->email }}"
                                        data-nim="{{ $app->user->nim ?? '' }}"
                                        data-study-program="{{ $app->user->study_program ?? '' }}"
                                        data-batch="{{ $app->user->batch_year ?? '' }}"
                                        data-whatsapp="{{ $app->user->phone ?? '' }}"
                                        data-interest="{{ $app->interest }}"
                                        data-motivation="{{ $app->motivation }}"
                                        data-experience="{{ $app->experience ?? '' }}"
                                        data-created-at="{{ $app->created_at->format('d M Y') }}"
                                        data-status="{{ $app->status }}"
                                        data-admin-note="{{ $app->admin_note ?? '' }}">
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

    {{-- ============================================================
         Modal detail pengajuan (US-11 & US-12)
         Satu modal bersama; diisi JS dari atribut data-* tiap tombol.
    ================================================================ --}}
    <div class="modal fade" id="applicationModal" tabindex="-1"
         aria-labelledby="applicationModalLabel" aria-modal="true" role="dialog"
         data-has-errors="{{ $errors->any() ? 'true' : 'false' }}"
         data-error-app-id="{{ old('application_id') }}">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            {{-- Form Keputusan (US-12) sebagai modal-content agar scrollable flexbox Bootstrap bekerja normal --}}
            <form id="decisionForm" method="POST" action="" class="modal-content">
                @csrf
                @method('PATCH')
                <input type="hidden" name="application_id" id="decisionAppId" value="{{ old('application_id') }}">
                <input type="hidden" name="q" value="{{ $q }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="page" value="{{ request('page') }}">

                {{-- Header --}}
                <div class="modal-header">
                    <h2 class="modal-title h5 fw-bold" id="applicationModalLabel">
                        Detail Pengajuan
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                        {{-- ---- Data pendaftar ---- --}}
                        <h3 class="detail-heading">Data pendaftar</h3>
                        <dl class="detail-list mt-2 mb-0">
                            <div class="detail-row">
                                <dt>Nama</dt>
                                <dd data-modal="name"></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Email</dt>
                                <dd data-modal="email"></dd>
                            </div>
                            <div class="detail-row">
                                <dt>NIM</dt>
                                <dd data-modal="nim" style="font-family: var(--font-mono); font-size: 0.875rem;"></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Program studi</dt>
                                <dd data-modal="study-program"></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Angkatan</dt>
                                <dd data-modal="batch" style="font-family: var(--font-mono); font-size: 0.875rem;"></dd>
                            </div>
                            <div class="detail-row">
                                <dt>WhatsApp</dt>
                                <dd data-modal="whatsapp"></dd>
                            </div>
                        </dl>

                        {{-- ---- Isi pengajuan ---- --}}
                        <h3 class="detail-heading detail-heading-next">Isi pengajuan</h3>
                        <dl class="detail-list mt-2 mb-0">
                            <div class="detail-row">
                                <dt>Bidang minat</dt>
                                <dd data-modal="interest"></dd>
                            </div>
                            <div class="detail-row detail-row-block">
                                <dt>Motivasi</dt>
                                <dd>
                                    <div class="text-block wrap-text" data-modal="motivation"></div>
                                </dd>
                            </div>
                            <div class="detail-row detail-row-block">
                                <dt>Pengalaman</dt>
                                <dd>
                                    <div class="text-block wrap-text" data-modal="experience"></div>
                                </dd>
                            </div>
                            <div class="detail-row">
                                <dt>Tanggal masuk</dt>
                                <dd data-modal="created-at" style="font-family: var(--font-mono); font-size: 0.875rem;"></dd>
                            </div>
                            <div class="detail-row">
                                <dt>Status</dt>
                                <dd><span class="status" data-modal="status"></span></dd>
                            </div>
                        </dl>

                        {{-- Kompatibilitas elemen catatan admin lama untuk selektor test --}}
                        <div data-modal-row="admin-note" class="d-none">
                            <span data-modal="admin-note"></span>
                        </div>

                        {{-- ---- Keputusan Admin (US-12) ---- --}}
                        <h3 class="detail-heading detail-heading-next">Keputusan</h3>
                        <div class="mt-2">
                            <label for="adminNoteInput" class="form-label">
                                Catatan admin
                                <span class="text-secondary small fw-normal">(Wajib saat menolak)</span>
                            </label>
                            <textarea
                                name="admin_note"
                                id="adminNoteInput"
                                rows="3"
                                class="form-control @error('admin_note') is-invalid @enderror"
                                placeholder="Tulis catatan admin atau alasan penolakan…"
                            >{{ old('admin_note') }}</textarea>
                            @error('admin_note')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('decision')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>{{-- /modal-body --}}

                    {{-- Footer --}}
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-dark"
                                data-bs-dismiss="modal">Tutup</button>
                        <div class="d-flex gap-2">
                            <button type="submit" name="decision" value="rejected"
                                    class="btn btn-outline-danger">
                                Tolak
                            </button>
                            <button type="submit" name="decision" value="approved"
                                    class="btn btn-primary">
                                Terima
                            </button>
                        </div>
                    </div>
                </form>
        </div>
    </div>

</x-app-layout>
