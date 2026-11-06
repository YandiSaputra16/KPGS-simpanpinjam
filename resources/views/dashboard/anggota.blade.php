{{-- Sambutan --}}

{{-- Ringkasan Anggota --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-sm-6 col-lg-3">
        <x-dashboard-stat-card
            title="Simpanan Pokok"
            :value="'Rp ' . number_format($data['simpananPokok'] ?? 0, 0, ',', '.')"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <x-dashboard-stat-card
            title="Simpanan Wajib"
            :value="'Rp ' . number_format($data['simpananWajib'] ?? 0, 0, ',', '.')"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <x-dashboard-stat-card
            title="Simpanan Sukarela"
            :value="'Rp ' . number_format($data['simpananSukarela'] ?? 0, 0, ',', '.')"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <x-dashboard-stat-card
            title="Pinjaman Aktif"
            :value="$data['pinjamanAktif'] ?? 0"
        />
    </div>

</div>
 {{-- INFORMASI PENGAJUAN --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-lg-9">

                        <h5 class="fw-semibold mb-2">
                             Pengajuan Pinjaman
                        </h5>

                        <p class="text-muted mb-0">
                            Setelah pengajuan dikirim, pinjaman akan diproses
                            oleh petugas KPGS secara bertahap. Status pengajuan
                            akan diperbarui setelah proses pemeriksaan selesai.
                        </p>

                    </div>

                    <div class="col-lg-3 text-lg-end mt-3 mt-lg-0">

                        <a href="{{ route('pinjaman.create') }}"
                           class="btn btn-primary">
                            Ajukan Pinjaman
                        </a>

                    </div>

                </div>

            </div>

        </div>

            {{-- Pencairan Simpanan --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row
                                justify-content-between align-items-md-center gap-3">
                        <div>
                            <h3 class="h5 fw-semibold text-dark mb-2">
                                Pencairan Simpanan
                            </h3>

                            <p class="text-muted small mb-0">
                                Ajukan pencairan simpanan  dan pengajuan akan diproses oleh petugas KPGS secara bertahap. Status pengajuan akan diperbarui setelah proses pemeriksaan selesai.
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a
                                href="{{ route('pencairan-simpanan.saya.create') }}"
                                class="btn btn-primary"
                            >
                                Ajukan Pencairan
                            </a>
                        </div>
                    </div>
                </div>
            </div>


{{-- Informasi Pinjaman --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div class="mb-4">
            <h3 class="h5 fw-semibold text-dark mb-1">
                Pinjaman Saya
            </h3>

            <p class="text-muted small mb-0">
                Ringkasan pinjaman yang sedang diproses atau berjalan.
            </p>
        </div>

        @if ($data['pinjaman'] ?? null)

            @php
                $pinjaman = $data['pinjaman'];
            @endphp

            <div class="row g-3">

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-light border rounded p-3 h-100">
                        <p class="small text-muted mb-2">
                            Nomor Pinjaman
                        </p>

                        <p class="small fw-semibold text-dark mb-0">
                            {{ $pinjaman->no_pinjaman }}
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-light border rounded p-3 h-100">
                        <p class="small text-muted mb-2">
                            Jumlah Disetujui
                        </p>

                        <p class="small fw-semibold text-dark mb-0">
                            Rp {{ number_format($pinjaman->jumlah_disetujui ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-light border rounded p-3 h-100">
                        <p class="small text-muted mb-2">
                            Angsuran per Bulan
                        </p>

                        <p class="small fw-semibold text-dark mb-0">
                            Rp {{ number_format($pinjaman->angsuran_per_bulan ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="bg-light border rounded p-3 h-100">
                        <p class="small text-muted mb-2">
                            Status
                        </p>

                        <p class="small fw-semibold text-dark text-capitalize mb-0">
                            {{ str_replace('_', ' ', $pinjaman->status) }}
                        </p>
                    </div>
                </div>

            </div>

        @else

            <div class="bg-light border rounded p-4 text-center">

                <p class="small fw-medium text-muted mb-1">
                    Belum ada pinjaman aktif.
                </p>

                <p class="small text-muted mb-0">
                    Pengajuan pinjaman dapat dilakukan melalui menu Pinjaman Saya.
                </p>

            </div>

        @endif

    </div>

</div>

{{-- Informasi Anggota --}}
<div class="card border-0 shadow-sm">

   
</div>