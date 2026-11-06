<x-app-layout>
<div class="container py-4">
    <div class="mb-4"><h1 class="h3 mb-1">{{ $tahap==='cs'?'Pemeriksaan Pencairan Simpanan':($tahap==='kabag'?'Persetujuan Pencairan Simpanan':'Pembayaran Pencairan Simpanan') }}</h1><p class="text-muted mb-0">{{ $tahap==='cs'?'Periksa data anggota, jenis simpanan, saldo, dan kelengkapan pengajuan.':($tahap==='kabag'?'Tinjau pengajuan yang telah diperiksa CS/Teller.':'Bayarkan pengajuan yang telah disetujui Kabag. Saldo dikurangi setelah pembayaran dicatat.') }}</p></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>No. Pengajuan</th><th>Anggota</th><th>Jenis Simpanan</th><th>Jumlah</th><th>Alasan / Catatan</th><th>Diajukan</th><th style="min-width:280px">Tindakan</th></tr></thead><tbody>
    @forelse($items as $item)<tr><td><strong>{{ $item->no_pengajuan }}</strong><div class="small text-muted">{{ str_replace('_',' ',$item->status) }}</div></td><td>{{ $item->anggota?->user?->nama ?? $item->anggota?->kode_anggota }}<div class="small text-muted">{{ $item->anggota?->kode_anggota }}</div></td><td>{{ $item->nama_jenis_simpanan }}<div class="small text-muted">Saldo anggota: Rp {{ number_format((float)($item->anggota->{'saldo_simpanan_'.$item->jenis_simpanan} ?? 0),0,',','.') }}</div></td><td>Rp {{ number_format((float)$item->jumlah,0,',','.') }}</td><td>{{ $item->alasan }}@if($item->catatan_cs)<div class="small text-muted mt-1">Catatan CS: {{ $item->catatan_cs }}</div>@endif @if($item->catatan_kabag)<div class="small text-muted mt-1">Catatan Kabag: {{ $item->catatan_kabag }}</div>@endif @if($item->bukti)<div class="small mt-1"><a href="{{ asset('storage/'.$item->bukti) }}" target="_blank" rel="noopener">Lihat dokumen pengajuan</a></div>@endif</td><td>{{ $item->created_at?->format('d/m/Y H:i') }}</td><td>
    @if($tahap==='cs')<form method="POST" action="{{ route('pencairan-simpanan.cs.periksa',$item) }}">@csrf<label class="form-label small">Catatan pemeriksaan</label><textarea class="form-control form-control-sm mb-2" name="catatan" rows="2"></textarea><div class="d-flex flex-wrap gap-2"><button class="btn btn-sm btn-success" name="keputusan" value="lanjut">Teruskan ke Kabag</button><button class="btn btn-sm btn-outline-danger" name="keputusan" value="tolak">Tolak</button></div></form>
    @elseif($tahap==='kabag')<form method="POST" action="{{ route('pencairan-simpanan.kabag.keputusan',$item) }}">@csrf<label class="form-label small">Catatan keputusan</label><textarea class="form-control form-control-sm mb-2" name="catatan" rows="2"></textarea><div class="d-flex flex-wrap gap-2"><button class="btn btn-sm btn-success" name="keputusan" value="setujui">Setujui</button><button class="btn btn-sm btn-outline-danger" name="keputusan" value="tolak">Tolak</button></div></form>
    @else<form method="POST" action="{{ route('pencairan-simpanan.bendahara.cairkan',$item) }}" enctype="multipart/form-data">@csrf<label class="form-label small">Tanggal pembayaran</label><input class="form-control form-control-sm mb-2" type="date" name="tanggal_pencairan" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required><label class="form-label small">Bukti pembayaran (opsional)</label><input class="form-control form-control-sm mb-2" type="file" name="bukti_pencairan" accept="image/jpeg,image/png,image/webp"><button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('Pastikan uang benar-benar sudah dibayarkan kepada anggota. Lanjutkan pencatatan pencairan?')">Bayar & Catat Kas Keluar</button></form>@endif
    </td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pengajuan pada tahap ini.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $items->links() }}</div></div>
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
                        <tr><th class="px-3">No. Pengajuan</th><th>Anggota</th><th>Jenis Simpanan</th><th>Tanggal</th><th class="text-end">Jumlah</th><th>Status</th></tr>
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

</x-app-layout>
