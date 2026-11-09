
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Data Angsuran</h2>
            <p class="text-muted mb-0">
                Kelola dan periksa pembayaran angsuran anggota.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="container-xxl">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="fw-semibold mb-2">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Proses belum berhasil.
                    </div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Statistik --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info"
                                     style="width:48px;height:48px;flex-shrink:0;">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total Transaksi</div>
                                    <div class="fs-3 fw-bold">{{ $jumlahTransaksi }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-25 text-warning"
                                     style="width:48px;height:48px;flex-shrink:0;">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Menunggu Verifikasi</div>
                                    <div class="fs-3 fw-bold">{{ $jumlahMenunggu }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success"
                                     style="width:48px;height:48px;flex-shrink:0;">
                                    <i class="bi bi-check-circle fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Disetujui</div>
                                    <div class="fs-3 fw-bold">{{ $jumlahDisetujui }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Data --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">Filter Data</h5>
                            <p class="text-muted small mb-0">
                                Gunakan filter untuk menemukan pembayaran tertentu.
                            </p>
                        </div>
                        <i class="bi bi-funnel text-secondary fs-5"></i>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="filterPencarian" class="form-label fw-semibold">Pencarian</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    type="search"
                                    id="filterPencarian"
                                    class="form-control"
                                    placeholder="Nomor pinjaman atau nama anggota..."
                                >
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label for="filterStatus" class="form-label fw-semibold">Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="menunggu">Menunggu Verifikasi</option>
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="filterTanggalMulai" class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" id="filterTanggalMulai" class="form-control">
                        </div>

                        <div class="col-6 col-lg-2">
                            <label for="filterTanggalAkhir" class="form-label fw-semibold">Tanggal Akhir</label>
                            <input type="date" id="filterTanggalAkhir" class="form-control">
                        </div>

                        <div class="col-12 col-lg-2 d-flex gap-2">
                            <button type="button" id="resetFilter" class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset
                            </button>
                            <button type="button" id="terapkanFilter" class="btn btn-primary flex-fill">
                                <i class="bi bi-search me-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Angsuran Menunggu Verifikasi --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">Daftar Angsuran</h5>
                            <p class="text-muted small mb-0">
                                Menampilkan pembayaran angsuran yang menunggu pemeriksaan.
                            </p>
                        </div>
                        <span class="badge bg-info bg-opacity-10 text-info">
                            <span id="jumlahData">{{ $jumlahMenunggu }}</span> Data
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelAngsuran">
                        <thead style="background-color:#edf8fb;">
                            <tr>
                                <th class="px-3 py-3 text-nowrap">No. Pinjaman</th>
                                <th class="px-3 py-3">Anggota</th>
                                <th class="px-3 py-3 text-nowrap">Angsuran</th>
                                <th class="px-3 py-3 text-nowrap">Tanggal Bayar</th>
                                <th class="px-3 py-3 text-end text-nowrap">Jumlah</th>
                                <th class="px-3 py-3 text-nowrap">Jatuh Tempo</th>
                                <th class="px-3 py-3 text-nowrap">Status</th>
                                <th class="px-3 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($angsuranMenunggu as $item)
                                @php
                                    $pinjamanItem = $item->pinjaman;
                                    $anggotaItem = $pinjamanItem?->anggota;
                                    $userAnggota = $anggotaItem?->user;

                                    $namaAnggota = $userAnggota?->nama ?? '-';
                                    $kodeAnggota = $anggotaItem?->kode_anggota ?? '-';
                                    $nomorPinjaman = $pinjamanItem?->no_pinjaman ?? '-';

                                    $nominalPembayaran = (float) (
                                        ($item->jumlah_bayar ?? 0) > 0
                                            ? $item->jumlah_bayar
                                            : ($item->total_tagihan ?? $item->total_angsuran ?? 0)
                                    );

                                    $tanggalPembayaran = $item->tanggal_bayar;
                                    $tanggalJatuhTempo = $item->tanggal_jatuh_tempo;

                                    $tanggalFilter = $tanggalPembayaran
                                        ? \Illuminate\Support\Carbon::parse($tanggalPembayaran)->format('Y-m-d')
                                        : '';

                                    $teksPencarian = strtolower(
                                        $nomorPinjaman . ' ' .
                                        $namaAnggota . ' ' .
                                        $kodeAnggota . ' ' .
                                        ($item->angsuran_ke ?? '')
                                    );
                                @endphp

                                <tr
                                    class="baris-angsuran"
                                    data-search="{{ $teksPencarian }}"
                                    data-status="menunggu"
                                    data-tanggal="{{ $tanggalFilter }}"
                                >
                                    <td class="px-3 py-3 fw-semibold text-nowrap">
                                        {{ $nomorPinjaman }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="fw-semibold">{{ $namaAnggota }}</div>
                                        <div class="small text-muted">{{ $kodeAnggota }}</div>
                                    </td>

                                    <td class="px-3 py-3 text-nowrap">
                                        Ke-{{ $item->angsuran_ke ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-nowrap">
                                        @if ($tanggalPembayaran)
                                            {{ \Illuminate\Support\Carbon::parse($tanggalPembayaran)->translatedFormat('d F Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-end fw-semibold text-nowrap">
                                        Rp {{ number_format($nominalPembayaran, 0, ',', '.') }}
                                    </td>

                                    <td class="px-3 py-3 text-nowrap">
                                        @if ($tanggalJatuhTempo)
                                            {{ \Illuminate\Support\Carbon::parse($tanggalJatuhTempo)->translatedFormat('d F Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Menunggu Verifikasi
                                        </span>
                                    </td>

                                    <td class="px-3 py-3 text-center">
                                        <a
                                            href="{{ route('angsuran.show', $item) }}"
                                            class="btn btn-sm btn-outline-primary text-nowrap"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr id="barisKosong">
                                    <td colspan="8" class="text-center py-5 px-3">
                                        <i class="bi bi-clipboard-check text-muted d-block mb-3" style="font-size:3rem;"></i>
                                        <h5 class="fw-bold mb-2">Tidak Ada Antrean Verifikasi</h5>
                                        <p class="text-muted mb-0">
                                            Belum ada pembayaran angsuran yang menunggu verifikasi.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse

                            <tr id="hasilFilterKosong" class="d-none">
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-search text-muted d-block mb-3" style="font-size:2rem;"></i>
                                    <div class="fw-semibold">Data tidak ditemukan</div>
                                    <div class="text-muted small">Ubah kata pencarian atau rentang tanggal.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Riwayat Persetujuan Angsuran --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">Riwayat Persetujuan Angsuran</h5>
                            <p class="text-muted small mb-0">
                                Menampilkan pembayaran angsuran yang telah disetujui oleh CS/Teller.
                            </p>
                        </div>

                        <span class="badge bg-success bg-opacity-10 text-success">
                            {{ $jumlahDisetujui }} Transaksi Disetujui
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color:#edf8fb;">
                            <tr>
                                <th class="px-3 py-3 text-nowrap">No. Pinjaman</th>
                                <th class="px-3 py-3">Anggota</th>
                                <th class="px-3 py-3 text-nowrap">Angsuran</th>
                                <th class="px-3 py-3 text-nowrap">Tanggal Bayar</th>
                                <th class="px-3 py-3 text-end text-nowrap">Jumlah</th>
                                <th class="px-3 py-3 text-nowrap">Tanggal Verifikasi</th>
                                <th class="px-3 py-3">Verifikator</th>
                                <th class="px-3 py-3 text-nowrap">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($riwayatPersetujuan as $item)
                                @php
                                    $pinjamanItem = $item->pinjaman;
                                    $anggotaItem = $pinjamanItem?->anggota;

                                    $nominalPembayaran = (float) (
                                        ($item->jumlah_bayar ?? 0) > 0
                                            ? $item->jumlah_bayar
                                            : ($item->total_tagihan ?? $item->total_angsuran ?? 0)
                                    );
                                @endphp

                                <tr>
                                    <td class="px-3 py-3 fw-semibold text-nowrap">
                                        {{ $pinjamanItem?->no_pinjaman ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <div class="fw-semibold">
                                            {{ $anggotaItem?->user?->nama ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $anggotaItem?->kode_anggota ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 text-nowrap">
                                        Ke-{{ $item->angsuran_ke }}
                                    </td>

                                    <td class="px-3 py-3 text-nowrap">
                                        {{ $item->tanggal_bayar
                                            ? \Illuminate\Support\Carbon::parse($item->tanggal_bayar)->translatedFormat('d F Y')
                                            : '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-end fw-semibold text-nowrap">
                                        Rp {{ number_format($nominalPembayaran, 0, ',', '.') }}
                                    </td>

                                    <td class="px-3 py-3 text-nowrap">
                                        {{ $item->tanggal_verifikasi
                                            ? $item->tanggal_verifikasi->translatedFormat('d F Y H:i')
                                            : '-' }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $item->verifikator?->nama ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Disetujui
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-clock-history text-muted d-block mb-3"
                                           style="font-size:2.5rem;"></i>
                                        <div class="fw-semibold">
                                            Belum Ada Riwayat Persetujuan
                                        </div>
                                        <div class="text-muted small">
                                            Pembayaran angsuran yang telah disetujui akan ditampilkan di sini.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Filter sisi tampilan untuk antrean verifikasi --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pencarian = document.getElementById('filterPencarian');
            const status = document.getElementById('filterStatus');
            const tanggalMulai = document.getElementById('filterTanggalMulai');
            const tanggalAkhir = document.getElementById('filterTanggalAkhir');
            const tombolFilter = document.getElementById('terapkanFilter');
            const tombolReset = document.getElementById('resetFilter');
            const jumlahData = document.getElementById('jumlahData');
            const hasilKosong = document.getElementById('hasilFilterKosong');

            const baris = Array.from(document.querySelectorAll('.baris-angsuran'));

            function terapkanFilter() {
                const kata = pencarian.value.trim().toLowerCase();
                const pilihanStatus = status.value;
                const mulai = tanggalMulai.value;
                const akhir = tanggalAkhir.value;

                let terlihat = 0;

                baris.forEach(function (row) {
                    const cocokPencarian = (row.dataset.search || '').includes(kata);
                    const cocokStatus = !pilihanStatus || row.dataset.status === pilihanStatus;
                    const tanggal = row.dataset.tanggal || '';

                    const cocokMulai = !mulai || (tanggal && tanggal >= mulai);
                    const cocokAkhir = !akhir || (tanggal && tanggal <= akhir);

                    const tampil = cocokPencarian && cocokStatus && cocokMulai && cocokAkhir;

                    row.classList.toggle('d-none', !tampil);

                    if (tampil) {
                        terlihat++;
                    }
                });

                jumlahData.textContent = terlihat;

                if (hasilKosong) {
                    hasilKosong.classList.toggle(
                        'd-none',
                        terlihat !== 0 || baris.length === 0
                    );
                }
            }

            tombolFilter.addEventListener('click', terapkanFilter);

            pencarian.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    terapkanFilter();
                }
            });

            tombolReset.addEventListener('click', function () {
                pencarian.value = '';
                status.value = '';
                tanggalMulai.value = '';
                tanggalAkhir.value = '';
                terapkanFilter();
            });
        });
    </script>
</x-app-layout>
