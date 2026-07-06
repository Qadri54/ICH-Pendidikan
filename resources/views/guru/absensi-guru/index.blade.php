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
                            lat: '', lng: '', acc: '', dist: '',
                            loading: false,
                            error: '',
                            watchId: null,
                            centerLat: {{ $zone['latitude'] }},
                            centerLng: {{ $zone['longitude'] }},
                            radius: {{ $zone['radius_meter'] }},
                            maxAccuracy: {{ \App\Models\AttendanceSetting::where('setting_key', 'max_gps_accuracy')->value('setting_value') ?? 100 }},
                            get ready() { return this.lat && this.acc <= this.maxAccuracy },
                            get withinZone() { return this.ready && (this.dist + this.acc) <= this.radius },
                            haversine(lat1, lng1, lat2, lng2) {
                                const R = 6371000;
                                const toRad = d => d * Math.PI / 180;
                                const dLat = toRad(lat2 - lat1);
                                const dLng = toRad(lng2 - lng1);
                                const a = Math.sin(dLat/2)**2 + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng/2)**2;
                                return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
                            },
                            getLocation() {
                                if (this.watchId) navigator.geolocation.clearWatch(this.watchId);
                                this.loading = true;
                                this.error = '';
                                this.lat = '';
                                this.dist = '';
                                this.watchId = navigator.geolocation.watchPosition(
                                    pos => {
                                        this.acc = Math.round(pos.coords.accuracy);
                                        if (pos.coords.accuracy <= this.maxAccuracy) {
                                            this.lat = pos.coords.latitude;
                                            this.lng = pos.coords.longitude;
                                            this.dist = this.haversine(this.lat, this.lng, this.centerLat, this.centerLng);
                                            this.loading = false;
                                            navigator.geolocation.clearWatch(this.watchId);
                                            this.watchId = null;
                                        }
                                    },
                                    err => {
                                        this.error = 'Gagal mendapatkan lokasi: ' + err.message;
                                        this.loading = false;
                                    },
                                    { enableHighAccuracy: true, timeout: 30000, maximumAge: 0 }
                                );
                            },
                            destroy() { if (this.watchId) navigator.geolocation.clearWatch(this.watchId); }
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
                                    <template x-if="ready && withinZone">
                                        <div>
                                            <p class="text-ich-green font-semibold">
                                                Anda berjarak <span x-text="dist"></span>m dari sekolah (akurasi ±<span x-text="acc"></span>m)
                                            </p>
                                            <p class="text-ich-ink-400 mt-1">Anda berada dalam area sekolah</p>
                                        </div>
                                    </template>
                                    <template x-if="ready && !withinZone">
                                        <div>
                                            <p class="text-ich-error font-semibold">
                                                Anda berjarak <span x-text="dist"></span>m dari sekolah (akurasi ±<span x-text="acc"></span>m)
                                            </p>
                                            <p class="text-ich-ink-400 mt-1">Anda berada di luar area sekolah (maks. <span x-text="radius"></span>m)</p>
                                        </div>
                                    </template>
                                    <template x-if="loading">
                                        <div>
                                            <p class="text-ich-teal font-semibold">Mencari sinyal GPS yang akurat...</p>
                                            <p class="text-ich-ink-400 mt-1" x-show="acc">Akurasi saat ini: ±<span x-text="acc"></span>m (butuh ≤<span x-text="maxAccuracy"></span>m)</p>
                                        </div>
                                    </template>
                                    <template x-if="!lat && !loading && !error">
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
                                        <span x-text="loading ? 'Mencari...' : (ready ? 'Ambil Ulang' : 'Ambil Lokasi')"></span>
                                    </button>
                                    <button type="submit"
                                            :disabled="!withinZone"
                                            :class="withinZone ? 'bg-ich-green hover:bg-ich-green-dark' : 'bg-ich-ink-200 cursor-not-allowed'"
                                            class="flex-1 py-2.5 text-white font-ui font-bold text-sm
                                                   rounded-ich-lg shadow-ich-btn transition-colors">
                                        Check-in
                                    </button>
                                </div>

                                {{-- Radius info --}}
                                <p class="text-xs text-ich-ink-400 font-sans mt-2 text-center">
                                    Radius sekolah: {{ $zone['radius_meter'] }}m · Maks. akurasi GPS: {{ \App\Models\AttendanceSetting::where('setting_key', 'max_gps_accuracy')->value('setting_value') ?? 100 }}m
                                </p>
                            </form>
                        @endif
                    </div>

                    {{-- Form Izin/Sakit --}}
                    <div x-show="tab === 'izin'" x-cloak>
                        <form method="POST" action="{{ route('guru.absensi-guru.izin-sakit') }}" x-data="{ izinStatus: '' }">
                            @csrf
                            <div class="mb-4">
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Status</label>
                                <div class="flex gap-3">
                                    <label class="flex-1 flex items-center gap-2 cursor-pointer p-3 rounded-lg
                                                  border-2 border-ich-line has-[:checked]:border-[#8B5CF6]
                                                  has-[:checked]:bg-ich-purple-soft transition-colors">
                                        <input type="radio" name="status" value="Izin" class="accent-[#8B5CF6]"
                                               @change="izinStatus = 'Izin'">
                                        <span class="font-ui font-bold text-sm">Izin</span>
                                    </label>
                                    <label class="flex-1 flex items-center gap-2 cursor-pointer p-3 rounded-lg
                                                  border-2 border-ich-line has-[:checked]:border-ich-error
                                                  has-[:checked]:bg-ich-error-soft transition-colors">
                                        <input type="radio" name="status" value="Sakit" class="accent-ich-error"
                                               @change="izinStatus = 'Sakit'">
                                        <span class="font-ui font-bold text-sm">Sakit</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-4" x-show="izinStatus === 'Izin'" x-transition>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Keterangan Izin</label>
                                <textarea name="keterangan_izin" rows="2" placeholder="Tuliskan alasan izin..."
                                          class="w-full px-3 py-2 bg-white border-2 border-ich-line rounded-ich-lg
                                                 font-sans text-sm focus:outline-none focus:border-ich-teal resize-none"></textarea>
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
                            <span>Jam Absensi</span>
                            <span class="font-semibold">{{ $todayRecord->check_in_time?->format('H:i') ?? '-' }} WIB</span>
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
