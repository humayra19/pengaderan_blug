<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan formulir profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Perbarui data diri pengguna (US-13).
     *
     * Semua peran dapat mengubah nama, email, dan nomor WhatsApp.
     * Hanya pengguna dengan peran 'user' yang dapat mengubah NIM,
     * program studi, dan angkatan. Admin tidak dapat mengubah field tersebut.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Pastikan admin tidak bisa mengubah field akademik mahasiswa
        if ($request->user()->role !== 'user') {
            unset($data['nim'], $data['study_program'], $data['batch_year']);
        }

        $request->user()->fill($data);
        $request->user()->save();

        return redirect()->route('profile.edit')->with('success', 'Data diri berhasil diperbarui.');
    }
}
