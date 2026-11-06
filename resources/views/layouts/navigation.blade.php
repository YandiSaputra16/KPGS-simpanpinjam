
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top">
    <div class="container-fluid px-3 px-lg-4">

        {{-- BRAND KPGS --}}
        <a
            href="{{ route('dashboard') }}"
            class="navbar-brand d-flex align-items-center text-decoration-none me-0"
        >
            <img
                src="{{ asset('image/kpgs logo 2.jpg') }}"
                alt="Logo KPGS"
                class="kpgs-navbar-logo me-2"
            >

            <div class="lh-sm">
                <div class="fw-bold text-dark">KPGS</div>
                <div class="small text-secondary">Simpan Pinjam</div>
            </div>
        </a>

        {{-- TOMBOL SIDEBAR MOBILE --}}
        <button
            type="button"
            class="btn btn-outline-secondary d-lg-none"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar"
            aria-label="Buka menu"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                fill="currentColor"
                viewBox="0 0 16 16"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M2.5 12.5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"
                />
            </svg>
        </button>

        {{-- NOTIFIKASI PENGGUNA --}}
        @auth
            @php
                $jumlahNotifikasiBelumDibaca = \App\Models\Notifikasi::milikUser((int) Auth::id())
                    ->belumDibaca()
                    ->count();

                $notifikasiTerbaru = \App\Models\Notifikasi::milikUser((int) Auth::id())
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp

            <div class="dropdown ms-auto me-2">
                <button
                    type="button"
                    class="btn btn-light border position-relative d-flex align-items-center justify-content-center"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="outside"
                    aria-expanded="false"
                    aria-label="Notifikasi"
                    style="width: 42px; height: 42px;"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        fill="currentColor"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                    >
                        <path d="M8 16a2 2 0 0 0 1.985-1.75h-3.97A2 2 0 0 0 8 16zm.104-14.995a1 1 0 0 0-.208 0A1.5 1.5 0 0 0 6.5 2.5v.258C4.79 3.17 3.5 4.7 3.5 6.5c0 1.098-.26 2.16-.75 3.143l-.5 1A.75.75 0 0 0 2.92 11.75h10.16a.75.75 0 0 0 .67-1.107l-.5-1A7.03 7.03 0 0 1 12.5 6.5c0-1.8-1.29-3.33-3-3.742V2.5a1.5 1.5 0 0 0-1.396-1.495zM5 6.5a3 3 0 1 1 6 0c0 1.33.315 2.64.915 3.83l.085.17H4l.085-.17A8.53 8.53 0 0 0 5 6.5z"/>
                    </svg>

                    @if ($jumlahNotifikasiBelumDibaca > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $jumlahNotifikasiBelumDibaca > 99 ? '99+' : $jumlahNotifikasiBelumDibaca }}
                            <span class="visually-hidden">notifikasi belum dibaca</span>
                        </span>
                    @endif
                </button>

                <div
                    class="dropdown-menu dropdown-menu-end shadow p-0 mt-2 kpgs-notifikasi-dropdown"
                    style="width: min(360px, 92vw);"
                >
                    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
                        <span class="fw-semibold text-dark">Notifikasi</span>

                        @if ($jumlahNotifikasiBelumDibaca > 0)
                            <span class="badge bg-primary">
                                {{ $jumlahNotifikasiBelumDibaca }} belum dibaca
                            </span>
                        @endif
                    </div>

                    <div class="list-group list-group-flush">
                        @forelse ($notifikasiTerbaru as $item)
                            <form
                                method="POST"
                                action="{{ route('notifikasi.baca', $item) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="list-group-item list-group-item-action notifikasi-item text-start px-3 py-3 {{ $item->is_read ? 'sudah-dibaca' : 'belum-dibaca' }}"
                                >
                                    <span class="d-flex align-items-start justify-content-between gap-2">
                                        <span class="fw-semibold small notifikasi-judul">
                                            {{ $item->judul }}
                                        </span>

                                        @unless ($item->is_read)
                                            <span class="badge notifikasi-badge-baru flex-shrink-0">
                                                Baru
                                            </span>
                                        @endunless
                                    </span>

                                    <span class="d-block small notifikasi-pesan mt-1">
                                        {{ \Illuminate\Support\Str::limit($item->pesan, 95) }}
                                    </span>

                                    <span class="d-block notifikasi-waktu mt-1" style="font-size: .75rem;">
                                        {{ $item->created_at?->diffForHumans() }}
                                    </span>
                                </button>
                            </form>
                        @empty
                            <div class="text-center text-secondary px-3 py-4 small">
                                Belum ada notifikasi.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-top p-2">
                        <a
                            href="{{ route('notifikasi.index') }}"
                            class="btn btn-sm btn-outline-primary w-100"
                        >
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
            </div>
        @endauth

        {{-- PROFIL PENGGUNA --}}
        @auth
            <div class="dropdown">
                <button
                    type="button"
                    class="btn btn-light border d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold kpgs-avatar-36">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </span>

                    <span class="d-none d-md-block text-start lh-sm">
                        <span class="d-block fw-semibold text-dark">
                            {{ Auth::user()->nama }}
                        </span>

                        <span class="d-block small text-secondary text-capitalize">
                            {{ Auth::user()->role }}
                        </span>
                    </span>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="currentColor"
                        viewBox="0 0 16 16"
                        class="text-secondary"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M1.646 5.646a.5.5 0 0 1 .708 0L8 11.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"
                        />
                    </svg>
                </button>

                {{-- DROPDOWN PROFIL --}}
                <div class="dropdown-menu dropdown-menu-end shadow kpgs-profile-menu">
                    <div class="px-3 py-2">
                        <div class="fw-semibold text-dark">
                            {{ Auth::user()->nama }}
                        </div>

                        <div class="small text-secondary">
                            {{ Auth::user()->email }}
                        </div>

                        <div class="mt-1">
                            <span class="badge bg-primary text-capitalize">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                    </div>

                    <hr class="dropdown-divider">

                    <a
                        href="{{ route('profile.edit') }}"
                        class="dropdown-item d-flex align-items-center gap-2"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="17"
                            height="17"
                            fill="currentColor"
                            viewBox="0 0 16 16"
                            aria-hidden="true"
                        >
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path
                                fill-rule="evenodd"
                                d="M14 14s-1-1-1-4-1-4-5-4-5 1-5 4 1 4 1 4h10z"
                            />
                        </svg>
                        Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item d-flex align-items-center gap-2 text-danger"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="17"
                                height="17"
                                fill="currentColor"
                                viewBox="0 0 16 16"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"
                                />
                                <path
                                    fill-rule="evenodd"
                                    d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"
                                />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        @endauth

    </div>
</nav>
