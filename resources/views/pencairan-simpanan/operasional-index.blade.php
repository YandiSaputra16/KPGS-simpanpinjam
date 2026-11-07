<x-app-layout>
    @if ($tahap === 'kabag')
        <x-slot name="header">
            <div>
                <h2 class="h4 mb-1 fw-bold">Persetujuan Pencairan Simpanan</h2>
                <p class="text-muted small mb-0">
                    Tinjau hasil pemeriksaan CS/Teller dan tentukan keputusan pencairan simpanan anggota.
                </p>
            </div>
        </x-slot>

        <div class="container-fluid py-4">
            {{-- PESAN SISTEM --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="fw-semibold mb-2">Terdapat kesalahan:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                $riwayatRows = $riwayatPencairan->getCollection();

                $jumlahDisetujuiHalaman = $riwayatRows
                    ->whereIn('status', ['menunggu_pencairan', 'selesai'])
                    ->count();

                $jumlahDitolakHalaman = $riwayatRows
                    ->where('status', 'ditolak_kabag')
                    ->count();
            @endphp

            {{-- TAB PENGAJUAN AKTIF DAN RIWAYAT --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-2">
                    <ul class="nav nav-pills nav-fill gap-2" id="tabPencairanKabag" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link active w-100"
                                id="tab-persetujuan"
                                data-bs-toggle="tab"
                                data-bs-target="#panel-persetujuan"
                                type="button"
                                role="tab"
                                aria-controls="panel-persetujuan"
                                aria-selected="true"
                            >
                                <i class="bi bi-clipboard-check me-1"></i>
                                Persetujuan
                                <span class="badge bg-light text-primary ms-1">{{ $items->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link text-body w-100"
                                id="tab-riwayat"
                                data-bs-toggle="tab"
                                data-bs-target="#panel-riwayat"
                                type="button"
                                role="tab"
                                aria-controls="panel-riwayat"
                                aria-selected="false"
                            >
                                <i class="bi bi-clock-history me-1"></i>
                                Riwayat Persetujuan
                                <span class="badge bg-secondary ms-1">{{ $riwayatPencairan->total() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted mb-1">Total Data Riwayat</div>
                            <div class="fs-3 fw-bold">{{ number_format($riwayatPencairan->total(), 0, ',', '.') }}</div>
                            <div class="small text-muted">Data pencairan yang tercakup dalam riwayat Kabag</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted mb-1">Menunggu Diproses</div>
                            <div class="fs-3 fw-bold">{{ number_format($items->total(), 0, ',', '.') }}</div>
                            <div class="small text-muted">Pengajuan yang membutuhkan keputusan Kabag</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted mb-1">Disetujui / Diteruskan</div>
                            <div class="fs-3 fw-bold">{{ number_format($jumlahDisetujuiHalaman, 0, ',', '.') }}</div>
                            <div class="small text-muted">Pada halaman riwayat yang sedang ditampilkan</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted mb-1">Ditolak</div>
                            <div class="fs-3 fw-bold">{{ number_format($jumlahDitolakHalaman, 0, ',', '.') }}</div>
                            <div class="small text-muted">Pada halaman riwayat yang sedang ditampilkan</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tabPencairanKabagContent">
                {{-- TAB PERSETUJUAN --}}
                <div
                    class="tab-pane fade show active"
                    id="panel-persetujuan"
                    role="tabpanel"
                    aria-labelledby="tab-persetujuan"
                    tabindex="0"
                >
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1">Daftar Pengajuan Pencairan</h5>
                                    <div class="small text-muted">{{ number_format($items->total(), 0, ',', '.') }} pengajuan menunggu keputusan</div>
                                </div>
                            </div>
                        </div>

                        @if ($items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-3 py-3">No. Pengajuan</th>
                                            <th class="py-3">Anggota</th>
                                            <th class="py-3">Jenis Simpanan</th>
                                            <th class="py-3 text-end">Jumlah</th>
                                            <th class="py-3">Tanggal Pengajuan</th>
                                            <th class="py-3 text-center">Status</th>
                                            <th class="py-3 text-center">Tindakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr>
                                                <td class="px-3">
                                                    <div class="fw-semibold text-nowrap">{{ $item->no_pengajuan }}</div>
                                                    <div class="small text-muted">Diajukan {{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $item->anggota?->user?->nama ?? '-' }}</div>
                                                    <div class="small text-muted">{{ $item->anggota?->kode_anggota ?? '-' }}</div>
                                                </td>
                                                <td>
                                                    <div>{{ $item->nama_jenis_simpanan }}</div>
                                                    <div class="small text-muted">
                                                        Saldo: Rp {{ number_format((float) ($item->anggota->{'saldo_simpanan_'.$item->jenis_simpanan} ?? 0), 0, ',', '.') }}
                                                    </div>
                                                </td>
                                                <td class="text-end text-nowrap fw-semibold">
                                                    Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                                                </td>
                                                <td class="text-nowrap">{{ $item->created_at?->format('d/m/Y') ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning text-dark">Menunggu Kabag</span>
                                                </td>
                                                <td class="text-center">
                                                    <button
                                                        class="btn btn-sm btn-primary"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#keputusan-{{ $item->id }}"
                                                        aria-expanded="false"
                                                        aria-controls="keputusan-{{ $item->id }}"
                                                    >
                                                        <i class="bi bi-eye me-1"></i> Tinjau
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="border-0">
                                                <td colspan="7" class="p-0 border-0">
                                                    <div class="collapse" id="keputusan-{{ $item->id }}">
                                                        <div class="bg-light border-top border-bottom p-3 p-md-4">
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="card border-0 h-100">
                                                                        <div class="card-body">
                                                                            <h6 class="fw-bold mb-3">Informasi Pengajuan</h6>
                                                                            <div class="mb-2">
                                                                                <div class="small text-muted">Alasan pencairan</div>
                                                                                <div>{{ $item->alasan ?: '-' }}</div>
                                                                            </div>
                                                                            <div class="mb-2">
                                                                                <div class="small text-muted">Saldo simpanan saat ini</div>
                                                                                <div class="fw-semibold">Rp {{ number_format((float) ($item->anggota->{'saldo_simpanan_'.$item->jenis_simpanan} ?? 0), 0, ',', '.') }}</div>
                                                                            </div>
                                                                            @if ($item->bukti)
                                                                                <a href="{{ asset('storage/'.$item->bukti) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-2">
                                                                                    <i class="bi bi-file-earmark-image me-1"></i> Lihat Bukti Pengajuan
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="card border-0 h-100">
                                                                        <div class="card-body">
                                                                            <h6 class="fw-bold mb-3">Hasil Pemeriksaan CS/Teller</h6>
                                                                            <div class="small text-muted mb-1">Catatan pemeriksaan</div>
                                                                            <div>{{ $item->catatan_cs ?: 'Tidak ada catatan dari CS/Teller.' }}</div>
                                                                            <div class="small text-muted mt-3 mb-1">Diperiksa pada</div>
                                                                            <div>{{ $item->diperiksa_cs_pada?->format('d/m/Y H:i') ?? '-' }}</div>
                                                                            <div class="small text-muted mt-3 mb-1">Nominal yang diajukan</div>
                                                                            <div class="fw-semibold">Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <form method="POST" action="{{ route('pencairan-simpanan.kabag.keputusan', $item) }}">
                                                                @csrf
                                                                <label for="catatan-{{ $item->id }}" class="form-label fw-semibold">Catatan keputusan Kabag <span class="text-muted fw-normal">(opsional)</span></label>
                                                                <textarea id="catatan-{{ $item->id }}" class="form-control mb-3" name="catatan" rows="2" maxlength="2000" placeholder="Tuliskan catatan keputusan jika diperlukan..."></textarea>
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <button class="btn btn-success" type="submit" name="keputusan" value="setujui" onclick="return confirm('Setujui pengajuan pencairan simpanan ini?')">
                                                                        <i class="bi bi-check-circle me-1"></i> Setujui Pengajuan
                                                                    </button>
                                                                    <button class="btn btn-outline-danger" type="submit" name="keputusan" value="tolak" onclick="return confirm('Tolak pengajuan pencairan simpanan ini?')">
                                                                        <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($items->hasPages())
                                <div class="card-body">{{ $items->links('pagination::bootstrap-5') }}</div>
                            @endif
                        @else
                            <div class="text-center py-5 px-3">
                                <div class="mb-3"><i class="bi bi-inbox text-secondary" style="font-size: 2.5rem;"></i></div>
                                <h5 class="fw-bold mb-2">Belum Ada Pengajuan</h5>
                                <p class="text-muted mb-0">Saat ini tidak ada pengajuan pencairan simpanan yang menunggu persetujuan Kabag.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- TAB RIWAYAT PERSETUJUAN --}}
                <div
                    class="tab-pane fade"
                    id="panel-riwayat"
                    role="tabpanel"
                    aria-labelledby="tab-riwayat"
                    tabindex="0"
                >
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold mb-1">Riwayat Persetujuan Pencairan</h5>
                            <div class="small text-muted">{{ number_format($riwayatPencairan->total(), 0, ',', '.') }} data riwayat</div>
                        </div>
                        @if ($riwayatPencairan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-3 py-3">No. Pengajuan</th>
                                            <th class="py-3">Anggota</th>
                                            <th class="py-3">Jenis Simpanan</th>
                                            <th class="py-3 text-end">Jumlah</th>
                                            <th class="py-3">Tanggal Pengajuan</th>
                                            <th class="py-3 text-center">Status</th>
                                            <th class="py-3">Catatan Kabag</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($riwayatPencairan as $riwayat)
                                            @php
                                                $statusRiwayat = match ($riwayat->status) {
                                                    'menunggu_kabag' => ['Menunggu Kabag', 'bg-warning text-dark'],
                                                    'menunggu_pencairan' => ['Disetujui - Menunggu Bendahara', 'bg-primary'],
                                                    'selesai' => ['Selesai Dicairkan', 'bg-success'],
                                                    'ditolak_kabag' => ['Ditolak Kabag', 'bg-danger'],
                                                    default => [ucfirst(str_replace('_', ' ', $riwayat->status)), 'bg-secondary'],
                                                };
                                            @endphp
                                            <tr>
                                                <td class="px-3 fw-semibold text-nowrap">{{ $riwayat->no_pengajuan }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $riwayat->anggota?->user?->nama ?? '-' }}</div>
                                                    <div class="small text-muted">{{ $riwayat->anggota?->kode_anggota ?? '-' }}</div>
                                                </td>
                                                <td>{{ $riwayat->nama_jenis_simpanan }}</td>
                                                <td class="text-end text-nowrap">Rp {{ number_format((float) $riwayat->jumlah, 0, ',', '.') }}</td>
                                                <td class="text-nowrap">{{ $riwayat->created_at?->format('d/m/Y') ?? '-' }}</td>
                                                <td><span class="badge {{ $statusRiwayat[1] }}">{{ $statusRiwayat[0] }}</span></td>
                                                <td>{{ $riwayat->catatan_kabag ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($riwayatPencairan->hasPages())
                                <div class="card-body">{{ $riwayatPencairan->links('pagination::bootstrap-5', ['pageName' => 'riwayat']) }}</div>
                            @endif
                        @else
                            <div class="text-center py-5 px-3">
                                <div class="mb-3"><i class="bi bi-clock-history text-secondary" style="font-size: 2.5rem;"></i></div>
                                <h5 class="fw-bold mb-2">Belum Ada Riwayat Persetujuan</h5>
                                <p class="text-muted mb-0">Riwayat keputusan pencairan simpanan Kabag akan ditampilkan di sini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container py-4">
            <div class="mb-4">
                <h1 class="h3 mb-1">{{ $tahap === 'cs' ? 'Pemeriksaan Pencairan Simpanan' : 'Pembayaran Pencairan Simpanan' }}</h1>
                <p class="text-muted mb-0">{{ $tahap === 'cs' ? 'Periksa data anggota, jenis simpanan, saldo, dan kelengkapan pengajuan.' : 'Bayarkan pengajuan yang telah disetujui Kabag. Saldo dikurangi setelah pembayaran dicatat.' }}</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Anggota</th>
                                <th>Jenis Simpanan</th>
                                <th>Jumlah</th>
                                <th>Alasan / Catatan</th>
                                <th>Diajukan</th>
                                <th style="min-width:280px">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->no_pengajuan }}</strong>
                                        <div class="small text-muted">{{ str_replace('_', ' ', $item->status) }}</div>
                                    </td>
                                    <td>
                                        {{ $item->anggota?->user?->nama ?? $item->anggota?->kode_anggota }}
                                        <div class="small text-muted">{{ $item->anggota?->kode_anggota }}</div>
                                    </td>
                                    <td>
                                        {{ $item->nama_jenis_simpanan }}
                                        <div class="small text-muted">Saldo anggota: Rp {{ number_format((float) ($item->anggota->{'saldo_simpanan_'.$item->jenis_simpanan} ?? 0), 0, ',', '.') }}</div>
                                    </td>
                                    <td>Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        {{ $item->alasan }}
                                        @if ($item->catatan_cs)
                                            <div class="small text-muted mt-1">Catatan CS: {{ $item->catatan_cs }}</div>
                                        @endif
                                        @if ($item->catatan_kabag)
                                            <div class="small text-muted mt-1">Catatan Kabag: {{ $item->catatan_kabag }}</div>
                                        @endif
                                        @if ($item->bukti)
                                            <div class="small mt-1"><a href="{{ asset('storage/'.$item->bukti) }}" target="_blank" rel="noopener">Lihat dokumen pengajuan</a></div>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($tahap === 'cs')
                                            <form method="POST" action="{{ route('pencairan-simpanan.cs.periksa', $item) }}">
                                                @csrf
                                                <label class="form-label small">Catatan pemeriksaan</label>
                                                <textarea class="form-control form-control-sm mb-2" name="catatan" rows="2"></textarea>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <button class="btn btn-sm btn-success" name="keputusan" value="lanjut">Teruskan ke Kabag</button>
                                                    <button class="btn btn-sm btn-outline-danger" name="keputusan" value="tolak">Tolak</button>
                                                </div>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('pencairan-simpanan.bendahara.cairkan', $item) }}" enctype="multipart/form-data">
                                                @csrf
                                                <label class="form-label small">Tanggal pembayaran</label>
                                                <input class="form-control form-control-sm mb-2" type="date" name="tanggal_pencairan" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                                                <label class="form-label small">Bukti pembayaran (opsional)</label>
                                                <input class="form-control form-control-sm mb-2" type="file" name="bukti_pencairan" accept="image/jpeg,image/png,image/webp">
                                                <button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('Pastikan uang benar-benar sudah dibayarkan kepada anggota. Lanjutkan pencatatan pencairan?')">Bayar &amp; Catat Kas Keluar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Tidak ada pengajuan pada tahap ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-body">{{ $items->links() }}</div>
            </div>

            @if (isset($riwayatPencairan) && $tahap !== 'cs')
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h2 class="h5 fw-bold mb-1">Riwayat Pencairan Simpanan</h2>
                        <p class="small text-muted mb-0">Data pengajuan yang relevan dengan tahap operasional Anda. Tabel ini hanya untuk melihat data.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3">No. Pengajuan</th>
                                    <th>Anggota</th>
                                    <th>Jenis Simpanan</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riwayatPencairan as $riwayat)
                                    @php
                                        $statusRiwayat = match ($riwayat->status) {
                                            'menunggu_cs' => ['Menunggu CS/Teller', 'bg-warning text-dark'],
                                            'menunggu_kabag' => ['Menunggu Kabag', 'bg-info text-dark'],
                                            'menunggu_pencairan' => ['Menunggu Pencairan', 'bg-primary'],
                                            'selesai' => ['Selesai', 'bg-success'],
                                            'ditolak_cs' => ['Ditolak CS/Teller', 'bg-danger'],
                                            'ditolak_kabag' => ['Ditolak Kabag', 'bg-danger'],
                                            default => [ucfirst(str_replace('_', ' ', $riwayat->status)), 'bg-secondary'],
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-3 fw-semibold text-nowrap">{{ $riwayat->no_pengajuan }}</td>
                                        <td>{{ $riwayat->anggota?->user?->nama ?? $riwayat->anggota?->user?->name ?? '-' }}<div class="small text-muted">{{ $riwayat->anggota?->kode_anggota ?? '-' }}</div></td>
                                        <td>{{ $riwayat->nama_jenis_simpanan }}</td>
                                        <td class="text-nowrap">{{ $riwayat->created_at?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="text-end text-nowrap">Rp {{ number_format((float) $riwayat->jumlah, 0, ',', '.') }}</td>
                                        <td><span class="badge {{ $statusRiwayat[1] }}">{{ $statusRiwayat[0] }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pencairan yang relevan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($riwayatPencairan->hasPages())
                        <div class="card-body">{{ $riwayatPencairan->links('pagination::bootstrap-5', ['pageName' => 'riwayat']) }}</div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</x-app-layout>
