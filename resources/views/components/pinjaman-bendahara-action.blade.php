<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <h5 class="fw-bold mb-1">
            Tindakan Bendahara
        </h5>

        <p class="text-muted small mb-0">
            Proses pencairan dana pinjaman yang telah mendapatkan
            persetujuan dari Kepala Bagian.
        </p>

    </div>


    <div class="card-body">

        <div class="row g-4">

            {{-- ==================================================
                INFORMASI PENCAIRAN
            =================================================== --}}

            <div class="col-12">

                <div class="bg-light border rounded p-3">

                    <div class="row g-3">

                        <div class="col-12 col-md-4">

                            <div class="small text-muted mb-1">
                                Status saat ini
                            </div>

                            <span class="badge bg-success">
                                Disetujui
                            </span>

                        </div>


                        <div class="col-12 col-md-4">

                            <div class="small text-muted mb-1">
                                Jumlah Disetujui
                            </div>

                            <div class="fw-bold">
                                Rp {{ number_format(
                                    (float) ($pinjaman->jumlah_disetujui ?? 0),
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </div>

                        </div>


                        <div class="col-12 col-md-4">

                            <div class="small text-muted mb-1">
                                Angsuran per Bulan
                            </div>

                            <div class="fw-bold">
                                Rp {{ number_format(
                                    (float) ($pinjaman->angsuran_per_bulan ?? 0),
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ==================================================
                PROSES PENCAIRAN
            =================================================== --}}

            <div class="col-12">

                <div class="border rounded p-4">

                    <h6 class="fw-bold mb-2">
                        Pencairan Pinjaman
                    </h6>

                    <p class="text-muted small mb-4">
                        Pastikan data pinjaman telah sesuai sebelum
                        melakukan pencairan dana kepada anggota.
                    </p>


                    @php($rekeningLengkap = $pinjaman->anggota?->memilikiRekeningLengkap() ?? false)
                    <div class="bg-light border rounded p-3 mb-3">
                        <h6 class="fw-bold mb-3">Tujuan Transfer Anggota</h6>
                        @if ($rekeningLengkap)
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="small text-muted">Nama Bank</div>
                                    <div class="fw-semibold">{{ $pinjaman->anggota->nama_bank }}</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="small text-muted">Nomor Rekening</div>
                                    <div class="fw-semibold">{{ $pinjaman->anggota->nomor_rekening }}</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="small text-muted">Atas Nama</div>
                                    <div class="fw-semibold">{{ $pinjaman->anggota->atas_nama_rekening }}</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                Data rekening anggota belum lengkap. Lengkapi data rekening sebelum pencairan.
                            </div>
                        @endif
                    </div>

                    <form
                        method="POST"
                        action="{{ route('pinjaman.bendahara.cairkan', $pinjaman) }}"
                        data-confirm-submit="Apakah Anda yakin ingin mencairkan pinjaman ini?"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-primary"
                            @disabled(!$rekeningLengkap)
                        >
                            Cairkan Pinjaman
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>