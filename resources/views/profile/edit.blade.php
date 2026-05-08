@php
$isMobile = auth()->user()?->role?->role_name === 'Orang Tua';
$layout   = $isMobile ? 'mobile-layout' : 'main-layout';
@endphp

<x-dynamic-component :component="$layout" title="Pengaturan Profil" page-title="Pengaturan Profil">

    @unless($isMobile)
    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Pengaturan Profil</h1>
        <p class="text-sm text-ich-ink-600 mt-1">Kelola informasi akun dan keamanan Anda</p>
    </div>
    @endunless

    <div class="{{ $isMobile ? 'pb-6 space-y-4' : 'max-w-2xl space-y-6' }}">

        <div class="bg-white rounded-xl shadow-ich-card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white rounded-xl shadow-ich-card p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white rounded-xl shadow-ich-card p-6">
            @include('profile.partials.delete-user-form')
        </div>

    </div>

</x-dynamic-component>
