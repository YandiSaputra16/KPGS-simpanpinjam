<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">
                {{ __('Profile') }}
            </h2>

            <p class="text-muted mb-0">
                Kelola informasi akun dan keamanan profil Anda.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid">

        {{-- Informasi Profil --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Ubah Password --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Hapus Akun --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-app-layout>