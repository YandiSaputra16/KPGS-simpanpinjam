<x-app-layout>
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div><h1 class="h3 mb-1">Ajukan Pencairan Simpanan</h1><p class="text-muted mb-0">Pilih jenis simpanan yang ingin dicairkan. Pencairan diproses setelah pemeriksaan CS/Teller, persetujuan Kabag, dan pembayaran Bendahara.</p></div>
            <a href="{{ route('pencairan-simpanan.saya.index') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
        @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="card border-0 shadow-sm"><div class="card-body p-4">
            <form method="POST" action="{{ route('pencairan-simpanan.saya.store') }}" enctype="multipart/form-data">@csrf
                <div class="mb-3"><label class="form-label" for="jenis_simpanan">Jenis simpanan</label><select class="form-select" id="jenis_simpanan" name="jenis_simpanan" required><option value="">-- Pilih jenis simpanan --</option>@foreach($jenisSimpanan as $kode => $label)<option value="{{ $kode }}" @selected(old('jenis_simpanan')===$kode)>{{ $label }}</option>@endforeach</select></div>
                <div class="alert alert-info mb-3" id="saldoInfo">Pilih jenis simpanan untuk melihat saldo tersedia.</div>
                <div class="mb-3"><label class="form-label" for="jumlah">Jumlah pencairan (Rp)</label><input class="form-control" type="number" id="jumlah" name="jumlah" min="1" step="0.01" value="{{ old('jumlah') }}" required><div class="form-text">Nominal tidak boleh melebihi saldo tersedia setelah memperhitungkan pengajuan aktif untuk jenis simpanan yang sama.</div></div>
                <div class="mb-3"><label class="form-label" for="alasan">Alasan pencairan</label><textarea class="form-control" id="alasan" name="alasan" rows="4" maxlength="2000" required>{{ old('alasan') }}</textarea></div>
                <div class="mb-3"><label class="form-label" for="bukti">Dokumen pendukung (opsional)</label><input class="form-control" type="file" id="bukti" name="bukti" accept="image/jpeg,image/png,image/webp"><div class="form-text">JPG, PNG, atau WEBP maksimal 2 MB.</div></div>
                <button class="btn btn-primary" type="submit">Kirim Pengajuan</button>
            </form>
        </div></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jenis = document.getElementById('jenis_simpanan');
            const info = document.getElementById('saldoInfo');
            const jumlah = document.getElementById('jumlah');
            const saldo = { pokok: @json((float) $anggota->saldo_simpanan_pokok), wajib: @json((float) $anggota->saldo_simpanan_wajib), sukarela: @json((float) $anggota->saldo_simpanan_sukarela) };
            const label = { pokok: 'Simpanan Pokok', wajib: 'Simpanan Wajib', sukarela: 'Simpanan Sukarela' };
            const rupiah = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
            function updateSaldo() { const key = jenis.value; if (!key) { info.textContent = 'Pilih jenis simpanan untuk melihat saldo tersedia.'; jumlah.removeAttribute('max'); return; } info.innerHTML = 'Saldo tercatat ' + label[key] + ': <strong>' + rupiah(saldo[key]) + '</strong>. Saldo yang benar-benar tersedia akan diperiksa kembali saat pengajuan disimpan.'; jumlah.max = saldo[key]; }
            jenis.addEventListener('change', updateSaldo); updateSaldo();
        });
    </script>
</x-app-layout>
