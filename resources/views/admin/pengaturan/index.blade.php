@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Pengaturan">

    <div class="mb-6 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-[#F3F4F6] flex items-center justify-center">
            <svg class="w-5 h-5 text-ich-ink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Pengaturan Sistem</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Kelola konfigurasi pendaftaran, semester, dan absensi</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{ tab: 'pendaftaran' }" class="max-w-4xl">

        {{-- Status Overview Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-4 cursor-pointer hover:ring-2 hover:ring-ich-teal/30 transition-all"
                 @click="tab = 'pendaftaran'">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $registrationSetting->is_registration_period ? 'bg-[#D1FAE5]' : 'bg-[#F3F4F6]' }}">
                    <x-ich-icon name="clipboard" :size="22" :color="$registrationSetting->is_registration_period ? '#009966' : '#6B7280'"/>
                </div>
                <div>
                    <p class="text-xs font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Pendaftaran</p>
                    <p class="text-sm font-display font-bold {{ $registrationSetting->is_registration_period ? 'text-[#009966]' : 'text-ich-ink-500' }}">
                        {{ $registrationSetting->is_registration_period ? 'Dibuka' : 'Ditutup' }}
                    </p>
                </div>
            </div>

            @php $activeSemester = $semesters->firstWhere('is_active', true); @endphp
            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-4 cursor-pointer hover:ring-2 hover:ring-ich-teal/30 transition-all"
                 @click="tab = 'semester'">
                <div class="w-11 h-11 rounded-xl bg-[#EDE9FE] flex items-center justify-center flex-shrink-0">
                    <x-ich-icon name="book" :size="22" color="#8B5CF6"/>
                </div>
                <div>
                    <p class="text-xs font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Semester</p>
                    <p class="text-sm font-display font-bold text-ich-ink-900">
                        {{ $activeSemester ? "Sem. {$activeSemester->semester} — {$activeSemester->tahun_ajaran}" : 'Belum diatur' }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-ich-card p-4 flex items-center gap-4 cursor-pointer hover:ring-2 hover:ring-ich-teal/30 transition-all"
                 @click="tab = 'geofence'">
                <div class="w-11 h-11 rounded-xl bg-[#FEF5DC] flex items-center justify-center flex-shrink-0">
                    <x-ich-icon name="user_check" :size="22" color="#E09F17"/>
                </div>
                <div>
                    <p class="text-xs font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Geofence</p>
                    <p class="text-sm font-display font-bold text-ich-ink-900">
                        {{ ($settings['geofence_radius_meter'] ?? false) ? ($settings['geofence_radius_meter'] . 'm radius') : 'Belum diatur' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex gap-1 bg-white rounded-xl shadow-ich-card p-1.5 mb-5">
            <button @click="tab = 'pendaftaran'" type="button"
                    :class="tab === 'pendaftaran' ? 'bg-ich-teal text-white shadow-sm' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-ui font-bold text-sm transition-all">
                <x-ich-icon name="clipboard" :size="16"/>
                Pendaftaran
            </button>
            <button @click="tab = 'semester'" type="button"
                    :class="tab === 'semester' ? 'bg-ich-teal text-white shadow-sm' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-ui font-bold text-sm transition-all">
                <x-ich-icon name="book" :size="16"/>
                Semester
            </button>
            <button @click="tab = 'geofence'" type="button"
                    :class="tab === 'geofence' ? 'bg-ich-teal text-white shadow-sm' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-ui font-bold text-sm transition-all">
                <x-ich-icon name="user_check" :size="16"/>
                Geofence
            </button>
        </div>

        {{-- Tab: Pendaftaran --}}
        <div x-show="tab === 'pendaftaran'" x-cloak>
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-lg bg-ich-teal/10 flex items-center justify-center">
                        <x-ich-icon name="clipboard" :size="18" color="#009688"/>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-ich-ink-900">Penerimaan Siswa Baru</h2>
                        <p class="text-xs text-ich-ink-400 font-sans">Atur masa penerimaan pendaftaran siswa baru</p>
                    </div>
                </div>

                <div class="bg-[#F5F6FA] rounded-xl p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center
                                        {{ $registrationSetting->is_registration_period ? 'bg-[#D1FAE5]' : 'bg-gray-200' }}">
                                @if($registrationSetting->is_registration_period)
                                    <x-ich-icon name="check" :size="24" color="#009966"/>
                                @else
                                    <x-ich-icon name="close" :size="24" color="#6B7280"/>
                                @endif
                            </div>
                            <div>
                                <p class="font-display font-bold text-lg
                                          {{ $registrationSetting->is_registration_period ? 'text-[#009966]' : 'text-ich-ink-500' }}">
                                    {{ $registrationSetting->is_registration_period ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}
                                </p>
                                <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                    {{ $registrationSetting->is_registration_period
                                        ? 'Orang tua dapat mengirim formulir pendaftaran.'
                                        : 'Orang tua tidak dapat mengirim formulir pendaftaran.' }}
                                </p>
                            </div>
                        </div>
                        @if(! $isReadOnly)
                            <form method="POST" action="{{ route('admin.pengaturan.toggle-pendaftaran') }}" class="shrink-0">
                                @csrf
                                <button type="submit"
                                        class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors
                                               {{ $registrationSetting->is_registration_period ? 'bg-ich-green' : 'bg-ich-ink-300' }}">
                                    <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition-transform
                                                 {{ $registrationSetting->is_registration_period ? 'translate-x-7' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Semester --}}
        <div x-show="tab === 'semester'" x-cloak>
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                        <x-ich-icon name="book" :size="18" color="#8B5CF6"/>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-ich-ink-900">Manajemen Semester</h2>
                        <p class="text-xs text-ich-ink-400 font-sans">Kelola tahun ajaran dan semester aktif</p>
                    </div>
                </div>

                {{-- Daftar Semester --}}
                @if($semesters->isEmpty())
                    <div class="bg-[#F5F6FA] rounded-xl p-8 text-center mb-6">
                        <x-ich-icon name="book" :size="36" color="#99A1AF" class="mx-auto mb-2"/>
                        <p class="font-ui font-bold text-sm text-ich-ink-500">Belum Ada Semester</p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-1">Tambahkan semester menggunakan form di bawah.</p>
                    </div>
                @else
                    <div class="space-y-3 mb-6">
                        @foreach($semesters as $sem)
                            <div class="rounded-xl border-2 p-4 flex items-center justify-between gap-3 transition-all
                                        {{ $sem->is_active ? 'border-[#009966] bg-[#D1FAE5]/30' : 'border-ich-line bg-white hover:border-ich-ink-200' }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                                {{ $sem->is_active ? 'bg-[#009966] text-white' : 'bg-[#F5F6FA]' }}">
                                        <span class="font-display font-bold text-sm">{{ $sem->semester }}</span>
                                    </div>
                                    <div>
                                        <p class="font-ui font-bold text-sm text-ich-ink-900">
                                            Semester {{ $sem->semester }}
                                            <span class="font-normal text-ich-ink-500">— T.A {{ $sem->tahun_ajaran }}</span>
                                        </p>
                                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                                            {{ $sem->tanggal_mulai->translatedFormat('d M Y') }}
                                            – {{ $sem->tanggal_selesai->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($sem->is_active)
                                        <span class="px-3 py-1.5 bg-[#009966] text-white text-xs font-ui font-bold rounded-lg">
                                            Aktif
                                        </span>
                                    @elseif(! $isReadOnly)
                                        <form method="POST" action="{{ route('admin.pengaturan.semester.activate', $sem) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 text-xs font-ui font-bold bg-ich-teal/10 text-ich-teal
                                                           rounded-lg hover:bg-ich-teal hover:text-white transition-colors">
                                                Aktifkan
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pengaturan.semester.destroy', $sem) }}"
                                              onsubmit="return confirm('Hapus semester ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1.5 text-xs font-ui font-bold bg-red-50 text-ich-error
                                                           rounded-lg hover:bg-ich-error hover:text-white transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Form Tambah Semester --}}
                @if(! $isReadOnly)
                    <div class="border-t border-ich-line pt-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-ich-icon name="plus" :size="16" color="#009688"/>
                            <p class="font-ui font-bold text-sm text-ich-ink-700">Tambah Semester Baru</p>
                        </div>

                        <form method="POST" action="{{ route('admin.pengaturan.semester.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tahun Ajaran</label>
                                    <input type="text" name="tahun_ajaran"
                                           value="{{ old('tahun_ajaran') }}"
                                           placeholder="2025-2026"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('tahun_ajaran') border-ich-error @else border-ich-line @enderror">
                                    @error('tahun_ajaran')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Semester</label>
                                    <select name="semester"
                                            class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg
                                                   font-sans text-sm focus:outline-none focus:border-ich-teal">
                                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai"
                                           value="{{ old('tanggal_mulai') }}"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('tanggal_mulai') border-ich-error @enderror">
                                    @error('tanggal_mulai')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai"
                                           value="{{ old('tanggal_selesai') }}"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('tanggal_selesai') border-ich-error @enderror">
                                    @error('tanggal_selesai')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit"
                                    class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                                Tambah Semester
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab: Geofence --}}
        <div x-show="tab === 'geofence'" x-cloak>
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-lg bg-[#FEF5DC] flex items-center justify-center">
                        <x-ich-icon name="user_check" :size="18" color="#E09F17"/>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-ich-ink-900">Konfigurasi Geofencing & Jam Absensi</h2>
                        <p class="text-xs text-ich-ink-400 font-sans">Atur lokasi sekolah dan waktu absensi guru</p>
                    </div>
                </div>

                @if($isReadOnly)
                    {{-- Read-only view --}}
                    <div class="space-y-4">
                        <div class="bg-[#F5F6FA] rounded-xl p-5">
                            <p class="font-ui font-bold text-xs text-ich-ink-400 uppercase tracking-wider mb-3">Koordinat Sekolah</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-ich-ink-400 font-sans">Latitude</p>
                                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $settings['geofence_latitude'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-ich-ink-400 font-sans">Longitude</p>
                                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $settings['geofence_longitude'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-ich-ink-400 font-sans">Radius</p>
                                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ ($settings['geofence_radius_meter'] ?? '-') . 'm' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#F5F6FA] rounded-xl p-5">
                            <p class="font-ui font-bold text-xs text-ich-ink-400 uppercase tracking-wider mb-3">Jam Absensi</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-ich-ink-400 font-sans">Mulai Check-in</p>
                                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $settings['check_in_start'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-ich-ink-400 font-sans">Akhir Check-in</p>
                                    <p class="font-ui font-bold text-sm text-ich-ink-900">{{ $settings['check_in_end'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#F0F4FF] rounded-lg p-3">
                            <p class="font-sans text-xs text-ich-ink-500">
                                Anda tidak memiliki akses untuk mengubah pengaturan ini.
                            </p>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-6">
                        @csrf

                        {{-- Koordinat --}}
                        <div>
                            <p class="font-ui font-bold text-xs text-ich-ink-400 uppercase tracking-wider mb-3">Koordinat Sekolah</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Latitude</label>
                                    <input type="text" name="geofence_latitude"
                                           value="{{ old('geofence_latitude', $settings['geofence_latitude'] ?? '') }}"
                                           placeholder="-6.2088"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('geofence_latitude') border-ich-error @else border-ich-line @enderror">
                                    @error('geofence_latitude')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Longitude</label>
                                    <input type="text" name="geofence_longitude"
                                           value="{{ old('geofence_longitude', $settings['geofence_longitude'] ?? '') }}"
                                           placeholder="106.8456"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('geofence_longitude') border-ich-error @else border-ich-line @enderror">
                                    @error('geofence_longitude')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Radius (meter)</label>
                                    <input type="number" name="geofence_radius_meter"
                                           value="{{ old('geofence_radius_meter', $settings['geofence_radius_meter'] ?? '') }}"
                                           placeholder="100"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('geofence_radius_meter') border-ich-error @else border-ich-line @enderror">
                                    @error('geofence_radius_meter')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Jam Absensi --}}
                        <div>
                            <p class="font-ui font-bold text-xs text-ich-ink-400 uppercase tracking-wider mb-3">Jam Absensi</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Mulai Check-in</label>
                                    <input type="time" name="check_in_start"
                                           value="{{ old('check_in_start', $settings['check_in_start'] ?? '') }}"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('check_in_start') border-ich-error @enderror">
                                    @error('check_in_start')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Akhir Check-in</label>
                                    <input type="time" name="check_in_end"
                                           value="{{ old('check_in_end', $settings['check_in_end'] ?? '') }}"
                                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal
                                                  @error('check_in_end') border-ich-error @enderror">
                                    @error('check_in_end')
                                        <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#F0F4FF] rounded-lg p-3">
                            <p class="font-sans text-xs text-ich-ink-500">
                                <strong>Tip:</strong> Untuk mendapatkan koordinat sekolah, buka Google Maps → klik kanan pada lokasi → salin koordinat.
                            </p>
                        </div>

                        <button type="submit"
                                class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                            Simpan Pengaturan
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>

</x-main-layout>
