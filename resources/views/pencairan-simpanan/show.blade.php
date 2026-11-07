<x-app-layout>
@php
    $role = auth()->user()->role;
    $isCs = $role === 'cs' && $item->status === 'menunggu_cs';
    $isKabag = $role === 'kabag' && $item->status === 'menunggu_kabag';
    $isBendahara = $role === 'bendahara' && $item->status === 'menunggu_pencairan';
    [$statusClass] = match($item->status) {
        'menunggu_cs' => ['text-bg-warning'],
        'menunggu_kabag' => ['text-bg-info'],
        'menunggu_pencairan' => ['text-bg-primary'],
        'selesai' => ['text-bg-success'],
        'ditolak_cs','ditolak_kabag' => ['text-bg-danger'],
        default => ['text-bg-secondary'],
    };
    $backRoute = match($role) {
        'anggota' => 'pencairan-simpanan.saya.index',
        'cs' => 'pencairan-simpanan.cs.index',
        'kabag' => 'pencairan-simpanan.kabag.index',
        'bendahara' => 'pencairan-simpanan.bendahara.index',
        default => 'dashboard',
    };
@endphp
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><a href="{{ route($backRoute) }}" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke daftar</a><h1 class="h3 fw-bold mt-2 mb-1">Detail Pencairan Simpanan</h1><div class="text-muted">{{ $item->no_pengajuan }}</div></div>
        <span class="badge {{ $statusClass }} fs-6 px-3 py-2">{{ $statusLabel }}</span>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><div class="fw-semibold">Proses belum dapat disimpan:</div><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Informasi Pengajuan</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6"><div class="small text-muted">Nama Anggota</div><div class="fw-semibold">{{ $anggota->user?->nama ?? '-' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted">Kode Anggota</div><div class="fw-semibold">{{ $anggota->kode_anggota ?? '-' }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted">Jenis Simpanan</div><div class="fw-semibold">{{ $item->nama_jenis_simpanan }}</div></div>
                        <div class="col-12 col-md-6"><div class="small text-muted">Tanggal Pengajuan</div><div class="fw-semibold">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</div></div>
                        <div class="col-12"><div class="small text-muted">Nominal Pengajuan</div><div class="fs-4 fw-bold text-primary">Rp {{ number_format((float)$item->jumlah,0,',','.') }}</div></div>
                        <div class="col-12"><div class="small text-muted">Alasan Pencairan</div><div style="white-space:pre-line">{{ $item->alasan }}</div></div>
                    </div>
                    <hr>
                    <div class="d-flex flex-wrap justify-content-between gap-2"><span class="text-muted">Saldo jenis simpanan saat ini</span><strong>Rp {{ number_format($saldoSimpanan,0,',','.') }}</strong></div>
                    @if($item->bukti)<div class="mt-3"><a href="{{ asset('storage/'.$item->bukti) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary"><i class="bi bi-paperclip me-1"></i>Lihat bukti pengajuan</a></div>@endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Riwayat Pemeriksaan dan Keputusan</h2></div>
                <div class="card-body">
                    <div class="border-start border-3 border-primary ps-3 pb-3 mb-3">
                        <div class="fw-semibold">Pengajuan dibuat anggota</div><div class="small text-muted">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>
                    <div class="border-start border-3 border-info ps-3 pb-3 mb-3">
                        <div class="fw-semibold">Pemeriksaan CS/Teller</div>
                        <div class="small text-muted">{{ $item->pemeriksaCs?->nama ?? 'Belum diperiksa' }} · {{ $item->diperiksa_cs_pada?->format('d/m/Y H:i') ?? '-' }}</div>
                        @if($item->catatan_cs)<div class="mt-1" style="white-space:pre-line">{{ $item->catatan_cs }}</div>@endif
                    </div>
                    <div class="border-start border-3 border-warning ps-3 pb-3 mb-3">
                        <div class="fw-semibold">Keputusan Kabag</div>
                        <div class="small text-muted">{{ $item->penyetujuKabag?->nama ?? 'Belum diputuskan' }} · {{ $item->disetujui_kabag_pada?->format('d/m/Y H:i') ?? '-' }}</div>
                        @if($item->catatan_kabag)<div class="mt-1" style="white-space:pre-line">{{ $item->catatan_kabag }}</div>@endif
                    </div>
                    <div class="border-start border-3 border-success ps-3">
                        <div class="fw-semibold">Pembayaran Bendahara</div>
                        <div class="small text-muted">{{ $item->bendahara?->nama ?? 'Belum dicairkan' }} · {{ $item->tanggal_pencairan?->format('d/m/Y') ?? '-' }}</div>
                        @if($item->bukti_pencairan)<div class="mt-2"><a href="{{ asset('storage/'.$item->bukti_pencairan) }}" target="_blank" rel="noopener">Lihat bukti pembayaran</a></div>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            @if($isCs)
                <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Tindakan CS/Teller</h2></div><div class="card-body">
                    <p class="small text-muted">Pastikan data anggota, saldo, alasan, dan bukti telah diperiksa.</p>
                    <form method="POST" action="{{ route('pencairan-simpanan.cs.periksa',$item) }}">@csrf
                        <label class="form-label">Catatan pemeriksaan</label><textarea class="form-control mb-3" name="catatan" rows="4" maxlength="2000" placeholder="Catatan untuk anggota atau Kabag...">{{ old('catatan') }}</textarea>
                        <button class="btn btn-success w-100 mb-2" name="keputusan" value="lanjut">Teruskan ke Kabag</button>
                        <button class="btn btn-outline-danger w-100" name="keputusan" value="tolak" onclick="return confirm('Tolak pengajuan pencairan ini?')">Tolak Pengajuan</button>
                    </form>
                </div></div>
            @elseif($isKabag)
                <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Keputusan Kabag</h2></div><div class="card-body">
                    <p class="small text-muted">Persetujuan akan meneruskan pengajuan kepada Bendahara dan mengirim notifikasi.</p>
                    <form method="POST" action="{{ route('pencairan-simpanan.kabag.keputusan',$item) }}">@csrf
                        <label class="form-label">Catatan keputusan</label><textarea class="form-control mb-3" name="catatan" rows="4" maxlength="2000" placeholder="Catatan keputusan...">{{ old('catatan') }}</textarea>
                        <button class="btn btn-success w-100 mb-2" name="keputusan" value="setujui">Setujui Pengajuan</button>
                        <button class="btn btn-outline-danger w-100" name="keputusan" value="tolak" onclick="return confirm('Tolak pengajuan pencairan ini?')">Tolak Pengajuan</button>
                    </form>
                </div></div>
            @elseif($isBendahara)
                <div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Pembayaran Bendahara</h2></div><div class="card-body">
                    <div class="alert alert-info small">Pastikan dana benar-benar telah diserahkan kepada anggota sebelum mencatat pembayaran.</div>
                    <div class="d-flex justify-content-between mb-3"><span class="text-muted">Nominal dibayarkan</span><strong>Rp {{ number_format((float)$item->jumlah,0,',','.') }}</strong></div>
                    <form method="POST" action="{{ route('pencairan-simpanan.bendahara.cairkan',$item) }}" enctype="multipart/form-data">@csrf
                        <label class="form-label">Tanggal pembayaran</label><input class="form-control mb-3" type="date" name="tanggal_pencairan" value="{{ old('tanggal_pencairan',now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                        <label class="form-label">Bukti pembayaran (opsional)</label><input class="form-control mb-3" type="file" name="bukti_pencairan" accept="image/jpeg,image/png,image/webp">
                        <button class="btn btn-primary w-100" type="submit" onclick="return confirm('Pastikan dana telah dibayarkan. Lanjutkan pencatatan kas keluar dan pengurangan saldo?')">Bayar & Catat Kas Keluar</button>
                    </form>
                </div></div>
            @else
                <div class="card border-0 shadow-sm"><div class="card-body">
                    <h2 class="h6 fw-bold">Status Pengajuan</h2><p class="mb-0">{{ $statusLabel }}.</p>
                    @if(str_starts_with($item->status,'ditolak'))<div class="small text-muted mt-2">Silakan baca catatan pemeriksaan/keputusan pada riwayat di sebelah kiri.</div>@endif
                </div></div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
