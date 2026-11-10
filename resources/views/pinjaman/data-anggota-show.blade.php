<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h2 class="h4 mb-1 fw-bold">Detail Data Anggota</h2>
                <p class="text-muted small mb-0">Ringkasan profil dan riwayat transaksi anggota.</p>
            </div>
            <a href="{{ route('data-anggota.ao.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Anggota
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                    <div>
                        <div class="small text-muted mb-1">Nama Anggota</div>
                        <h3 class="h4 fw-bold mb-1">{{ $anggota->user?->nama ?? '-' }}</h3>
                        <div class="text-muted">{{ $anggota->kode_anggota }}</div>
                    </div>
                    <div>
                        @if ($anggota->status_keanggotaan === 'aktif')
                            <span class="badge bg-success fs-6">Anggota Aktif</span>
                        @else
                            <span class="badge bg-secondary fs-6">Anggota Nonaktif</span>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="small text-muted">NIK</div>
                        <div class="fw-semibold">{{ $anggota->nik ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="small text-muted">Nomor HP</div>
                        <div class="fw-semibold">{{ $anggota->no_hp ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="small text-muted">Email</div>
                        <div class="fw-semibold">{{ $anggota->user?->email ?? '-' }}</div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="small text-muted">Pekerjaan</div>
                        <div class="fw-semibold">{{ $anggota->pekerjaan ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Alamat</div>
                        <div class="fw-semibold">{{ $anggota->alamat ?: '-' }}</div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="small text-muted">Tanggal Bergabung</div>
                        <div class="fw-semibold">{{ $anggota->tanggal_daftar?->format('d-m-Y') ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted">Total Saldo Simpanan</div>
                        <div class="fs-4 fw-bold">Rp {{ number_format($ringkasan['total_simpanan'], 0, ',', '.') }}</div>
                        <div class="small text-muted">Berdasarkan saldo simpanan pada data anggota</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted">Riwayat Pinjaman</div>
                        <div class="fs-4 fw-bold">{{ $ringkasan['total_pengajuan_pinjaman'] }}</div>
                        <div class="small text-muted">{{ $ringkasan['pinjaman_aktif'] }} pinjaman berstatus disetujui/dicairkan</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted">Riwayat Angsuran</div>
                        <div class="fs-4 fw-bold">{{ $ringkasan['angsuran_lunas'] }} / {{ $ringkasan['total_jadwal_angsuran'] }}</div>
                        <div class="small text-muted">
                            Angsuran lunas dari seluruh jadwal.
                            @if ($ringkasan['angsuran_terlambat'] > 0)
                                <span class="text-danger">{{ $ringkasan['angsuran_terlambat'] }} terlambat</span>
                            @else
                                Tidak ada angsuran terlambat yang tercatat saat ini.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Saldo Simpanan</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Simpanan Pokok</span>
                            <strong>Rp {{ number_format((float) $anggota->saldo_simpanan_pokok, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Simpanan Wajib</span>
                            <strong>Rp {{ number_format((float) $anggota->saldo_simpanan_wajib, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Simpanan Sukarela</span>
                            <strong>Rp {{ number_format((float) $anggota->saldo_simpanan_sukarela, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3"><h5 class="fw-bold mb-0">Data Usaha pada Pengajuan Pinjaman</h5></div>
                    <div class="card-body">
                        @php
                            $dataUsaha = $anggota->pinjaman
                                ->filter(fn ($pinjaman) => $pinjaman->dataPendukung !== null);
                        @endphp
                        @forelse ($dataUsaha as $pinjaman)
                            <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                <div class="fw-semibold mb-2">
                                    {{ $pinjaman->no_pinjaman }}
                                    <span class="text-muted fw-normal small">— {{ $pinjaman->tanggal_pengajuan?->format('d-m-Y') ?? '-' }}</span>
                                </div>
                                <div class="row g-3 small">
                                    <div class="col-6 col-md-3">
                                        <div class="text-muted">Jumlah Sapi</div>
                                        <strong>{{ $pinjaman->dataPendukung->jumlah_sapi ?? '-' }}</strong>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-muted">Produksi Susu/Bulan</div>
                                        <strong>{{ $pinjaman->dataPendukung->produksi_susu_bulanan !== null ? number_format((float) $pinjaman->dataPendukung->produksi_susu_bulanan, 2, ',', '.') : '-' }}</strong>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-muted">Kondisi Usaha</div>
                                        <strong>{{ ucfirst($pinjaman->dataPendukung->kondisi_usaha ?? '-') }}</strong>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="text-muted">Pendapatan/Bulan</div>
                                        <strong>Rp {{ number_format((float) ($pinjaman->dataPendukung->pendapatan_bulanan ?? 0), 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-muted">Kewajiban Angsuran Lain</div>
                                        <strong>Rp {{ number_format((float) ($pinjaman->dataPendukung->kewajiban_angsuran_lain ?? 0), 0, ',', '.') }}</strong>
                                    </div>
                                    @if ($pinjaman->dataPendukung->keterangan_tambahan)
                                        <div class="col-12">
                                            <div class="text-muted">Keterangan Tambahan</div>
                                            <div>{{ $pinjaman->dataPendukung->keterangan_tambahan }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">Belum ada data usaha pendukung pinjaman yang tercatat untuk anggota ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Riwayat Simpanan</h5>
                <div class="small text-muted">Menampilkan transaksi yang tercatat, termasuk status verifikasinya.</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tanggal</th><th>No. Transaksi</th><th>Jenis</th>
                            <th class="text-end">Jumlah</th><th>Status</th><th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anggota->simpanan as $simpanan)
                            <tr>
                                <td class="ps-3 text-nowrap">{{ $simpanan->tanggal_transaksi?->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $simpanan->no_simpanan }}</td>
                                <td>{{ $simpanan->jenisSimpanan?->nama_jenis ?? '-' }}</td>
                                <td class="text-end text-nowrap">Rp {{ number_format((float) $simpanan->jumlah, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $simpananBadge = match ($simpanan->status) {
                                            'disetujui' => 'bg-success',
                                            'pending' => 'bg-warning text-dark',
                                            'ditolak' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $simpananBadge }}">{{ ucfirst($simpanan->status ?? '-') }}</span>
                                </td>
                                <td>{{ $simpanan->keterangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada transaksi simpanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Riwayat Pinjaman dan Angsuran</h5>
            </div>
            <div class="card-body">
                @forelse ($anggota->pinjaman as $pinjaman)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                            <div>
                                <div class="fw-bold">{{ $pinjaman->no_pinjaman }}</div>
                                <div class="small text-muted">
                                    {{ $pinjaman->masterPinjaman?->nama_paket ?? 'Pinjaman' }}
                                    · Pengajuan {{ $pinjaman->tanggal_pengajuan?->format('d-m-Y') ?? '-' }}
                                </div>
                            </div>
                            @php
                                $pinjamanBadge = match ($pinjaman->status) {
                                    'lunas' => 'bg-success',
                                    'ditolak' => 'bg-danger',
                                    'dicairkan' => 'bg-primary',
                                    'disetujui' => 'bg-info text-dark',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp
                            <span class="badge {{ $pinjamanBadge }} align-self-start">{{ ucwords(str_replace('_', ' ', $pinjaman->status ?? '-')) }}</span>
                        </div>

                        <div class="row g-3 mb-3 small">
                            <div class="col-6 col-md-3">
                                <div class="text-muted">Jumlah Pengajuan</div>
                                <strong>Rp {{ number_format((float) $pinjaman->jumlah_pinjaman, 0, ',', '.') }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-muted">Disetujui</div>
                                <strong>{{ $pinjaman->jumlah_disetujui !== null ? 'Rp '.number_format((float) $pinjaman->jumlah_disetujui, 0, ',', '.') : '-' }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-muted">Tenor</div>
                                <strong>{{ $pinjaman->tenor }} bulan</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-muted">Sisa Pinjaman</div>
                                <strong>Rp {{ number_format((float) $pinjaman->sisa_pinjaman, 0, ',', '.') }}</strong>
                            </div>
                            <div class="col-12">
                                <div class="text-muted">Tujuan Pinjaman</div>
                                <div>{{ $pinjaman->tujuan_pinjaman ?: '-' }}</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Angsuran</th><th>Jatuh Tempo</th><th class="text-end">Tagihan</th>
                                        <th class="text-end">Dibayar</th><th>Tanggal Bayar</th><th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pinjaman->angsuran as $angsuran)
                                        <tr>
                                            <td>Ke-{{ $angsuran->angsuran_ke }}</td>
                                            <td class="text-nowrap">{{ $angsuran->tanggal_jatuh_tempo?->format('d-m-Y') ?? '-' }}</td>
                                            <td class="text-end text-nowrap">Rp {{ number_format((float) $angsuran->total_angsuran + (float) $angsuran->denda, 0, ',', '.') }}</td>
                                            <td class="text-end text-nowrap">Rp {{ number_format((float) $angsuran->jumlah_bayar, 0, ',', '.') }}</td>
                                            <td class="text-nowrap">{{ $angsuran->tanggal_bayar?->format('d-m-Y') ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $angsuranBadge = match ($angsuran->status_pembayaran) {
                                                        'lunas' => 'bg-success',
                                                        'menunggu_verifikasi' => 'bg-warning text-dark',
                                                        'ditolak' => 'bg-danger',
                                                        default => ($angsuran->tanggal_jatuh_tempo && $angsuran->tanggal_jatuh_tempo->lt(today()) ? 'bg-danger' : 'bg-secondary'),
                                                    };
                                                    $angsuranLabel = $angsuran->status_pembayaran === 'belum_bayar'
                                                        && $angsuran->tanggal_jatuh_tempo
                                                        && $angsuran->tanggal_jatuh_tempo->lt(today())
                                                        ? 'Terlambat'
                                                        : ucwords(str_replace('_', ' ', $angsuran->status_pembayaran ?? '-'));
                                                @endphp
                                                <span class="badge {{ $angsuranBadge }}">{{ $angsuranLabel }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada jadwal angsuran.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Anggota ini belum memiliki riwayat pinjaman.</div>
                @endforelse
            </div>
        </div>

        <div class="alert alert-info small mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Informasi pada halaman ini hanya untuk pemantauan dan analisis. Perubahan data anggota maupun transaksi tetap dilakukan oleh petugas sesuai kewenangannya.
        </div>
    </div>
</x-app-layout>
