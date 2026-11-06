<x-app-layout>
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h3 mb-1">Pengajuan Pencairan Simpanan</h1><p class="text-muted mb-0">Saldo saat ini: Pokok Rp {{ number_format((float)$anggota->saldo_simpanan_pokok,0,',','.') }} · Wajib Rp {{ number_format((float)$anggota->saldo_simpanan_wajib,0,',','.') }} · Sukarela Rp {{ number_format((float)$anggota->saldo_simpanan_sukarela,0,',','.') }}</p></div><a class="btn btn-primary" href="{{ route('pencairan-simpanan.saya.create') }}">Ajukan Pencairan</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>No. Pengajuan</th><th>Tanggal</th><th>Jenis Simpanan</th><th>Jumlah</th><th>Alasan</th><th>Status</th><th>Catatan</th></tr></thead><tbody>
    @forelse($items as $item)<tr><td class="fw-semibold">{{ $item->no_pengajuan }}</td><td>{{ $item->created_at?->format('d/m/Y H:i') }}</td><td>{{ $item->nama_jenis_simpanan }}</td><td>Rp {{ number_format((float)$item->jumlah,0,',','.') }}</td><td>{{ $item->alasan }}</td><td><span class="badge text-bg-{{ $item->status==='selesai'?'success':(str_starts_with($item->status,'ditolak')?'danger':'warning') }}">{{ str_replace('_',' ',ucfirst($item->status)) }}</span></td><td>{{ $item->catatan_cs ?: $item->catatan_kabag ?: '—' }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan pencairan.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $items->links() }}</div></div>
</div>
</x-app-layout>
