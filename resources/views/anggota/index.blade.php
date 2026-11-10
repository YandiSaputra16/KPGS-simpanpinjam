
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Manajemen Anggota</h2>
                <p class="text-muted mb-0">
                    Kelola data anggota dan akun anggota KPGS.
                </p>
            </div>

            <a href="{{ route('anggota.create') }}" class="btn btn-primary">
                + Tambah Anggota
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Anggota</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>No. HP</th>
                                <th>Informasi Rekening</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($anggota as $item)
                                <tr>
                                    <td>
                                        {{ $anggota->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $item->kode_anggota }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $item->user?->nama ?? '-' }}
                                    </td>

                                    <td>{{ $item->nik }}</td>
                                    <td>{{ $item->no_hp }}</td>

                                    <td>
                                        @if ($item->memilikiRekeningLengkap())
                                            <div class="fw-semibold">
                                                {{ $item->nama_bank }}
                                            </div>
                                            <div class="small text-muted">
                                                No. Rekening: {{ $item->nomor_rekening }}
                                            </div>
                                            <div class="small text-muted">
                                                Atas Nama: {{ $item->atas_nama_rekening }}
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                Belum lengkap
                                            </span>

                                            <div class="small text-muted mt-1">
                                                @if (blank($item->nama_bank)
                                                    && blank($item->nomor_rekening)
                                                    && blank($item->atas_nama_rekening))
                                                    Data rekening belum diisi.
                                                @else
                                                    Sebagian data rekening belum diisi.
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($item->status_keanggotaan === 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('anggota.edit', $item) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            @if ($item->status_keanggotaan === 'aktif')
                                                <form action="{{ route('anggota.destroy', $item) }}"
                                                      method="POST"
                                                      data-confirm-submit="Nonaktifkan anggota ini?">
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
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="fw-semibold">
                                            Belum ada data anggota.
                                        </div>
                                        <div class="small mt-1">
                                            Gunakan tombol "Tambah Anggota" untuk
                                            mendaftarkan anggota baru.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($anggota->hasPages())
                    <div class="mt-4">
                        {{ $anggota->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
