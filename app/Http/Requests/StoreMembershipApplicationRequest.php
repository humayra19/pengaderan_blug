<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'user';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = (int) date('Y');
        $minYear = $currentYear - 10;
        $allowedInterests = config('blug.interests', []);

        return [
            'nim' => ['required', 'numeric', 'digits_between:8,15'],
            'study_program' => ['required', 'string', 'max:255'],
            'batch_year' => ['required', 'integer', 'between:'.$minYear.','.$currentYear],
            'phone' => ['required', 'string', 'regex:/^(08|628)[0-9]{7,13}$/'],
            'interest' => ['required', 'string', 'in:'.implode(',', $allowedInterests)],
            'motivation' => ['required', 'string', 'min:50', 'max:1000'],
            'experience' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nim.required' => 'NIM wajib diisi.',
            'nim.numeric' => 'NIM harus berupa angka.',
            'nim.digits_between' => 'NIM harus terdiri dari 8 sampai 15 digit angka.',
            'study_program.required' => 'Program studi wajib diisi.',
            'study_program.max' => 'Program studi maksimal 255 karakter.',
            'batch_year.required' => 'Angkatan wajib dipilih.',
            'batch_year.integer' => 'Tahun angkatan harus berupa angka.',
            'batch_year.between' => 'Tahun angkatan tidak valid.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp harus berformat 08xx atau 628xx.',
            'interest.required' => 'Bidang minat wajib dipilih.',
            'interest.in' => 'Pilihan bidang minat tidak valid.',
            'motivation.required' => 'Motivasi bergabung wajib diisi.',
            'motivation.min' => 'Motivasi bergabung minimal 50 karakter.',
            'motivation.max' => 'Motivasi bergabung maksimal 1000 karakter.',
            'experience.max' => 'Pengalaman atau skill maksimal 1000 karakter.',
        ];
    }
}

