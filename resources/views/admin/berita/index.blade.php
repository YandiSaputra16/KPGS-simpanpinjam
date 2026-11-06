<x-app-layout>
    <x-slot name="title">
        Berita KPGS
    </x-slot>

    <div class="container-fluid">
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1">
                    Berita KPGS
                </h1>

                <p class="text-muted mb-0">
                    Kelola informasi dan berita yang ditampilkan pada website KPGS.
                </p>
            </div>

            <div>
                <a
                    href="{{ route('admin.berita.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Berita
                </a>
            </div>
        </div>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div
                class="alert alert-success d-flex align-items-center gap-2 mb-4"
                role="alert"
            >
                <i class="bi bi-check-circle"></i>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Daftar berita --}}
        <div class="card border-0 shadow-sm">
            @if ($berita->count())
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-newspaper text-primary"></i>
                        <div>
                            <h5 class="fw-bold mb-0">
                                Daftar Berita
                            </h5>
                            <small class="text-muted">
                                Informasi yang dikelola administrator KPGS.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">
                                        Berita
                                    </th>

                                    <th class="py-3 text-nowrap">
                                        Status
                                    </th>

                                    <th class="py-3 text-nowrap">
                                        Pembuat
                                    </th>

                                    <th class="py-3 text-nowrap">
                                        Tanggal
                                    </th>

                                    <th class="py-3 text-end">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($berita as $item)
                                    <tr>
                                        {{-- Berita --}}
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if ($item->gambar)
                                                    <img
                                                        src="{{ asset('storage/' . $item->gambar) }}"
                                                        alt="{{ $item->judul }}"
                                                        class="rounded object-fit-cover kpgs-thumb-96x64"
                                                    >
                                                @else
                                                    <div
                                                        class="d-flex align-items-center justify-content-center rounded bg-light text-muted flex-shrink-0 kpgs-thumb-96x64"
                                                    >
                                                        <div class="text-center">
                                                            <i class="bi bi-image fs-5 d-block"></i>
                                                            <small>
                                                                Tanpa gambar
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div >
                                                    <div class="fw-semibold text-dark">
                                                        {{ $item->judul }}
                                                    </div>

                                                    @if ($item->ringkasan)
                                                        <div
                                                            class="small text-muted text-truncate kpgs-news-summary"
                                                        >
                                                            {{ $item->ringkasan }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3">
                                            @if ($item->status === 'published')
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Published
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    <i class="bi bi-file-earmark me-1"></i>
                                                    Draft
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Pembuat --}}
                                        <td class="py-3">
                                            <div class="small text-dark">
                                                {{ $item->pembuat?->nama ?? 'Tidak diketahui' }}
                                            </div>
                                        </td>

                                        {{-- Tanggal --}}
                                        <td class="py-3 text-nowrap">
                                            <div class="small text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $item->created_at?->format('d M Y') }}
                                            </div>

                                            <div class="small text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $item->created_at?->format('H:i') }}
                                            </div>
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-3">
                                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                <a
                                                    href="{{ route('admin.berita.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Edit berita"
                                                >
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Edit
                                                </a>

                                                @if ($item->status === 'published')
                                                    <form
                                                        action="{{ route('admin.berita.unpublish', $item) }}"
                                                        method="POST"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-warning"
                                                            title="Ubah menjadi draft"
                                                            data-confirm="Apakah Anda yakin ingin mengubah berita ini menjadi draft?"
                                                        >
                                                            <i class="bi bi-file-earmark me-1"></i>
                                                            Draft
                                                        </button>
                                                    </form>
                                                @else
                                                    <form
                                                        action="{{ route('admin.berita.publish', $item) }}"
                                                        method="POST"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-success"
                                                            title="Publikasikan berita"
                                                            data-confirm="Apakah Anda yakin ingin mempublikasikan berita ini?"
                                                        >
                                                            <i class="bi bi-send me-1"></i>
                                                            Publish
                                                        </button>
                                                    </form>
                                                @endif

                                                <form
                                                    action="{{ route('admin.berita.destroy', $item) }}"
                                                    method="POST"
                                                    data-confirm-submit="Yakin ingin menghapus berita ini?"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Hapus berita"
                                                    >
                                                        <i class="bi bi-trash me-1"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($berita->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $berita->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                {{-- Kondisi kosong --}}
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-newspaper fs-1 text-muted"></i>
                    </div>

                    <h5 class="fw-bold mb-2">
                        Belum ada berita
                    </h5>

                    <p class="text-muted mb-4">
                        Belum ada berita yang dibuat oleh administrator.
                    </p>

                    <a
                        href="{{ route('admin.berita.create') }}"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Berita Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>