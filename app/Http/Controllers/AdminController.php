<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Kas;
use App\Models\User;

class AdminController extends Controller
{
    public function companyProfile(): View
    {
        $pengaturan = Pengaturan::aktif();

        return view('admin.company-profile', compact('pengaturan'));
    }

    public function updateCompanyProfile(Request $request): RedirectResponse
    {
        $pengaturan = Pengaturan::aktif();

        $validated = $request->validate([
            'nama_koperasi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],

            'tentang' => ['nullable', 'string'],
            'sejarah' => ['nullable', 'string'],
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'layanan' => ['nullable', 'string'],
            'syarat_anggota' => ['nullable', 'string'],
            'prosedur_pinjaman' => ['nullable', 'string'],

            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'maps_embed' => ['nullable', 'string'],
        ]);

        $pengaturan->update($validated);

        ActivityLog::catat(
            action: 'update_company_profile',
            deskripsi: 'Memperbarui informasi company profile KPGS.',
            model: 'Pengaturan',
            modelId: $pengaturan->id,
        );

        return redirect()
            ->route('admin.company-profile')
            ->with('success', 'Company Profile berhasil diperbarui.');
    }

    public function settings(): View
    {
        $pengaturan = Pengaturan::aktif();

        return view('admin.settings', compact('pengaturan'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $pengaturan = Pengaturan::aktif();

        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:ico,png,jpg,jpeg,webp', 'max:1024'],
            'google_maps_api_key' => ['nullable', 'string', 'max:255'],
            'fonnte_token' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('logo')) {
            if ($pengaturan->logo) {
                Storage::disk('public')->delete($pengaturan->logo);
            }

            $validated['logo'] = $request
                ->file('logo')
                ->store('pengaturan', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($pengaturan->favicon) {
                Storage::disk('public')->delete($pengaturan->favicon);
            }

            $validated['favicon'] = $request
                ->file('favicon')
                ->store('pengaturan', 'public');
        }

        $pengaturan->update($validated);

        ActivityLog::catat(
            action: 'update_application_settings',
            deskripsi: 'Memperbarui pengaturan aplikasi KPGS.',
            model: 'Pengaturan',
            modelId: $pengaturan->id,
        );

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }

    
public function activities(Request $request): View
{
    $validated = $request->validate([
        'dari' => ['nullable', 'date'],
        'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        'action' => ['nullable', 'string', 'max:100'],
        'model' => ['nullable', 'string', 'max:100'],
        'user_id' => ['nullable', 'integer', 'exists:users,id'],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Filter Riwayat Aktivitas
    |--------------------------------------------------------------------------
    */

    $activityQuery = ActivityLog::query()
        ->with('user');

    if (!empty($validated['dari'])) {
        $activityQuery->whereDate(
            'created_at',
            '>=',
            $validated['dari']
        );
    }

    if (!empty($validated['sampai'])) {
        $activityQuery->whereDate(
            'created_at',
            '<=',
            $validated['sampai']
        );
    }

    if (!empty($validated['action'])) {
        $activityQuery->where(
            'action',
            $validated['action']
        );
    }

    if (!empty($validated['model'])) {
        $activityQuery->where(
            'model',
            $validated['model']
        );
    }

    if (!empty($validated['user_id'])) {
        $activityQuery->where(
            'user_id',
            $validated['user_id']
        );
    }

    $activities = $activityQuery
        ->latest('created_at')
        ->paginate(20)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Filter Periode Keuangan
    |--------------------------------------------------------------------------
    | Menggunakan tanggal transaksi pada tabel kas.
    | Jika tanggal tidak dipilih, ringkasan menghitung seluruh periode.
    */

    $kasQuery = Kas::query();

    if (!empty($validated['dari'])) {
        $kasQuery->whereDate(
            'tanggal',
            '>=',
            $validated['dari']
        );
    }

    if (!empty($validated['sampai'])) {
        $kasQuery->whereDate(
            'tanggal',
            '<=',
            $validated['sampai']
        );
    }

    $totalKasMasuk = (clone $kasQuery)
        ->where('jenis', 'masuk')
        ->sum('jumlah');

    $totalKasKeluar = (clone $kasQuery)
        ->where('jenis', 'keluar')
        ->sum('jumlah');

    $selisihArusKas = $totalKasMasuk - $totalKasKeluar;

    /*
    |--------------------------------------------------------------------------
    | Pilihan Filter
    |--------------------------------------------------------------------------
    */

    $daftarAksi = ActivityLog::query()
        ->whereNotNull('action')
        ->where('action', '!=', '')
        ->distinct()
        ->orderBy('action')
        ->pluck('action');

    $daftarModel = ActivityLog::query()
        ->whereNotNull('model')
        ->where('model', '!=', '')
        ->distinct()
        ->orderBy('model')
        ->pluck('model');

    $daftarPengguna = User::query()
        ->whereIn(
            'id',
            ActivityLog::query()
                ->whereNotNull('user_id')
                ->select('user_id')
        )
        ->orderBy('nama')
        ->get(['id', 'nama']);

    return view('admin.activities', compact(
        'activities',
        'totalKasMasuk',
        'totalKasKeluar',
        'selisihArusKas',
        'daftarAksi',
        'daftarModel',
        'daftarPengguna',
        'validated'
    ));
}

}