<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Daftar akun pengguna internal.
     */
    public function index(): View
    {
        $users = User::query()
            ->whereIn('role', [
                'cs',
                'ao',
                'kabag',
                'bendahara',
                'pimpinan',
            ])
            ->orderBy('nama')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    /**
     * Form tambah pengguna internal.
     */
    public function create(): View
    {
        $roles = [
            'cs' => 'Customer Service',
            'ao' => 'Account Officer',
            'kabag' => 'Kepala Bagian',
            'bendahara' => 'Bendahara',
            'pimpinan' => 'Pimpinan',
        ];

        return view('users.create', compact('roles'));
    }

    /**
     * Menyimpan akun pengguna internal baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => [
                'required',
                Rule::in([
                    'cs',
                    'ao',
                    'kabag',
                    'bendahara',
                    'pimpinan',
                ]),
            ],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun pengguna berhasil dibuat.');
    }

    /**
     * Form edit pengguna.
     */
    public function edit(User $user): View
    {
        $this->ensureInternalUser($user);

        $roles = [
            'cs' => 'Customer Service',
            'ao' => 'Account Officer',
            'kabag' => 'Kepala Bagian',
            'bendahara' => 'Bendahara',
            'pimpinan' => 'Pimpinan',
        ];

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Memperbarui akun pengguna.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureInternalUser($user);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::in([
                    'cs',
                    'ao',
                    'kabag',
                    'bendahara',
                    'pimpinan',
                ]),
            ],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    /**
     * Menonaktifkan akun pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->ensureInternalUser($user);

        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dinonaktifkan.');
        }

        $user->update([
            'status' => 'nonaktif',
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun pengguna berhasil dinonaktifkan.');
    }

    /**
     * Memastikan pengguna merupakan akun internal.
     */
    private function ensureInternalUser(User $user): void
    {
        abort_unless(
            in_array($user->role, [
                'cs',
                'ao',
                'kabag',
                'bendahara',
                'pimpinan',
            ], true),
            404
        );
    }
}