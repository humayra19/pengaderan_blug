<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /**
     * Tampilkan formulir atau status pengajuan keanggotaan.
     */
    public function index(): View
    {
        return view('pengajuan.index');
    }

    /**
     * Simpan pengajuan keanggotaan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('pengajuan.index');
    }
}

