
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h4 mb-1 fw-bold text-dark">Notifikasi</h1>
                <p class="mb-0 text-secondary small">
                    Informasi aktivitas dan pembaruan transaksi akun Anda.
                </p>
            </div>

            @if ($jumlahBelumDibaca > 0)
                <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="container-fluid px-3 px-lg-4 py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup"
                ></button>
            </div>
        @endif

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex flex-wrap gap-2">
                <a
                    href="{{ route('notifikasi.index', ['filter' => 'semua']) }}"
                    class="btn {{ $filter === 'semua' ? 'btn-primary' : 'btn-outline-secondary' }}"
                >
                    Semua
                </a>

                <a
                    href="{{ route('notifikasi.index', ['filter' => 'belum_dibaca']) }}"
                    class="btn {{ $filter === 'belum_dibaca' ? 'btn-primary' : 'btn-outline-secondary' }}"
                >
                    Belum dibaca

                    @if ($jumlahBelumDibaca > 0)
                        <span class="badge {{ $filter === 'belum_dibaca' ? 'bg-light text-primary' : 'bg-primary' }} ms-1">
                            {{ $jumlahBelumDibaca }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('notifikasi.index', ['filter' => 'sudah_dibaca']) }}"
                    class="btn {{ $filter === 'sudah_dibaca' ? 'btn-primary' : 'btn-outline-secondary' }}"
                >
                    Sudah dibaca
                </a>
            </div>

            <div class="small text-secondary">
                {{ $notifikasi->total() }} notifikasi
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                @forelse ($notifikasi as $item)
                    <div
                        class="list-group-item notifikasi-item px-3 px-lg-4 py-3 {{ $item->is_read ? 'sudah-dibaca' : 'belum-dibaca' }}"
                    >
                        <div class="d-flex align-items-start gap-3">
                            <div
                                class="notifikasi-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $item->is_read ? 'sudah-dibaca' : 'belum-dibaca' }}"
                                style="width: 42px; height: 42px;"
                            >
                                @switch($item->tipe)
                                    @case('pinjaman')
                                        <i class="bi bi-cash-stack" aria-hidden="true"></i>
                                        @break

                                    @case('angsuran')
                                        <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                        @break

                                    @case('simpanan')
                                        <i class="bi bi-piggy-bank" aria-hidden="true"></i>
                                        @break

                                    @case('shu')
                                        <i class="bi bi-wallet2" aria-hidden="true"></i>
                                        @break

                                    @default
                                        <i class="bi bi-bell" aria-hidden="true"></i>
                                @endswitch
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-1">
                                    <h2 class="h6 mb-1 fw-semibold notifikasi-judul">
                                        {{ $item->judul }}

                                        @unless ($item->is_read)
                                            <span class="badge notifikasi-badge-baru ms-1">
                                                Baru
                                            </span>
                                        @endunless
                                    </h2>

                                    <time
                                        class="small notifikasi-waktu flex-shrink-0"
                                        datetime="{{ $item->created_at?->toIso8601String() }}"
                                    >
                                        {{ $item->created_at?->translatedFormat('d M Y, H:i') }}
                                    </time>
                                </div>

                                <p class="mb-2 notifikasi-pesan">
                                    {{ $item->pesan }}
                                </p>

                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge notifikasi-tipe text-capitalize">
                                        {{ $item->tipe }}
                                    </span>

                                    @if ($item->memilikiLink())
                                        <form
                                            method="POST"
                                            action="{{ route('notifikasi.baca', $item) }}"
                                        >
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-primary">
                                                Lihat detail
                                            </button>
                                        </form>
                                    @elseif (! $item->is_read)
                                        <form
                                            method="POST"
                                            action="{{ route('notifikasi.baca', $item) }}"
                                        >
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                Tandai dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center px-4 py-5">
                        <div class="mb-3 text-secondary">
                            <i class="bi bi-bell-slash fs-1" aria-hidden="true"></i>
                        </div>

                        <h2 class="h6 fw-semibold">Belum ada notifikasi</h2>

                        <p class="text-secondary mb-0">
                            @if ($filter === 'belum_dibaca')
                                Semua notifikasi sudah dibaca.
                            @elseif ($filter === 'sudah_dibaca')
                                Belum ada notifikasi yang dibaca.
                            @else
                                Notifikasi aktivitas akun Anda akan muncul di halaman ini.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        @if ($notifikasi->hasPages())
            <div class="mt-4">
                {{ $notifikasi->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-app-layout>
