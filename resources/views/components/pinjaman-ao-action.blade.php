@if ($pinjaman->status === 'menunggu_ao')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-1">
                Analisis dan Rekomendasi AO
            </h5>
            <p class="text-muted small mb-0">
                Tentukan jumlah rekomendasi pinjaman berdasarkan hasil analisis
                data anggota dan data pendukung pengajuan.
            </p>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                {{-- REKOMENDASI AO --}}
                <div class="col-12 col-lg-6">
                    <div class="border rounded p-4 h-100">
                        <h6 class="fw-bold mb-2">
                            Berikan Rekomendasi
                        </h6>

                        <p class="text-muted small mb-4">
                            Nominal rekomendasi tidak boleh melebihi jumlah pinjaman
                            yang diajukan oleh anggota.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('pinjaman.ao.proses', $pinjaman) }}"
                        >
                            @csrf

                            <div class="mb-4">
                                <label
                                    for="jumlah_rekomendasi"
                                    class="form-label fw-semibold"
                                >
                                    Jumlah Rekomendasi
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>

                                    <input
                                        type="number"
                                        id="jumlah_rekomendasi"
                                        name="jumlah_rekomendasi"
                                        value="{{ old('jumlah_rekomendasi', $pinjaman->jumlah_rekomendasi) }}"
                                        min="1"
                                        max="{{ $pinjaman->jumlah_pinjaman }}"
                                        step="0.01"
                                        required
                                        class="form-control @error('jumlah_rekomendasi') is-invalid @enderror"
                                        placeholder="Masukkan nominal rekomendasi"
                                    >

                                    @error('jumlah_rekomendasi')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-text">
                                    Pengajuan anggota:
                                    <strong>
                                        Rp {{ number_format(
                                            (float) $pinjaman->jumlah_pinjaman,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </strong>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label
                                    for="catatan"
                                    class="form-label fw-semibold"
                                >
                                    Catatan Analisis
                                </label>

                                <textarea
                                    id="catatan"
                                    name="catatan"
                                    rows="4"
                                    maxlength="1000"
                                    class="form-control @error('catatan') is-invalid @enderror"
                                    placeholder="Tuliskan catatan atau hasil analisis AO..."
                                >{{ old(
                                    'catatan',
                                    $pinjaman->approvals
                                        ->firstWhere('level', 'ao')
                                        ?->catatan
                                ) }}</textarea>

                                @error('catatan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-confirm="Simpan rekomendasi dan teruskan pengajuan ini ke Kabag?"
                            >
                                <i class="bi bi-check-circle me-1"></i>
                                Simpan Rekomendasi
                            </button>
                        </form>
                    </div>
                </div>

                {{-- PENOLAKAN AO --}}
                <div class="col-12 col-lg-6">
                    <div class="border border-danger rounded p-4 h-100">
                        <h6 class="fw-bold text-danger mb-2">
                            Tolak Pengajuan
                        </h6>

                        <p class="text-muted small mb-4">
                            Gunakan tindakan ini apabila berdasarkan hasil analisis,
                            pengajuan tidak dapat diteruskan kepada Kabag.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('pinjaman.ao.tolak', $pinjaman) }}"
                        >
                            @csrf

                            <div class="mb-4">
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

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                                data-confirm="Tolak pengajuan pinjaman ini berdasarkan hasil analisis AO?"
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
@endif