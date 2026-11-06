{{-- Sambutan --}}

{{-- Ringkasan AO --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Menunggu Analisis"
            :value="$data['menungguAO'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Pinjaman Aktif"
            :value="$data['pinjamanAktif'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Anggota Aktif"
            :value="$data['totalAnggota'] ?? 0"
        />
    </div>

</div>

{{-- DETAIL PENGAJUAN MENUNGGU ANALISIS --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <h3 class="h5 fw-semibold text-dark mb-3">
            Pengajuan Menunggu Analisis
        </h3>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted">No. Pinjaman</th>
                        <th class="small fw-semibold text-muted">Anggota</th>
                        <th class="small fw-semibold text-muted">Jumlah Pengajuan</th>
                        <th class="small fw-semibold text-muted">Tenor</th>
                        <th class="small fw-semibold text-muted">Tanggal Pengajuan</th>
                        <th class="small fw-semibold text-muted">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['pengajuanAO'] ?? collect()) as $pinjaman)
                        <tr>
                            <td class="small fw-semibold text-dark">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td class="small">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="small text-nowrap">
                                Rp {{ number_format((float) ($pinjaman->jumlah_pinjaman ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tenor ?? 0 }} bulan
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->created_at?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('pinjaman.ao.show', $pinjaman) }}"
                                    class="btn btn-primary btn-sm text-nowrap"
                                >
                                    Analisis
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="small text-muted">
                                    Tidak ada pengajuan yang menunggu analisis.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- DETAIL PINJAMAN AKTIF --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <h3 class="h5 fw-semibold text-dark mb-3">
            Pinjaman Aktif
        </h3>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted">No. Pinjaman</th>
                        <th class="small fw-semibold text-muted">Anggota</th>
                        <th class="small fw-semibold text-muted">Jumlah Pengajuan</th>
                        <th class="small fw-semibold text-muted">Jumlah Disetujui</th>
                        <th class="small fw-semibold text-muted">Status</th>
                        <th class="small fw-semibold text-muted">Tanggal Pengajuan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['daftarPinjamanAktif'] ?? collect()) as $pinjaman)
                        @php
                            $statusPinjaman = [
                                'menunggu_cs' => 'Menunggu CS',
                                'menunggu_ao' => 'Menunggu AO',
                                'menunggu_kabag' => 'Menunggu Kabag',
                                'disetujui' => 'Disetujui',
                                'dicairkan' => 'Dicairkan',
                            ];

                            $warnaStatus = [
                                'menunggu_cs' => 'bg-secondary-subtle text-secondary',
                                'menunggu_ao' => 'bg-primary-subtle text-primary',
                                'menunggu_kabag' => 'bg-warning-subtle text-warning-emphasis',
                                'disetujui' => 'bg-success-subtle text-success',
                                'dicairkan' => 'bg-info-subtle text-info-emphasis',
                            ];
                        @endphp

                        <tr>
                            <td class="small fw-semibold text-dark">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td class="small">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="small text-nowrap">
                                Rp {{ number_format((float) ($pinjaman->jumlah_pinjaman ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="small text-nowrap">
                                @if ($pinjaman->jumlah_disetujui !== null)
                                    Rp {{ number_format((float) $pinjaman->jumlah_disetujui, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $warnaStatus[$pinjaman->status] ?? 'bg-secondary-subtle text-secondary' }}">
                                    {{ $statusPinjaman[$pinjaman->status] ?? ucfirst(str_replace('_', ' ', $pinjaman->status ?? 'Tidak diketahui')) }}
                                </span>
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->created_at?->format('d/m/Y') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="small text-muted">
                                    Belum ada data pinjaman aktif.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- DETAIL ANGGOTA AKTIF --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <h3 class="h5 fw-semibold text-dark mb-3">
            Anggota Aktif
        </h3>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted">Kode Anggota</th>
                        <th class="small fw-semibold text-muted">Nama Anggota</th>
                        <th class="small fw-semibold text-muted">No. HP</th>
                        <th class="small fw-semibold text-muted">Pekerjaan</th>
                        <th class="small fw-semibold text-muted">Tanggal Daftar</th>
                        <th class="small fw-semibold text-muted">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['daftarAnggotaAktif'] ?? collect()) as $anggota)
                        <tr>
                            <td class="small fw-semibold text-dark">
                                {{ $anggota->kode_anggota ?? '-' }}
                            </td>

                            <td class="small">
                                {{ $anggota->user?->nama ?? '-' }}
                            </td>

                            <td class="small text-nowrap">
                                {{ $anggota->no_hp ?? '-' }}
                            </td>

                            <td class="small">
                                {{ $anggota->pekerjaan ?? '-' }}
                            </td>

                            <td class="small text-nowrap">
                                {{ $anggota->tanggal_daftar
                                    ? \Illuminate\Support\Carbon::parse($anggota->tanggal_daftar)->format('d/m/Y')
                                    : '-' }}
                            </td>

                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="small text-muted">
                                    Belum ada anggota aktif.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>