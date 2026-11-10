<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        if ($this->user()?->role === 'anggota' && $this->user()->anggota) {
            $anggota = $this->user()->anggota;

            $rules += [
                'nik' => ['required', 'digits:16', Rule::unique('anggota', 'nik')->ignore($anggota?->id)],
                'no_hp' => ['required', 'string', 'max:20'],
                'alamat' => ['required', 'string', 'max:2000'],
                'pekerjaan' => ['nullable', 'string', 'max:255'],
                'nama_bank' => ['nullable', 'string', 'max:100'],
                'nomor_rekening' => ['nullable', 'string', 'max:50'],
                'atas_nama_rekening' => ['nullable', 'string', 'max:150'],
            ];
        }

        return $rules;
    }
}
