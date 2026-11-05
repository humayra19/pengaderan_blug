<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = (int) date('Y');
        $minYear     = $currentYear - 10;

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['required', 'string', 'regex:/^(08|628)[0-9]{7,13}$/'],
        ];

        // Field akademik hanya wajib dan divalidasi untuk pendaftar (role: user)
        if ($this->user()?->role === 'user') {
            $rules['nim']           = ['required', 'numeric', 'digits_between:8,15'];
            $rules['study_program'] = ['required', 'string', 'max:255'];
            $rules['batch_year']    = ['required', 'integer', 'between:' . $minYear . ',' . $currentYear];
        }

        return $rules;
    }

    /**
     * Pesan galat kustom dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'          => 'Nama lengkap wajib diisi.',
            'name.max'               => 'Nama lengkap maksimal 255 karakter.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email ini sudah digunakan oleh akun lain.',
            'email.max'              => 'Email maksimal 255 karakter.',
            'phone.required'         => 'Nomor WhatsApp wajib diisi.',
            'phone.regex'            => 'Nomor WhatsApp harus berformat 08xx atau 628xx.',
            'nim.required'           => 'NIM wajib diisi.',
            'nim.numeric'            => 'NIM harus berupa angka.',
            'nim.digits_between'     => 'NIM harus terdiri dari 8 sampai 15 digit angka.',
            'study_program.required' => 'Program studi wajib diisi.',
            'study_program.max'      => 'Program studi maksimal 255 karakter.',
            'batch_year.required'    => 'Angkatan wajib dipilih.',
            'batch_year.integer'     => 'Tahun angkatan harus berupa angka.',
            'batch_year.between'     => 'Tahun angkatan tidak valid.',
        ];
    }
}
