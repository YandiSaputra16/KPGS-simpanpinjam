
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-1">
            Tindakan Kepala Bagian
        </h5>

        <p class="text-muted small mb-0">
            Periksa hasil analisis Account Officer dan tentukan
            persetujuan akhir terhadap pengajuan pinjaman.
        </p>
    </div>

    <div class="card-body">
        <div class="row g-4">

            {{-- PERSETUJUAN PINJAMAN --}}
            <div class="col-12 col-lg-6">
                <div class="border rounded p-4 h-100">
                    <h6 class="fw-bold mb-2">
                        Setujui Pinjaman
                    </h6>

                    <p class="text-muted small mb-3">
                        Pengajuan yang telah memenuhi pertimbangan
                        dapat disetujui dan diteruskan kepada Bendahara
                        untuk proses pencairan.
                    </p>

                    <div class="bg-light border rounded p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <span class="small text-muted">
                                Status saat ini
                            </span>

                            <span class="badge bg-warning text-dark">
                                Menunggu Kabag
                            </span>
                        </div>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('pinjaman.kabag.setujui', $pinjaman) }}"
                        data-confirm-submit="Apakah Anda yakin ingin menyetujui pengajuan pinjaman ini?"
                    >
                        @csrf

                        <div class="mb-3">
                            <label
                                for="catatan_kabag"
                                class="form-label fw-semibold"
                            >
                                Catatan Keputusan
                                <span class="text-muted small">(Opsional)</span>
                            </label>

                            <textarea
                                id="catatan_kabag"
                                name="catatan"
                                rows="4"
                                maxlength="1000"
                                class="form-control @error('catatan') is-invalid @enderror"
                                placeholder="Tuliskan pertimbangan atau catatan persetujuan..."
                            >{{ old('catatan') }}</textarea>

                            @error('catatan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Setujui Pinjaman
                        </button>
                    </form>
                </div>
            </div>

            {{-- PENOLAKAN PENGAJUAN --}}
            <div class="col-12 col-lg-6">
                <div class="border border-danger rounded p-4 h-100">
                    <h6 class="fw-bold text-danger mb-2">
                        Tolak Pengajuan
                    </h6>

                    <p class="text-muted small mb-3">
                        Jika hasil analisis tidak memenuhi pertimbangan
                        persetujuan, Kabag dapat menolak pengajuan dengan
                        memberikan alasan yang jelas.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('pinjaman.kabag.tolak', $pinjaman) }}"
                    >
                        @csrf

                        <div class="mb-3">
                            <label
                                for="alasan_kabag"
                                class="form-label fw-semibold"
                            >
                                Alasan Penolakan
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                id="alasan_kabag"
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

                        <button
                            type="submit"
                            class="btn btn-outline-danger"
                            data-confirm="Apakah Anda yakin ingin menolak pengajuan ini?"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Tolak Pengajuan
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
