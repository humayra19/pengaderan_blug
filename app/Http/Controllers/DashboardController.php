<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     */
    public function index(): View
    {
        return view('dashboard');
    }

    /**
     * Perbarui keputusan pengajuan keanggotaan.
     */
    public function update(Request $request, string $application): RedirectResponse
    {
        return redirect()->route('dashboard');
    }
}

