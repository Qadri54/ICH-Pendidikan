<x-main-layout title="Absensi Saya">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Absensi Saya</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ $errors->first('error') }}
        </div>
    @endif

    <div class="max-w-lg">

        @if(! $todayRecord)
            {{-- Belum absen hari ini --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6 mb-5">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full bg-ich-warning-soft flex items-center justify-center">
                        <x-ich-icon name="clock" :size="20" color="#E09F17"/>
                    </div>
                    <div>
                        <p class="font-ui font-bold text-ich-ink-900">Belum Absen</p>
                        <p class="font-sans text-xs text-ich-ink-400">Pilih metode absensi di bawah</p>
                    </div>
                </div>

                {{-- Tab: Check-in GPS vs Izin/Sakit --}}
                <div x-data="{ tab: 'checkin' }">
                    <div class="flex gap-2 mb-5">
                        <button @click="tab = 'checkin'" type="button"
                                :class="tab === 'checkin'
                                    ? 'bg-ich-green text-white'
                                    : 'bg-ich-surface text-ich-ink-600'"
                                class="flex-1 py-2 text-xs font-ui font-bold rounded-lg transition-colors">
                            Check-in GPS
                        </button>
                        <button @click="tab = 'izin'" type="button"
                                :class="tab === 'izin'
                                    ? 'bg-ich-green text-white'
                                    : 'bg-ich-surface text-ich-ink-600'"
                                class="flex-1 py-2 text-xs font-ui font-bold rounded-lg transition-colors">
                            Izin / Sakit
                        </button>
                    </div>

                    {{-- Form Check-in GPS --}}
                    <div x-show="tab === 'checkin'" x-cloak
                         x-data="{
                            lat: '', lng: '', acc: '',
                            loading: false,
                            error: '',
                            getLocation() {
                                this.loading = true;
                                this.error = '';
                                navigator.geolocation.getCurrentPosition(
                                    pos => {
                                        this.lat     = pos.coords.latitude;
                                        this.lng     = pos.coords.longitude;
                                        this.acc     = pos.coords.accuracy;
                                        this.loading = false;
                                    },
                                    err => {
                                        this.error   = 'Gagal mendapatkan lokasi: ' + err.message;
                                        this.loading = false;
                                    },
                                    { enableHighAccuracy: true, timeout: 10000 }
                                );
                            }
                         }">

                        @if(! $zone)
                            <div class="bg-ich-warning-soft rounded-lg p-3 text-xs font-sans text-ich-warning">
                                Titik koordinat sekolah belum dikonfigurasi. Hubungi admin.
                            </div>
                        @else
                            <form method="POST" action="{{ route('guru.absensi-guru.checkin') }}"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="latitude"  :value="lat">
                                <input type="hidden" name="longitude" :value="lng">
                                <input type="hidden" name="accuracy"  :value="acc">

                                {{-- Selfie --}}
                                <div class="mb-4">
                                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Foto Selfie</label>
                                    <input type="file" name="selfie" accept="image/*" capture="user"
                                           class="w-full text-sm font-sans text-ich-ink-600
                                                  file:mr-3 file:py-1.5 file:px-3
                                                  file:rounded-lg file:border-0
                                                  file:bg-ich-green file:text-white file:font-ui file:font-bold file:text-xs
                                                  @error('selfie') border border-ich-error rounded-lg @enderror">
                                    @error('selfie')
                                        <p class="text-xs text-ich-error mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Lokasi --}}
                                <div class="mb-4 p-3 bg-ich-surface rounded-lg text-xs font-sans">
                                    <template x-if="lat">
                                        <p class="text-ich-green font-semibold">
                                            Lokasi: <span x-text="lat.toFixed(6)"></span>, <span x-text="lng.toFixed(6)"></span>
                                            (akurasi ±<span x-text="Math.round(acc)"></span>m)
                                        </p>
                                    </template>
                                    <template x-if="!lat">
                                        <p class="text-ich-ink-400">Lokasi belum diambil</p>
                                    </template>
                                    <template x-if="error">
                                        <p class="text-ich-error" x-text="error"></p>
                                    </template>
                                </div>

                                <div class="flex gap-2">
                                    <button @click.prevent="getLocation()" type="button"
                                            class="flex-1 py-2.5 bg-ich-blue-soft text-ich-teal font-ui font-bold text-sm
                                                   rounded-ich-lg border-2 border-ich-teal/20 transition-colors
                                                   hover:bg-ich-teal/10"
                                            :disabled="loading">
                                        <span x-text="loading ? 'Mengambil...' : 'Ambil Lokasi'"></span>
                                    </button>
                                    <button type="submit"
                                            :disabled="!lat"
                                            :class="lat ? 'bg-ich-green hover:bg-ich-green-dark' : 'bg-ich-ink-200 cursor-not-allowed'"
                                            class="flex-1 py-2.5 text-white font-ui font-bold text-sm
                                                   rounded-ich-lg shadow-ich-btn transition-colors">
                                        Check-in
                                    </button>
                                </div>

                                {{-- Radius info --}}
                                <p class="text-xs text-ich-ink-400 font-sans mt-2 text-center">
                                    Harus dalam radius {{ number_format($zone['radius_meter']) }}m dari sekolah
                                </p>
                            </form>
                        @endif
                    </div>

                    {{-- Form Izin/Sakit --}}
                    <div x-show="tab === 'izin'" x-cloak>
                        <form method="POST" action="{{ route('guru.absensi-guru.izin-sakit') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Keterangan</label>
                                <div class="flex gap-3">
                                    <label class="flex-1 flex items-center gap-2 cursor-pointer p-3 rounded-lg
                                                  border-2 border-ich-line has-[:checked]:border-[#8B5CF6]
                                                  has-[:checked]:bg-ich-purple-soft transition-colors">
                                        <input type="radio" name="status" value="Izin" class="accent-[#8B5CF6]">
                                        <span class="font-ui font-bold text-sm">Izin</span>
                                    </label>
                                    <label class="flex-1 flex items-center gap-2 cursor-pointer p-3 rounded-lg
                                                  border-2 border-ich-line has-[:checked]:border-ich-error
                                                  has-[:checked]:bg-ich-error-soft transition-colors">
                                        <input type="radio" name="status" value="Sakit" class="accent-ich-error">
                                        <span class="font-ui font-bold text-sm">Sakit</span>
                                    </label>
                                </div>
                            </div>
                            <button type="submit"
                                    class="w-full py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        @elseif($todayRecord->check_out_time === null && $todayRecord->attendance_status === 'Hadir')
            {{-- Sudah check-in, belum check-out --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full bg-ich-success-soft flex items-center justify-center">
                        <x-ich-icon name="check_circle" :size="20" color="#009966"/>
                    </div>
                    <div>
                        <p class="font-ui font-bold text-ich-green">Sudah Check-in</p>
                        <p class="font-sans text-xs text-ich-ink-400">
                            {{ $todayRecord->check_in_time->format('H:i') }} WIB
                            @if($todayRecord->is_within_geofence === 'ya')
                                · <span class="text-ich-success">Dalam area</span>
                            @elseif($todayRecord->is_within_geofence === 'tidak')
                                · <span class="text-ich-error">Di luar area</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Form Check-out --}}
                <div x-data="{
                    lat: '', lng: '', loading: false, error: '',
                    getLocation() {
                        this.loading = true;
                        navigator.geolocation.getCurrentPosition(
                            pos => { this.lat = pos.coords.latitude; this.lng = pos.coords.longitude; this.loading = false; },
                            err => { this.error = 'Gagal mendapatkan lokasi.'; this.loading = false; },
                            { enableHighAccuracy: true, timeout: 10000 }
                        );
                    }
                }">
                    <form method="POST" action="{{ route('guru.absensi-guru.checkout') }}">
                        @csrf
                        <input type="hidden" name="record_id" value="{{ $todayRecord->attendance_record_id }}">
                        <input type="hidden" name="latitude"  :value="lat">
                        <input type="hidden" name="longitude" :value="lng">

                        <div class="mb-4 p-3 bg-ich-surface rounded-lg text-xs font-sans">
                            <template x-if="lat">
                                <p class="text-ich-green font-semibold">
                                    Lokasi: <span x-text="lat.toFixed(6)"></span>, <span x-text="lng.toFixed(6)"></span>
                                </p>
                            </template>
                            <template x-if="!lat">
                                <p class="text-ich-ink-400">Lokasi belum diambil</p>
                            </template>
                        </div>

                        <div class="flex gap-2">
                            <button @click.prevent="getLocation()" type="button"
                                    class="flex-1 py-2.5 bg-ich-blue-soft text-ich-teal font-ui font-bold text-sm
                                           rounded-ich-lg border-2 border-ich-teal/20 hover:bg-ich-teal/10">
                                <span x-text="loading ? 'Mengambil...' : 'Ambil Lokasi'"></span>
                            </button>
                            <button type="submit"
                                    :disabled="!lat"
                                    :class="lat ? 'bg-ich-ink-900 hover:bg-ich-ink-700' : 'bg-ich-ink-200 cursor-not-allowed'"
                                    class="flex-1 py-2.5 text-white font-ui font-bold text-sm
                                           rounded-ich-lg shadow-ich-btn transition-colors">
                                Check-out
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        @else
            {{-- Absensi hari ini sudah selesai --}}
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                @php
                    $stCfg = match($todayRecord->attendance_status) {
                        'Hadir'             => ['icon' => 'check_circle', 'color' => '#009966', 'bg' => 'bg-ich-success-soft', 'label' => 'Hadir'],
                        'Izin'              => ['icon' => 'info',         'color' => '#8B5CF6', 'bg' => 'bg-ich-purple-soft', 'label' => 'Izin'],
                        'Sakit'             => ['icon' => 'alert',        'color' => '#EF4444', 'bg' => 'bg-ich-error-soft', 'label' => 'Sakit'],
                        'Tanpa Keterangan'  => ['icon' => 'clock',        'color' => '#E09F17', 'bg' => 'bg-ich-warning-soft', 'label' => 'Tanpa Keterangan'],
                        default             => ['icon' => 'clock',        'color' => '#6B7280', 'bg' => 'bg-ich-surface', 'label' => $todayRecord->attendance_status],
                    };
                @endphp
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full {{ $stCfg['bg'] }} flex items-center justify-center">
                        <x-ich-icon :name="$stCfg['icon']" :size="20" :color="$stCfg['color']"/>
                    </div>
                    <div>
                        <p class="font-ui font-bold text-ich-ink-900">Absensi Selesai — {{ $stCfg['label'] }}</p>
                        <p class="font-sans text-xs text-ich-ink-400">
                            {{ $todayRecord->created_at->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>

                @if($todayRecord->attendance_status === 'Hadir')
                    <div class="space-y-2 text-sm font-sans text-ich-ink-600">
                        <div class="flex justify-between">
                            <span>Check-in</span>
                            <span class="font-semibold">{{ $todayRecord->check_in_time?->format('H:i') ?? '-' }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Check-out</span>
                            <span class="font-semibold">{{ $todayRecord->check_out_time?->format('H:i') ?? 'Belum check-out' }} WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Geofence</span>
                            <span class="font-semibold {{ $todayRecord->is_within_geofence === 'ya' ? 'text-ich-success' : 'text-ich-error' }}">
                                {{ $todayRecord->is_within_geofence === 'ya' ? 'Dalam Area' : 'Di Luar Area' }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>

</x-main-layout>
