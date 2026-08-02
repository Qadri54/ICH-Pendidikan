<x-main-layout title="Daftarkan Siswa Baru">

    <div class="mb-6">
        <a href="{{ route('admin.pendaftaran.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Daftarkan Siswa Baru</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Pendaftaran siswa secara langsung oleh admin</p>
    </div>

    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.pendaftaran.store') }}" class="space-y-6 max-w-3xl"
          x-data="{ jenis: '{{ old('jenis_pendaftaran', 'TK') }}' }">
        @csrf

        {{-- Jenis Pendaftaran --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Jenis Pendaftaran</h3>
            <div class="flex gap-3">
                <label class="flex-1 flex items-center gap-2.5 h-11 px-3.5 border-2 rounded-ich-lg cursor-pointer transition-colors"
                       :class="jenis === 'TK' ? 'border-ich-teal bg-[#F0FDFA]' : 'border-ich-line bg-white'">
                    <input type="radio" name="jenis_pendaftaran" value="TK" x-model="jenis" class="accent-ich-teal">
                    <span class="font-ui font-semibold text-sm text-ich-ink-900">PG / TK ICH</span>
                </label>
                <label class="flex-1 flex items-center gap-2.5 h-11 px-3.5 border-2 rounded-ich-lg cursor-pointer transition-colors"
                       :class="jenis === 'Mengaji' ? 'border-ich-teal bg-[#F0FDFA]' : 'border-ich-line bg-white'">
                    <input type="radio" name="jenis_pendaftaran" value="Mengaji" x-model="jenis" class="accent-ich-teal">
                    <span class="font-ui font-semibold text-sm text-ich-ink-900">Mengaji</span>
                </label>
            </div>
            @error('jenis_pendaftaran') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Akun Orang Tua --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Akun Orang Tua</h3>
            <p class="text-xs text-ich-ink-400 mb-3">Akun orang tua akan dibuat otomatis dengan email ini. Password akan ditampilkan setelah pendaftaran berhasil.</p>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Email Orang Tua <span class="text-ich-error">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                @error('email') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Biodata Siswa --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Siswa</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Lengkap Siswa <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('nama_siswa') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tempat Lahir <span class="text-ich-error">*</span></label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('tempat_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Lahir <span class="text-ich-error">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('tanggal_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jenis Kelamin <span class="text-ich-error">*</span></label>
                    <select name="jenis_kelamin"
                            class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Anak ke <span class="text-ich-error">*</span></label>
                    <input type="number" name="anak_ke" value="{{ old('anak_ke') }}" min="1" max="20"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('anak_ke') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Alamat <span class="text-ich-error">*</span></label>
                    <textarea name="alamat" rows="2"
                              class="w-full px-3.5 py-2.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="jenis === 'TK'" x-cloak>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Ukuran Baju</label>
                    <select name="ukuran_baju"
                            class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                        <option value="">-- Pilih --</option>
                        <option value="S" {{ old('ukuran_baju') === 'S' ? 'selected' : '' }}>S</option>
                        <option value="M" {{ old('ukuran_baju') === 'M' ? 'selected' : '' }}>M</option>
                        <option value="L" {{ old('ukuran_baju') === 'L' ? 'selected' : '' }}>L</option>
                    </select>
                    @error('ukuran_baju') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Biodata Ayah --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Ayah</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ayah <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('nama_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tempat Lahir <span class="text-ich-error">*</span></label>
                    <input type="text" name="tempat_lahir_ayah" value="{{ old('tempat_lahir_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('tempat_lahir_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Lahir <span class="text-ich-error">*</span></label>
                    <input type="date" name="tanggal_lahir_ayah" value="{{ old('tanggal_lahir_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('tanggal_lahir_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Alamat <span class="text-ich-error">*</span></label>
                    <textarea name="alamat_ayah" rows="2"
                              class="w-full px-3.5 py-2.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">{{ old('alamat_ayah') }}</textarea>
                    @error('alamat_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Pendidikan <span class="text-ich-error">*</span></label>
                    <input type="text" name="pendidikan_ayah" value="{{ old('pendidikan_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('pendidikan_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Pekerjaan <span class="text-ich-error">*</span></label>
                    <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('pekerjaan_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No. Telp <span class="text-ich-error">*</span></label>
                    <input type="text" name="no_telp_ayah" value="{{ old('no_telp_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('no_telp_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Biodata Ibu --}}
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <h3 class="font-ui font-bold text-sm text-ich-ink-900 border-b border-ich-line pb-3 mb-4">Biodata Ibu</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ibu <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('nama_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tempat Lahir <span class="text-ich-error">*</span></label>
                    <input type="text" name="tempat_lahir_ibu" value="{{ old('tempat_lahir_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('tempat_lahir_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Lahir <span class="text-ich-error">*</span></label>
                    <input type="date" name="tanggal_lahir_ibu" value="{{ old('tanggal_lahir_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('tanggal_lahir_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Alamat <span class="text-ich-error">*</span></label>
                    <textarea name="alamat_ibu" rows="2"
                              class="w-full px-3.5 py-2.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">{{ old('alamat_ibu') }}</textarea>
                    @error('alamat_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Pendidikan <span class="text-ich-error">*</span></label>
                    <input type="text" name="pendidikan_ibu" value="{{ old('pendidikan_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('pendidikan_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Pekerjaan <span class="text-ich-error">*</span></label>
                    <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('pekerjaan_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No. Telp <span class="text-ich-error">*</span></label>
                    <input type="text" name="no_telp_ibu" value="{{ old('no_telp_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal">
                    @error('no_telp_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                Daftarkan Siswa
            </button>
            <a href="{{ route('admin.pendaftaran.index') }}"
               class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>

</x-main-layout>
