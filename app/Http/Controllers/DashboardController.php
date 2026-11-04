<?php

namespace App\Http\Controllers;

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
    public function index(): View
    {
        // Satu query agregat: SELECT status, COUNT(*) AS total … GROUP BY status
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

        // Daftar pengajuan: urutan terbaru, 10 per halaman, eager-load user
        $applications = MembershipApplication::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'applications'));
    }

    /**
     * Perbarui keputusan pengajuan keanggotaan (dikerjakan di US-12).
     */
    public function update(Request $request, string $application): RedirectResponse
    {
        return redirect()->route('dashboard');
    }
}

