<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMembershipApplicationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Tampilkan formulir atau status pengajuan keanggotaan.
     */
    public function index(): View
    {
        $user = auth()->user();
        $latestApplication = $user->latestApplication;
        $interests = config('blug.interests', []);
        $currentYear = (int) date('Y');
        $batchYears = range($currentYear, $currentYear - 10);

        return view('pengajuan.index', compact('user', 'latestApplication', 'interests', 'batchYears'));
    }

    /**
     * Simpan pengajuan keanggotaan baru.
     *
     * Aturan server: tolak bila pengguna sudah punya pengajuan dengan status
     * pending atau approved — tidak cukup menyembunyikan tombol di tampilan.
     */
    public function store(StoreMembershipApplicationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Cek keberadaan pengajuan aktif (pending atau approved) di sisi server.
        $activeApplication = $user->applications()
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->first();

        if ($activeApplication) {
            $pesan = $activeApplication->status === 'approved'
                ? 'Pengajuan Anda sudah diterima. Anda tidak dapat mengajukan kembali.'
                : 'Pengajuan Anda sedang dalam proses peninjauan. Silakan tunggu hasilnya.';

            return redirect()->route('pengajuan.index')->with('error', $pesan);
        }

        DB::transaction(function () use ($request, $user) {
            // 1. Simpan data profil ke tabel users
            $user->update([
                'nim' => $request->nim,
                'study_program' => $request->study_program,
                'batch_year' => $request->batch_year,
                'phone' => $request->phone,
            ]);

            // 2. Buat baris baru di membership_applications dengan status pending
            $user->applications()->create([
                'interest' => $request->interest,
                'motivation' => $request->motivation,
                'experience' => $request->experience,
                'status' => 'pending',
            ]);
        });

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim. Silakan tunggu peninjauan dari admin.');
    }
}
