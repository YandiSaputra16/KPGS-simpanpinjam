<x-public-layout :pengaturan="$pengaturan">
    <x-slot name="title">
        Berita KPGS
    </x-slot>

    <section class="bg-light">
        <div class="container py-5">

            {{-- Header --}}
            <div class="mb-5 col-12 col-lg-8">
                <span class="small fw-semibold text-primary text-uppercase">
                    Informasi KPGS
                </span>

                <h1 class="display-6 fw-bold text-dark mt-2 mb-0">
                    Berita KPGS
                </h1>

                <p class="text-secondary lh-lg mt-3 mb-0">
                    Informasi dan berita terbaru dari Koperasi Peternak Garut Selatan.
                </p>
            </div>

            {{-- Daftar Berita --}}
            @if ($berita->count())

                <div class="row g-4">

                    @foreach ($berita as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="card border-0 shadow-sm h-100 overflow-hidden">

                                {{-- Gambar --}}
                                @if ($item->gambar)
                                    <img
                                        src="{{ asset('storage/' . $item->gambar) }}"
                                        alt="{{ $item->judul }}"
                                        class="card-img-top object-fit-cover kpgs-image-height-224"
                                    >
                                @else
                                    <div
                                        class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary kpgs-image-height-224"
                                    >
                                        <span class="small">
                                            Tidak ada gambar
                                        </span>
                                    </div>
                                @endif

                                {{-- Isi Card --}}
                                <div class="card-body d-flex flex-column p-4">

                                    {{-- Tanggal --}}
                                    <div class="small text-secondary">
                                        {{ $item->published_at?->translatedFormat('d F Y') }}
                                    </div>

                                    {{-- Judul --}}
                                    <h2 class="h5 fw-bold text-dark lh-base mt-2 mb-0">
                                        {{ $item->judul }}
                                    </h2>

                                    {{-- Ringkasan --}}
                                    @if ($item->ringkasan)
                                        <p class="text-secondary lh-lg mt-3 mb-0">
                                            {{ $item->ringkasan }}
                                        </p>
                                    @else
                                        <p class="text-secondary lh-lg mt-3 mb-0">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 150) }}
                                        </p>
                                    @endif

                                    {{-- Tombol dan Isi Lengkap --}}
                                    <div class="mt-auto pt-4">

                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#berita-{{ $item->id }}"
                                            aria-expanded="false"
                                            aria-controls="berita-{{ $item->id }}"
                                        >
                                            <i class="bi bi-book me-1"></i>
                                            Baca Selengkapnya
                                        </button>

                                        {{-- Isi Lengkap --}}
                                        <div
                                            id="berita-{{ $item->id }}"
                                            class="collapse"
                                        >
                                            <div class="border-top mt-4 pt-4">
                                                <div
                                                    class="text-secondary lh-lg kpgs-pre-line"
                                                >
                                                    {{ $item->isi }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </article>
                        </div>
                    @endforeach

                </div>

                {{-- Pagination --}}
                @if ($berita->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $berita->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            @else

                {{-- Empty State --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 px-4">

                        <i class="bi bi-newspaper display-5 text-secondary mb-3"></i>

                        <h2 class="h5 fw-bold text-dark">
                            Belum Ada Berita
                        </h2>

                        <p class="text-secondary mb-0">
                            Belum ada berita yang dipublikasikan oleh KPGS.
                        </p>

                    </div>
                </div>

            @endif

        </div>
    </section>
</x-public-layout>