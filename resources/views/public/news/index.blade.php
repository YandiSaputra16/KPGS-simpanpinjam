
<x-public-layout :pengaturan="$pengaturan">
    <x-slot name="title">
        Berita KPGS
    </x-slot>

    <section class="bg-light">
        <div class="container-fluid px-4 px-xl-5 py-5">

            {{-- Header --}}
            <div class="mb-4">
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
            @if ($berita->isNotEmpty())

                <div class="row g-4 align-items-start">

                    @foreach ($berita as $item)

                        {{-- Satu Kesatuan Kartu Berita --}}
                        <div class="col-12 col-md-6 col-xl-4 news-item">

                            <article class="card border-0 shadow-sm overflow-hidden h-100">

                                {{-- Gambar Berita --}}
                                @if ($item->gambar)
                                    <img
                                        src="{{ asset('storage/' . $item->gambar) }}"
                                        alt="{{ $item->judul }}"
                                        class="card-img-top w-100"
                                        style="height: auto; display: block;"
                                    >
                                @else
                                    <div
                                        class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary"
                                        style="height: 200px;"
                                    >
                                        <span class="small">
                                            Tidak ada gambar
                                        </span>
                                    </div>
                                @endif

                                {{-- Konten Berita --}}
                                <div class="card-body p-4">

                                    {{-- Tanggal --}}
                                    <div class="small text-secondary">
                                        <i class="bi bi-calendar3 me-1"></i>
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
                                            {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 180) }}
                                        </p>
                                    @endif

                                    {{-- Tombol Baca Selengkapnya --}}
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm mt-3"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#berita-{{ $item->id }}"
                                        aria-expanded="false"
                                        aria-controls="berita-{{ $item->id }}"
                                    >
                                        <i class="bi bi-book me-1"></i>
                                        Baca Selengkapnya
                                    </button>

                                    {{-- Isi Lengkap Dalam Card yang Sama --}}
                                    <div
                                        id="berita-{{ $item->id }}"
                                        class="collapse"
                                    >
                                        <div class="border-top mt-4 pt-4">

                                            <div class="small text-primary fw-semibold text-uppercase mb-3">
                                                Isi Lengkap Berita
                                            </div>

                                            <div class="text-secondary lh-lg kpgs-pre-line">
                                                {{ $item->isi }}
                                            </div>

                                            {{-- Tombol Tutup --}}
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-sm mt-4"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#berita-{{ $item->id }}"
                                            >
                                                <i class="bi bi-x-lg me-1"></i>
                                                Tutup Berita
                                            </button>

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

    {{-- Mengatur Lebar Card Ketika Detail Dibuka --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.querySelectorAll('.news-item .collapse').forEach(function (detail) {

                const cardColumn = detail.closest('.news-item');

                detail.addEventListener('show.bs.collapse', function () {
                    cardColumn.classList.remove('col-md-6', 'col-xl-4');
                    cardColumn.classList.add('col-12');
                });

                detail.addEventListener('hidden.bs.collapse', function () {
                    cardColumn.classList.remove('col-12');
                    cardColumn.classList.add('col-md-6', 'col-xl-4');
                });

            });

        });
    </script>

</x-public-layout>
