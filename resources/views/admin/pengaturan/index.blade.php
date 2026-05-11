@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Pengaturan">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Pengaturan Sistem</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Konfigurasi absensi dan geofencing</p>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-xl space-y-6">

        {{-- Masa Pendaftaran --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h3 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-5">
                Masa Pendaftaran Siswa Baru
            </h3>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-ui font-semibold text-sm text-ich-ink-900">Status Penerimaan</p>
                    <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                        Jika dinonaktifkan, orang tua tidak dapat mengirim formulir pendaftaran.
                    </p>
                </div>
                @if(! $isReadOnly)
                    <form method="POST" action="{{ route('admin.pengaturan.toggle-pendaftaran') }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors
                                       {{ $registrationSetting->is_registration_period ? 'bg-ich-green' : 'bg-ich-ink-400' }}">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform
                                         {{ $registrationSetting->is_registration_period ? 'translate-x-6' : 'translate-x-1' }}">
                            </span>
                        </button>
                    </form>
                @else
                    <span class="px-2 py-1 rounded-full text-xs font-ui font-bold
                                 {{ $registrationSetting->is_registration_period ? 'bg-[#D1FAE5] text-[#009966]' : 'bg-[#F3F4F6] text-ich-ink-500' }}">
                        {{ $registrationSetting->is_registration_period ? 'Dibuka' : 'Ditutup' }}
                    </span>
                @endif
            </div>
            <p class="mt-3 text-xs font-ui font-bold
                       {{ $registrationSetting->is_registration_period ? 'text-ich-green' : 'text-ich-ink-400' }}">
                {{ $registrationSetting->is_registration_period ? 'Pendaftaran sedang DIBUKA' : 'Pendaftaran sedang DITUTUP' }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-ich-card p-6">
            @if($isReadOnly)
                <p class="text-sm text-ich-ink-400 font-sans">Anda tidak memiliki akses untuk mengubah pengaturan.</p>
            @else
            <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-5">
                @csrf

                <h3 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-4">
                    Konfigurasi Geofencing
                </h3>

                @foreach([
                    ['geofence_latitude',     'Latitude Pusat Geofence',  'text', '-6.2088'],
                    ['geofence_longitude',    'Longitude Pusat Geofence', 'text', '106.8456'],
                    ['geofence_radius_meter', 'Radius (meter)',           'number', '100'],
                ] as [$key, $label, $type, $placeholder])
                    <div>
                        <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">{{ $label }}</label>
                        <input type="{{ $type }}" name="{{ $key }}"
                               value="{{ old($key, $settings[$key] ?? '') }}"
                               placeholder="{{ $placeholder }}"
                               class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg
                                      font-sans text-sm text-ich-ink-900
                                      focus:outline-none focus:border-ich-teal
                                      @error($key) border-ich-error @else border-ich-teal @enderror">
                        @error($key)
                            <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <h3 class="font-ui font-bold text-ich-ink-900 border-b border-ich-line pb-3 mb-1 mt-6">
                    Jam Absensi
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['check_in_start', 'Mulai Check-in', '06:00'],
                        ['check_in_end',   'Akhir Check-in',  '08:00'],
                    ] as [$key, $label, $placeholder])
                        <div>
                            <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">{{ $label }}</label>
                            <input type="time" name="{{ $key }}"
                                   value="{{ old($key, $settings[$key] ?? '') }}"
                                   class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg
                                          font-sans text-sm focus:outline-none focus:border-ich-teal-dark
                                          @error($key) border-ich-error @enderror">
                            @error($key)
                                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                   rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

</x-main-layout>
