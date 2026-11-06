<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Manajemen Pengguna</h2>
                <p class="text-muted mb-0">Kelola akun pengguna internal KPGS.</p>
            </div>

            <a href="{{ route('users.create') }}" class="btn btn-primary">
                + Tambah Pengguna
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $user->nama }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $user->email }}
                                    </td>

                                    <td>
                                        @switch($user->role)
                                            @case('cs')
                                                Customer Service
                                                @break

                                            @case('ao')
                                                Account Officer
                                                @break

                                            @case('kabag')
                                                Kepala Bagian
                                                @break

                                            @case('bendahara')
                                                Bendahara
                                                @break

                                            @case('pimpinan')
                                                Pimpinan
                                                @break

                                            @default
                                                {{ ucfirst($user->role) }}
                                        @endswitch
                                    </td>

                                    <td>
                                        @if ($user->status === 'aktif')
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">

                                            <a href="{{ route('users.edit', $user) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            @if (auth()->id() !== $user->id && $user->status === 'aktif')
                                                <form action="{{ route('users.destroy', $user) }}"
                                                      method="POST"
                                                      data-confirm-submit="Nonaktifkan akun pengguna ini?">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger">
                                                        Nonaktifkan
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada akun pengguna internal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="mt-4">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>