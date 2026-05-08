<x-mobile-layout title="Daftar Anak" page-title="Daftar Anak">

    {{-- Closed notice --}}
    @unless($isRegistrationOpen)
        <div class="bg-[#FEF5DC] rounded-xl p-5 flex items-start gap-3 mb-5">
            <x-ich-icon name="clock" :size="24" color="#E09F17"/>
            <div>
                <p class="font-ui font-bold text-sm text-[#E09F17]">Pendaftaran Sedang Ditutup</p>
                <p class="font-sans text-xs text-ich-ink-600 mt-1">
                    Pendaftaran saat ini belum dibuka. Anda tidak dapat mengirim formulir pendaftaran.
                </p>
            </div>
        </div>
    @endunless

    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('pendaftaran.store') }}" class="space-y-4">
        @csrf

        <div class="bg-white rounded-xl shadow-ich-card p-5 space-y-4">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3">
                Data Orang Tua
            </h3>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Nama Ayah <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="nama_ayah"
                       value="{{ old('nama_ayah', $lastRegistration?->nama_ayah) }}"
                       placeholder="Masukkan nama ayah"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('nama_ayah') border-ich-error @else border-ich-line @enderror">
                @error('nama_ayah')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Nama Ibu <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="nama_ibu"
                       value="{{ old('nama_ibu', $lastRegistration?->nama_ibu) }}"
                       placeholder="Masukkan nama ibu"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('nama_ibu') border-ich-error @else border-ich-line @enderror">
                @error('nama_ibu')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Alamat <span class="text-ich-error">*</span>
                </label>
                <textarea name="alamat" rows="3"
                          placeholder="Masukkan alamat lengkap"
                          class="w-full px-3.5 py-2.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                 focus:outline-none focus:border-ich-teal resize-none
                                 @error('alamat') border-ich-error @else border-ich-line @enderror">{{ old('alamat', $lastRegistration?->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-ich-card p-5 space-y-4">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3">
                Data Anak
            </h3>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Nama Anak <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}"
                       placeholder="Nama lengkap anak"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('nama_siswa') border-ich-error @else border-ich-line @enderror">
                @error('nama_siswa')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Tempat Lahir <span class="text-ich-error">*</span>
                </label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                       placeholder="Kota tempat lahir anak"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('tempat_lahir') border-ich-error @else border-ich-line @enderror">
                @error('tempat_lahir')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">
                    Tanggal Lahir <span class="text-ich-error">*</span>
                </label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                       class="w-full h-11 px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                              focus:outline-none focus:border-ich-teal
                              @error('tanggal_lahir') border-ich-error @else border-ich-line @enderror">
                @error('tanggal_lahir')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
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
                @error('jenis_kelamin')
                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                @enderror
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
