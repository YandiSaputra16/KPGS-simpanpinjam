<div class="card border-0 shadow-sm mb-4">

    {{-- ==========================================================
        HEADER TINDAKAN CUSTOMER SERVICE
    =========================================================== --}}

    <div class="card-header bg-white py-3">

        <h5 class="fw-bold mb-1">
            Tindakan Customer Service
        </h5>

        <p class="text-muted small mb-0">
            Periksa kelengkapan administrasi pengajuan sebelum
            meneruskannya kepada Account Officer.
        </p>

    </div>


    <div class="card-body">

        <div class="row g-4">

            {{-- ==================================================
                TERUSKAN KE AO
            =================================================== --}}

            <div class="col-12 col-lg-6">

                <div class="border rounded p-4 h-100">

                    <h6 class="fw-bold mb-2">
                        Teruskan ke AO
                    </h6>

                    <p class="text-muted small mb-3">
                        Pengajuan yang telah memenuhi pemeriksaan
                        administrasi dapat diteruskan kepada
                        Account Officer untuk dilakukan analisis.
                    </p>


                    {{-- STATUS SAAT INI --}}

                    <div class="bg-light border rounded p-3 mb-4">

                        <div class="d-flex justify-content-between align-items-center gap-3">

                            <span class="small text-muted">
                                Status saat ini
                            </span>

                            <span class="badge bg-warning text-dark">
                                Menunggu CS
                            </span>

                        </div>

                    </div>


                    {{-- FORM TERUSKAN KE AO --}}

                    <form
                        method="POST"
                        action="{{ route('pinjaman.cs.proses', $pinjaman) }}"
                        data-confirm-submit="Apakah pengajuan ini sudah diperiksa dan ingin diteruskan ke AO?"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Proses ke AO
                        </button>

                    </form>

                </div>

            </div>


            {{-- ==================================================
                TOLAK PENGAJUAN
            =================================================== --}}

            <div class="col-12 col-lg-6">

                <div class="border border-danger rounded p-4 h-100">

                    <h6 class="fw-bold text-danger mb-2">
                        Tolak Pengajuan
                    </h6>

                    <p class="text-muted small mb-3">
                        Jika data administrasi tidak memenuhi persyaratan,
                        CS dapat menolak pengajuan dengan memberikan alasan
                        yang jelas.
                    </p>


                    {{-- FORM PENOLAKAN --}}

                    <form
                        method="POST"
                        action="{{ route('pinjaman.cs.tolak', $pinjaman) }}"
                    >

                        @csrf


                        {{-- ALASAN PENOLAKAN --}}

                        <div class="mb-3">

                            <label
                                for="alasan"
                                class="form-label fw-semibold"
                            >
                                Alasan Penolakan
                                <span class="text-danger">*</span>
                            </label>


                            <textarea
                                id="alasan"
                                name="alasan"
                                rows="5"
                                required
                                maxlength="1000"
                                class="form-control @error('alasan') is-invalid @enderror"
                                placeholder="Tuliskan alasan penolakan..."
                            >{{ old('alasan') }}</textarea>


                            @error('alasan')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- TOMBOL TOLAK --}}

                        <button
                            type="submit"
                            class="btn btn-outline-danger"
                            data-confirm="Apakah Anda yakin ingin menolak pengajuan ini?"
                        >
                            Tolak Pengajuan
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>