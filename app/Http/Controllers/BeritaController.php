<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BeritaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Daftar Berita
    |--------------------------------------------------------------------------
    */

    public function index(): View
    {
        $berita = Berita::query()
            ->with('pembuat')
            ->latest('created_at')
            ->paginate(10);

        return view('admin.berita.index', compact('berita'));
    }

    /*
    |--------------------------------------------------------------------------
    | Form Tambah Berita
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        return view('admin.berita.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan Berita
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:1000'],
            'isi' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['slug'] = Berita::generateSlug($validated['judul']);
        $validated['status'] = 'draft';
        $validated['published_at'] = null;
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request
                ->file('gambar')
                ->store('berita', 'public');
        }

        Berita::create($validated);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil dibuat sebagai draft.');
    }

    /*
    |--------------------------------------------------------------------------
    | Form Edit Berita
    |--------------------------------------------------------------------------
    */

    public function edit(Berita $berita): View
    {
        return view('admin.berita.edit', compact('berita'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update Berita
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Berita $berita
    ): RedirectResponse {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:1000'],
            'isi' => ['required', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['slug'] = Berita::generateSlug(
            $validated['judul'],
            $berita->id
        );

        if ($request->hasFile('gambar')) {
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }

            $validated['gambar'] = $request
                ->file('gambar')
                ->store('berita', 'public');
        }

        $berita->update($validated);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | Publikasikan Berita
    |--------------------------------------------------------------------------
    */

    public function publish(Berita $berita): RedirectResponse
    {
        $berita->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil dipublikasikan.');
    }

    /*
    |--------------------------------------------------------------------------
    | Kembalikan ke Draft
    |--------------------------------------------------------------------------
    */

    public function unpublish(Berita $berita): RedirectResponse
    {
        $berita->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita dikembalikan menjadi draft.');
    }

    /*
    |--------------------------------------------------------------------------
    | Hapus Berita
    |--------------------------------------------------------------------------
    */

    public function destroy(Berita $berita): RedirectResponse
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}