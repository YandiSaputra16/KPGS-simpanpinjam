<x-public-layout :pengaturan="$pengaturan">
    <x-slot name="title">
        Beranda
    </x-slot>

    {{-- Hero --}}
    <section class="bg-white">
        <div class="container py-5">
            <div class="row align-items-center g-5 py-lg-5">

                <div class="col-12 col-lg-6">
                    <h1 class="display-4 fw-bold text-dark mt-3 mb-0">
                        Koperasi Peternak
                        <span class="text-primary">
                            Garut Selatan
                        </span>
                    </h1>

                    <p class="fs-5 text-secondary lh-lg mt-4 mb-0">
                        {{ $pengaturan->tentang ?: 'Membangun koperasi yang memberikan manfaat bagi anggota dan masyarakat melalui pelayanan yang terpercaya dan berkelanjutan.' }}
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a
                            href="{{ route('login') }}"
                            class="btn btn-primary px-4 py-3"
                        >
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Login Sistem
                        </a>

                        <a
                            href="#tentang"
                            class="btn btn-outline-secondary px-4 py-3"
                        >
                            <i class="bi bi-info-circle me-1"></i>
                            Mengenal KPGS
                        </a>
                    </div>
                </div>

                {{-- Logo KPGS --}}
                <div class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm bg-light overflow-hidden">
                        <div class="d-flex align-items-center justify-content-center p-4 p-lg-5 kpgs-public-hero">
                            <img
                                src="{{ asset('image/kpgs logo 3.jpg') }}"
                                alt="Logo Koperasi Peternak Garut Selatan"
                                class="img-fluid object-fit-contain kpgs-public-logo"
                            >
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Tentang --}}
    <section
        id="tentang"
        class="bg-light"
    >
        <div class="container py-5">

            <div class="col-12 col-lg-9">
                <span class="small fw-semibold text-primary text-uppercase">
                    Tentang KPGS
                </span>

                <h2 class="display-6 fw-bold text-dark mt-2 mb-0">
                    Mengenal Koperasi Peternak Garut Selatan
                </h2>

                <div class="text-secondary lh-lg mt-4 kpgs-pre-line">
                    {{ $pengaturan->tentang ?: 'Informasi tentang KPGS belum diisi oleh administrator.' }}
                </div>
            </div>

            @if ($pengaturan->sejarah)
                <div class="row g-4 mt-4">

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 p-lg-5">
                                <h3 class="h4 fw-bold text-dark">
                                    Sejarah
                                </h3>

                                <div class="text-secondary lh-lg mt-3 kpgs-pre-line">
                                    {{ $pengaturan->sejarah }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column gap-4 h-100">

                            @if ($pengaturan->visi)
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <h3 class="h4 fw-bold text-dark">
                                            Visi
                                        </h3>

                                        <div class="text-secondary lh-lg mt-3 kpgs-pre-line">
                                            {{ $pengaturan->visi }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($pengaturan->misi)
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <h3 class="h4 fw-bold text-dark">
                                            Misi
                                        </h3>

                                        <div class="text-secondary lh-lg mt-3 kpgs-pre-line">
                                            {{ $pengaturan->misi }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            @endif

        </div>
    </section>

    {{-- Layanan --}}
    <section
        id="layanan"
        class="bg-white"
    >
        <div class="container py-5">

            <div class="col-12 col-lg-9">
                <span class="small fw-semibold text-primary text-uppercase">
                    Layanan
                </span>

                <h2 class="display-6 fw-bold text-dark mt-2 mb-0">
                    Layanan KPGS
                </h2>

                <div class="text-secondary lh-lg mt-4 kpgs-pre-line">
                    {{ $pengaturan->layanan ?: 'Informasi layanan KPGS belum diisi oleh administrator.' }}
                </div>
            </div>

        </div>
    </section>

    {{-- Keanggotaan --}}
    <section
        id="keanggotaan"
        class="bg-light"
    >
        <div class="container py-5">

            <div class="col-12 col-lg-9">
                <span class="small fw-semibold text-primary text-uppercase">
                    Keanggotaan
                </span>

                <h2 class="display-6 fw-bold text-dark mt-2 mb-0">
                    Syarat Menjadi Anggota
                </h2>

                <div class="text-secondary lh-lg mt-4 kpgs-pre-line">
                    {{ $pengaturan->syarat_anggota ?: 'Informasi persyaratan anggota belum diisi oleh administrator.' }}
                </div>
            </div>

        </div>
    </section>

    {{-- Berita Terbaru --}}
    <section
        id="berita"
        class="bg-light"
    >
        <div class="container py-5">

            <div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-between gap-3">
                <div>
                    <span class="small fw-semibold text-primary text-uppercase">
                        Informasi Terkini
                    </span>

                    <h2 class="display-6 fw-bold text-dark mt-2 mb-0">
                        Berita Terbaru
                    </h2>

                    <p class="text-secondary mt-3 mb-0">
                        Informasi dan kegiatan terbaru dari Koperasi Peternak Garut Selatan.
                    </p>
                </div>
            </div>

            @if ($beritaTerbaru->isNotEmpty())

                <div class="row g-4 mt-4">

                    @foreach ($beritaTerbaru as $berita)

                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="card border-0 shadow-sm h-100 overflow-hidden">

                                {{-- Gambar berita --}}
                                @if ($berita->gambar)
                                    <img
                                        src="{{ asset('storage/' . $berita->gambar) }}"
                                        alt="{{ $berita->judul }}"
                                        class="card-img-top object-fit-cover kpgs-image-height-208"
                                    >
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary kpgs-image-height-208">
                                        <span class="small">
                                            KPGS
                                        </span>
                                    </div>
                                @endif

                                {{-- Konten berita --}}
                                <div class="card-body p-4">
                                    <div class="small text-secondary">
                                        {{ $berita->published_at?->format('d M Y') }}
                                    </div>

                                    <h3 class="h5 fw-bold text-dark mt-2 mb-0">
                                        {{ $berita->judul }}
                                    </h3>

                                    @if ($berita->ringkasan)
                                        <p class="small text-secondary lh-lg mt-3 mb-0">
                                            {{ $berita->ringkasan }}
                                        </p>
                                    @endif
                                </div>

                            </article>
                        </div>

                    @endforeach

                </div>

            @else

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-newspaper display-5 text-secondary mb-3"></i>

                        <h3 class="h5 fw-semibold text-dark">
                            Belum ada berita terbaru
                        </h3>

                        <p class="small text-secondary mb-0">
                            Informasi terbaru KPGS akan ditampilkan di bagian ini.
                        </p>
                    </div>
                </div>

            @endif

        </div>
    </section>

    {{-- Pinjaman --}}
    <section
        id="pinjaman"
        class="bg-white"
    >
        <div class="container py-5">

            <div class="col-12 col-lg-9">
                <span class="small fw-semibold text-primary text-uppercase">
                    Pinjaman
                </span>

                <h2 class="display-6 fw-bold text-dark mt-2 mb-0">
                    Prosedur Pengajuan Pinjaman
                </h2>

                <div class="text-secondary lh-lg mt-4 kpgs-pre-line">
                    {{ $pengaturan->prosedur_pinjaman ?: 'Informasi prosedur pinjaman belum diisi oleh administrator.' }}
                </div>
            </div>

            <div class="mt-4">
                <a
                    href="{{ route('login') }}"
                    class="btn btn-primary px-4 py-3"
                >
                    <i class="bi bi-box-arrow-in-right me-1"></i>
                    Login untuk Mengakses Sistem
                </a>
            </div>

        </div>
    </section>

</x-public-layout>