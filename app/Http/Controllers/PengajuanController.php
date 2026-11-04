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
     */
    public function store(StoreMembershipApplicationRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = $request->user();

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
