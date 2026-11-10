<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    /**
     * Menampilkan daftar anggota.
     */
    public function index(): View
    {
        $anggota = Anggota::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('anggota.index', compact('anggota'));
    }

    /**
     * Menampilkan form tambah anggota.
     */
    public function create(): View
    {
        return view('anggota.create');
    }

    /**
     * Menyimpan anggota sekaligus membuat akun pengguna.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'nik' => [
                'required',
                'digits:16',
                'unique:anggota,nik',
            ],

            'no_hp' => [
                'required',
                'string',
                'max:20',
            ],

            'alamat' => [
                'required',
                'string',
            ],

            'pekerjaan' => [
                'nullable',
                'string',
                'max:255',
            ],
            'nama_bank' => ['nullable', 'string', 'max:100'],
            'nomor_rekening' => ['nullable', 'string', 'max:50'],
            'atas_nama_rekening' => ['nullable', 'string', 'max:150'],

            'tanggal_daftar' => [
                'required',
                'date',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'anggota',
                'status' => 'aktif',
            ]);

            Anggota::create([
                'user_id' => $user->id,
                'kode_anggota' => Anggota::generateKode(),
                'nik' => $validated['nik'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'pekerjaan' => $validated['pekerjaan'] ?? null,
                'nama_bank' => $validated['nama_bank'] ?? null,
                'nomor_rekening' => $validated['nomor_rekening'] ?? null,
                'atas_nama_rekening' => $validated['atas_nama_rekening'] ?? null,
                'tanggal_daftar' => $validated['tanggal_daftar'],
                'status_keanggotaan' => 'aktif',
                'saldo_simpanan_pokok' => 0,
                'saldo_simpanan_wajib' => 0,
                'saldo_simpanan_sukarela' => 0,
            ]);
        });

        return redirect()
            ->route('anggota.index')
            ->with('success', 'Data anggota dan akun anggota berhasil dibuat.');
    }

    /**
     * Menampilkan detail anggota.
     */
    public function show(Anggota $anggota): View
    {
        $anggota->load('user');

        return view('anggota.show', compact('anggota'));
    }

    /**
     * Menampilkan form edit anggota.
     */
    public function edit(Anggota $anggota): View
    {
        $anggota->load('user');

        return view('anggota.edit', compact('anggota'));
    }

    /**
     * Memperbarui data anggota dan akun.
     */
    public function update(Request $request, Anggota $anggota): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($anggota->user_id),
            ],

            'nik' => [
                'required',
                'digits:16',
                Rule::unique('anggota', 'nik')->ignore($anggota->id),
            ],

            'no_hp' => [
                'required',
                'string',
                'max:20',
            ],

            'alamat' => [
                'required',
                'string',
            ],

            'pekerjaan' => [
                'nullable',
                'string',
                'max:255',
            ],
            'nama_bank' => ['nullable', 'string', 'max:100'],
            'nomor_rekening' => ['nullable', 'string', 'max:50'],
            'atas_nama_rekening' => ['nullable', 'string', 'max:150'],

            'tanggal_daftar' => [
                'required',
                'date',
            ],

            'status_keanggotaan' => [
                'required',
                Rule::in([
                    'aktif',
                    'nonaktif',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        DB::transaction(function () use ($validated, $anggota) {
            $user = $anggota->user;

            $user->nama = $validated['nama'];
            $user->email = $validated['email'];
            $user->status = $validated['status_keanggotaan'] === 'aktif'
                ? 'aktif'
                : 'nonaktif';

            if (!empty($validated['password'])) {
                $user->password = $validated['password'];
            }

            $user->save();

            $anggota->update([
                'nik' => $validated['nik'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'pekerjaan' => $validated['pekerjaan'] ?? null,
                'nama_bank' => $validated['nama_bank'] ?? null,
                'nomor_rekening' => $validated['nomor_rekening'] ?? null,
                'atas_nama_rekening' => $validated['atas_nama_rekening'] ?? null,
                'tanggal_daftar' => $validated['tanggal_daftar'],
                'status_keanggotaan' => $validated['status_keanggotaan'],
            ]);
        });

        return redirect()
            ->route('anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Menonaktifkan anggota dan akun terkait.
     */
    public function destroy(Anggota $anggota): RedirectResponse
    {
        DB::transaction(function () use ($anggota) {
            $anggota->update([
                'status_keanggotaan' => 'nonaktif',
            ]);

            if ($anggota->user) {
                $anggota->user->update([
                    'status' => 'nonaktif',
                ]);
            }
        });

        return redirect()
            ->route('anggota.index')
            ->with('success', 'Anggota berhasil dinonaktifkan.');
    }
}