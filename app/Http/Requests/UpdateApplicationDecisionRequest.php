<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationDecisionRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki hak akses untuk permintaan ini.
     * Hanya admin yang diperbolehkan.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'admin';
    }

    /**
     * Aturan validasi keputusan pengajuan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'application_id' => ['nullable'],
            'decision'       => ['required', 'string', 'in:approved,rejected'],
            'admin_note'     => ['nullable', 'string', 'max:1000', 'required_if:decision,rejected'],
            'q'              => ['nullable', 'string'],
            'status'         => ['nullable', 'string'],
            'page'           => ['nullable', 'integer'],
        ];
    }

    /**
     * Pesan galat dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'decision.required'      => 'Keputusan wajib dipilih (Terima atau Tolak).',
            'decision.in'            => 'Keputusan harus berupa diterima atau ditolak.',
            'admin_note.required_if' => 'Catatan admin wajib diisi saat menolak pengajuan.',
            'admin_note.max'         => 'Catatan admin maksimal 1000 karakter.',
        ];
    }

    /**
     * Tentukan URL tujuan redirect jika validasi gagal.
     */
    protected function getRedirectUrl(): string
    {
        $url = parent::getRedirectUrl();

        // Bila request tidak membawa previous referer (misal direct call di test),
        // fallback redirect ke dashboard agar tetap sesuai konteks admin.
        if ($url === url('/')) {
            return route('dashboard');
        }

        return $url;
    }
}
