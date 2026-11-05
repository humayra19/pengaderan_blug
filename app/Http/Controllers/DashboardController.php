<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateApplicationDecisionRequest;
use App\Models\MembershipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     *
     * Hitung jumlah pengajuan per status dalam satu query agregat (groupBy).
     */
    public function index(Request $request): View
    {
        // Kartu ringkasan: selalu dari SEMUA data, tidak ikut filter pencarian
        $counts = MembershipApplication::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total'    => $counts->sum(),
            'pending'  => (int) ($counts['pending']  ?? 0),
            'approved' => (int) ($counts['approved'] ?? 0),
            'rejected' => (int) ($counts['rejected'] ?? 0),
        ];

        // Parameter pencarian & filter
        $q             = trim((string) $request->query('q', ''));
        $status        = (string) $request->query('status', '');
        $validStatuses = ['pending', 'approved', 'rejected'];

        // Normalkan status yang tidak dikenal menjadi string kosong
        if (! in_array($status, $validStatuses, true)) {
            $status = '';
        }

        $query = MembershipApplication::with('user')->latest();

        // Cari nama ATAU NIM via whereHas (binding aman, bukan string mentah)
        if ($q !== '') {
            $query->whereHas('user', function ($sub) use ($q) {
                $sub->where('name', 'like', '%' . $q . '%')
                    ->orWhere('nim', 'like', '%' . $q . '%');
            });
        }

        // Filter status (nilai tidak valid sudah dinormalkan ke '' di atas)
        if ($status !== '') {
            $query->where('status', $status);
        }

        // withQueryString() agar parameter q & status ikut di tautan pagination
        $applications = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact('stats', 'applications', 'q', 'status'));
    }

    /**
     * Perbarui keputusan pengajuan keanggotaan (US-12).
     *
     * Menyimpan status, admin_note, reviewed_by (admin yang login),
     * dan reviewed_at (waktu saat ini). Admin dapat mengubah keputusan
     * yang sudah dibuat sebelumnya (untuk koreksi).
     */
    public function update(UpdateApplicationDecisionRequest $request, string $application): RedirectResponse
    {
        $app = MembershipApplication::find($application);
        if (! $app) {
            return redirect()->route('dashboard');
        }

        $decision = $request->validated('decision');
        $note     = $request->validated('admin_note');

        $app->update([
            'status'      => $decision,
            'admin_note'  => $note,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Pertahankan query pencarian, filter status, dan halaman pagination bila ada
        $queryParams = array_filter([
            'q'      => $request->input('q'),
            'status' => $request->input('status'),
            'page'   => $request->input('page'),
        ], fn ($val) => $val !== null && $val !== '');

        $label = $decision === 'approved' ? 'diterima' : 'ditolak';
        $applicantName = $app->user ? $app->user->name : 'Pendaftar';

        return redirect()
            ->route('dashboard', $queryParams)
            ->with('success', "Pengajuan atas nama {$applicantName} berhasil {$label}.");
    }
}

