<x-mobile-layout title="Daftar Anak" page-title="Daftar Anak">

    {{-- Closed notice --}}
    @unless($isRegistrationOpen)
        <div class="bg-ich-warning-soft rounded-xl p-5 flex items-start gap-3 mb-5">
            <x-ich-icon name="clock" :size="24" color="#E09F17"/>
            <div>
                <p class="font-ui font-bold text-sm text-ich-warning">Pendaftaran Sedang Ditutup</p>
                <p class="font-sans text-xs text-ich-ink-600 mt-1">
                    Pendaftaran saat ini belum dibuka. Anda tidak dapat mengirim formulir pendaftaran.
                </p>
            </div>
        </div>
    @endunless

    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('pendaftaran.store') }}" class="space-y-4"
          x-data="{ jenis: '{{ old('jenis_pendaftaran', 'TK') }}' }">
        @csrf

        {{-- Jenis Pendaftaran --}}
        <div class="bg-white rounded-xl shadow-ich-card p-5">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3 mb-4">
                Jenis Pendaftaran
            </h3>
            <div class="flex gap-3">
                <label class="flex-1 flex items-center gap-2.5 h-11 px-3.5 border-2 rounded-ich-lg cursor-pointer transition-colors"
                       :class="jenis === 'TK' ? 'border-ich-teal bg-[#F0FDFA]' : 'border-ich-line bg-white'">
                    <input type="radio" name="jenis_pendaftaran" value="TK"
                           x-model="jenis" class="accent-ich-teal">
                    <span class="font-ui font-semibold text-sm text-ich-ink-900">PG / TK ICH</span>
                </label>
                <label class="flex-1 flex items-center gap-2.5 h-11 px-3.5 border-2 rounded-ich-lg cursor-pointer transition-colors"
                       :class="jenis === 'Mengaji' ? 'border-ich-teal bg-[#F0FDFA]' : 'border-ich-line bg-white'">
                    <input type="radio" name="jenis_pendaftaran" value="Mengaji"
                           x-model="jenis" class="accent-ich-teal">
                    <span class="font-ui font-semibold text-sm text-ich-ink-900">Magrib Mengaji</span>
                </label>
            </div>
            @error('jenis_pendaftaran')
                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Biodata Siswa --}}
        <div class="bg-white rounded-xl shadow-ich-card p-5 space-y-4">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3">
                Biodata Siswa
            </h3>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Nama Lengkap <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}"
                       placeholder="Nama lengkap anak"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('nama_siswa') border-ich-error @else border-ich-line @enderror">
                @error('nama_siswa') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Tempat Lahir <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                           placeholder="Kota"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('tempat_lahir') border-ich-error @else border-ich-line @enderror">
                    @error('tempat_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Tanggal Lahir <span class="text-ich-error">*</span>
                    </label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('tanggal_lahir') border-ich-error @else border-ich-line @enderror">
                    @error('tanggal_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Alamat <span class="text-ich-error">*</span>
                </label>
                <textarea name="alamat" rows="2"
                          placeholder="Alamat lengkap"
                          class="w-full px-3.5 py-2.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                 focus:outline-none focus:border-ich-teal resize-none
                                 @error('alamat') border-ich-error @else border-ich-line @enderror">{{ old('alamat') }}</textarea>
                @error('alamat') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Anak ke <span class="text-ich-error">*</span>
                    </label>
                    <input type="number" name="anak_ke" value="{{ old('anak_ke') }}"
                           placeholder="1" min="1" max="20"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('anak_ke') border-ich-error @else border-ich-line @enderror">
                    @error('anak_ke') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Ukuran Baju — hanya TK --}}
                <div class="flex-1" x-show="jenis === 'TK'" x-cloak>
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Ukuran Baju <span class="text-ich-error">*</span>
                    </label>
                    <select name="ukuran_baju"
                            class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                   focus:outline-none focus:border-ich-teal
                                   @error('ukuran_baju') border-ich-error @else border-ich-line @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="S" {{ old('ukuran_baju') === 'S' ? 'selected' : '' }}>S</option>
                        <option value="M" {{ old('ukuran_baju') === 'M' ? 'selected' : '' }}>M</option>
                        <option value="L" {{ old('ukuran_baju') === 'L' ? 'selected' : '' }}>L</option>
                    </select>
                    @error('ukuran_baju') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Jenis Kelamin <span class="text-ich-error">*</span>
                </label>
                <div class="flex gap-3">
                    <label class="flex-1 flex items-center gap-2.5 h-11 px-3.5 bg-white border-2 rounded-ich-lg cursor-pointer
                                  {{ old('jenis_kelamin') === 'L' ? 'border-ich-teal' : 'border-ich-line' }}">
                        <input type="radio" name="jenis_kelamin" value="L"
                               {{ old('jenis_kelamin') === 'L' ? 'checked' : '' }}
                               class="accent-ich-teal">
                        <span class="font-ui font-semibold text-sm text-ich-ink-900">Laki-laki</span>
                    </label>
                    <label class="flex-1 flex items-center gap-2.5 h-11 px-3.5 bg-white border-2 rounded-ich-lg cursor-pointer
                                  {{ old('jenis_kelamin') === 'P' ? 'border-ich-teal' : 'border-ich-line' }}">
                        <input type="radio" name="jenis_kelamin" value="P"
                               {{ old('jenis_kelamin') === 'P' ? 'checked' : '' }}
                               class="accent-ich-teal">
                        <span class="font-ui font-semibold text-sm text-ich-ink-900">Perempuan</span>
                    </label>
                </div>
                @error('jenis_kelamin') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Biodata Ayah --}}
        <div class="bg-white rounded-xl shadow-ich-card p-5 space-y-4">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3">
                Biodata Ayah
            </h3>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Nama <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="nama_ayah"
                       value="{{ old('nama_ayah', $lastRegistration?->nama_ayah) }}"
                       placeholder="Nama lengkap ayah"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('nama_ayah') border-ich-error @else border-ich-line @enderror">
                @error('nama_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Tempat Lahir <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="tempat_lahir_ayah"
                           value="{{ old('tempat_lahir_ayah', $lastRegistration?->tempat_lahir_ayah) }}"
                           placeholder="Kota"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('tempat_lahir_ayah') border-ich-error @else border-ich-line @enderror">
                    @error('tempat_lahir_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Tanggal Lahir <span class="text-ich-error">*</span>
                    </label>
                    <input type="date" name="tanggal_lahir_ayah"
                           value="{{ old('tanggal_lahir_ayah', $lastRegistration?->tanggal_lahir_ayah?->format('Y-m-d')) }}"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('tanggal_lahir_ayah') border-ich-error @else border-ich-line @enderror">
                    @error('tanggal_lahir_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Alamat <span class="text-ich-error">*</span>
                </label>
                <textarea name="alamat_ayah" rows="2"
                          placeholder="Alamat lengkap ayah"
                          class="w-full px-3.5 py-2.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                 focus:outline-none focus:border-ich-teal resize-none
                                 @error('alamat_ayah') border-ich-error @else border-ich-line @enderror">{{ old('alamat_ayah', $lastRegistration?->alamat_ayah) }}</textarea>
                @error('alamat_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Pendidikan <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="pendidikan_ayah"
                           value="{{ old('pendidikan_ayah', $lastRegistration?->pendidikan_ayah) }}"
                           placeholder="Contoh: S1"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('pendidikan_ayah') border-ich-error @else border-ich-line @enderror">
                    @error('pendidikan_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Pekerjaan <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="pekerjaan_ayah"
                           value="{{ old('pekerjaan_ayah', $lastRegistration?->pekerjaan_ayah) }}"
                           placeholder="Contoh: Wiraswasta"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('pekerjaan_ayah') border-ich-error @else border-ich-line @enderror">
                    @error('pekerjaan_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    No. Telp / HP <span class="text-ich-error">*</span>
                </label>
                <input type="tel" name="no_telp_ayah"
                       value="{{ old('no_telp_ayah', $lastRegistration?->no_telp_ayah) }}"
                       placeholder="08xxxxxxxxxx"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('no_telp_ayah') border-ich-error @else border-ich-line @enderror">
                @error('no_telp_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Biodata Ibu --}}
        <div class="bg-white rounded-xl shadow-ich-card p-5 space-y-4">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3">
                Biodata Ibu
            </h3>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Nama <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="nama_ibu"
                       value="{{ old('nama_ibu', $lastRegistration?->nama_ibu) }}"
                       placeholder="Nama lengkap ibu"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('nama_ibu') border-ich-error @else border-ich-line @enderror">
                @error('nama_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Tempat Lahir <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="tempat_lahir_ibu"
                           value="{{ old('tempat_lahir_ibu', $lastRegistration?->tempat_lahir_ibu) }}"
                           placeholder="Kota"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('tempat_lahir_ibu') border-ich-error @else border-ich-line @enderror">
                    @error('tempat_lahir_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Tanggal Lahir <span class="text-ich-error">*</span>
                    </label>
                    <input type="date" name="tanggal_lahir_ibu"
                           value="{{ old('tanggal_lahir_ibu', $lastRegistration?->tanggal_lahir_ibu?->format('Y-m-d')) }}"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('tanggal_lahir_ibu') border-ich-error @else border-ich-line @enderror">
                    @error('tanggal_lahir_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Alamat <span class="text-ich-error">*</span>
                </label>
                <textarea name="alamat_ibu" rows="2"
                          placeholder="Alamat lengkap ibu"
                          class="w-full px-3.5 py-2.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                 focus:outline-none focus:border-ich-teal resize-none
                                 @error('alamat_ibu') border-ich-error @else border-ich-line @enderror">{{ old('alamat_ibu', $lastRegistration?->alamat_ibu) }}</textarea>
                @error('alamat_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Pekerjaan <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="pekerjaan_ibu"
                           value="{{ old('pekerjaan_ibu', $lastRegistration?->pekerjaan_ibu) }}"
                           placeholder="Contoh: Ibu Rumah Tangga"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('pekerjaan_ibu') border-ich-error @else border-ich-line @enderror">
                    @error('pekerjaan_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex-1">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                        Pendidikan <span class="text-ich-error">*</span>
                    </label>
                    <input type="text" name="pendidikan_ibu"
                           value="{{ old('pendidikan_ibu', $lastRegistration?->pendidikan_ibu) }}"
                           placeholder="Contoh: S1"
                           class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal
                                  @error('pendidikan_ibu') border-ich-error @else border-ich-line @enderror">
                    @error('pendidikan_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    No. Telp / HP <span class="text-ich-error">*</span>
                </label>
                <input type="tel" name="no_telp_ibu"
                       value="{{ old('no_telp_ibu', $lastRegistration?->no_telp_ibu) }}"
                       placeholder="08xxxxxxxxxx"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('no_telp_ibu') border-ich-error @else border-ich-line @enderror">
                @error('no_telp_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3 pb-4">
            <a href="{{ route('pendaftaran') }}"
               class="flex-1 h-12 flex items-center justify-center bg-white border-2 border-ich-line
                      text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg">
                Batal
            </a>
            <button type="submit" {{ $isRegistrationOpen ? '' : 'disabled' }}
                    class="flex-1 h-12 bg-ich-green text-white font-ui font-bold text-sm
                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors
                           disabled:opacity-50 disabled:cursor-not-allowed">
                Kirim Pendaftaran
            </button>
        </div>
    </form>

</x-mobile-layout>
