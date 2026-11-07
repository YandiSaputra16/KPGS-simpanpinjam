<x-app-layout>
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="h3 fw-bold mb-1">Pengajuan Pencairan Simpanan</h1><p class="text-muted mb-0">Pantau proses pengajuan dan riwayat pencairan simpanan Anda.</p></div>
        <a class="btn btn-primary" href="{{ route('pencairan-simpanan.saya.create') }}"><i class="bi bi-plus-lg me-1"></i> Ajukan Pencairan</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="row g-3 mb-4">
        @foreach(['pokok'=>'Simpanan Pokok','wajib'=>'Simpanan Wajib','sukarela'=>'Simpanan Sukarela'] as $kode=>$label)
            <div class="col-12 col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body">
                <div class="small text-muted mb-1">{{ $label }}</div>
                <div class="fs-5 fw-bold">Rp {{ number_format((float)$anggota->{'saldo_simpanan_'.$kode},0,',','.') }}</div>
            </div></div></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Riwayat Pengajuan</h2></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Nomor Pengajuan</th><th>Tanggal</th><th>Jenis</th><th class="text-end">Nominal</th><th>Status</th><th class="text-center">Detail</th></tr></thead>
            <tbody>
            @forelse($items as $item)
                @php
                    [$label,$warna] = match($item->status) {
                        'menunggu_cs'=>['Menunggu CS/Teller','text-bg-warning'],
                        'menunggu_kabag'=>['Menunggu Kabag','text-bg-info'],
                        'menunggu_pencairan'=>['Menunggu Pencairan','text-bg-primary'],
                        'selesai'=>['Selesai','text-bg-success'],
                        'ditolak_cs'=>['Ditolak CS/Teller','text-bg-danger'],
                        'ditolak_kabag'=>['Ditolak Kabag','text-bg-danger'],
                        default=>[ucfirst(str_replace('_',' ',$item->status)),'text-bg-secondary'],
                    };
                @endphp
                <tr><td class="ps-3 fw-semibold">{{ $item->no_pengajuan }}</td><td class="text-nowrap">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</td><td>{{ $item->nama_jenis_simpanan }}</td><td class="text-end text-nowrap">Rp {{ number_format((float)$item->jumlah,0,',','.') }}</td><td><span class="badge {{ $warna }}">{{ $label }}</span></td><td class="text-center"><a class="btn btn-sm btn-outline-primary" href="{{ route('pencairan-simpanan.saya.show',$item) }}">Lihat detail</a></td></tr>
            @empty <tr><td colspan="6" class="text-center text-muted py-5">Belum ada pengajuan pencairan.</td></tr> @endforelse
            </tbody>
        </table></div>
        @if($items->hasPages())<div class="card-footer bg-white">{{ $items->links('pagination::bootstrap-5') }}</div>@endif
    </div>
</div>
</x-app-layout>
