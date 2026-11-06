<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 leading-tight">
                    Tambah Anggota
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Membuat akun dan data anggota baru.
                </p>
            </div>

            <a
                href="{{ route('anggota.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200"
            >
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Pesan validasi --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm font-semibold text-red-700">
                        Data belum dapat disimpan.
                    </div>

                    <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">

                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Data Akun & Anggota
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Lengkapi data calon anggota untuk membuat akun sistem.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('anggota.store') }}"
                >
                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Data Akun --}}
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">
                                Data Akun
                            </h4>

                            <p class="mt-1 text-xs text-gray-500">
                                Digunakan anggota untuk masuk ke sistem.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Nama --}}
                            <div>
                                <label
                                    for="nama"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Nama Lengkap
                                </label>

                                <input
                                    type="text"
                                    id="nama"
                                    name="nama"
                                    value="{{ old('nama') }}"
                                    required
                                    autofocus
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Email
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        {{-- Data Anggota --}}
                        <div class="pt-2">
                            <h4 class="text-sm font-semibold text-gray-700">
                                Data Keanggotaan
                            </h4>

                            <p class="mt-1 text-xs text-gray-500">
                                Informasi identitas dan data dasar anggota.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- NIK --}}
                            <div>
                                <label
                                    for="nik"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    NIK
                                </label>

                                <input
                                    type="text"
                                    id="nik"
                                    name="nik"
                                    value="{{ old('nik') }}"
                                    required
                                    maxlength="16"
                                    minlength="16"
                                    inputmode="numeric"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                <p class="mt-1 text-xs text-gray-500">
                                    NIK harus terdiri dari 16 digit.
                                </p>

                                @error('nik')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- No HP --}}
                            <div>
                                <label
                                    for="no_hp"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Nomor HP
                                </label>

                                <input
                                    type="text"
                                    id="no_hp"
                                    name="no_hp"
                                    value="{{ old('no_hp') }}"
                                    required
                                    maxlength="15"
                                    inputmode="tel"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('no_hp')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Pekerjaan --}}
                            <div>
                                <label
                                    for="pekerjaan"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Pekerjaan
                                </label>

                                <input
                                    type="text"
                                    id="pekerjaan"
                                    name="pekerjaan"
                                    value="{{ old('pekerjaan') }}"
                                    maxlength="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('pekerjaan')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tanggal Daftar --}}
                            <div>
                                <label
                                    for="tanggal_daftar"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Tanggal Daftar
                                </label>

                                <input
                                    type="date"
                                    id="tanggal_daftar"
                                    name="tanggal_daftar"
                                    value="{{ old('tanggal_daftar', now()->format('Y-m-d')) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('tanggal_daftar')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label
                                for="alamat"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Alamat
                            </label>

                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('alamat') }}</textarea>

                            @error('alamat')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Informasi Sistem --}}
                        <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                            <p class="text-sm font-semibold text-gray-700">
                                Informasi Akun
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Sistem akan membuat akun dengan role
                                <span class="font-semibold text-gray-700">
                                    Anggota
                                </span>
                                secara otomatis.
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Password awal akan dibuat oleh sistem.
                            </p>
                        </div>

                    </div>

                    {{-- Footer Form --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">

                        <a
                            href="{{ route('anggota.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Simpan Anggota
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>