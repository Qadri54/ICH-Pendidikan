<x-main-layout title="Tambah Siswa">

    <div class="mb-6">
        <a href="{{ route('admin.siswa.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Tambah Siswa Baru</h1>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Siswa <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal-dark
                                  @error('nama_siswa') border-ich-error @else border-ich-teal @enderror">
                    @error('nama_siswa') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIS</label>
                    <input type="text" name="NIS" value="{{ old('NIS') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                  focus:outline-none focus:border-ich-teal-dark
                                  @error('NIS') border-ich-error @else border-ich-teal @enderror">
                    @error('NIS') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Kelas <span class="text-ich-error">*</span></label>
                    <select name="class_id"
                            class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->class_id }}" {{ old('class_id') == $k->class_id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Jenis Kelamin <span class="text-ich-error">*</span></label>
                    <select name="jenis_kelamin"
                            class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Lahir <span class="text-ich-error">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @error('tanggal_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tempat Lahir <span class="text-ich-error">*</span></label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                    @error('tempat_lahir') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ayah <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                    @error('nama_ayah') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Ibu <span class="text-ich-error">*</span></label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none focus:border-ich-teal-dark">
                    @error('nama_ibu') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Akun Orang Tua (opsional)</label>
                <select name="user_id"
                        class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Tidak ditautkan --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->user_id }}" {{ old('user_id') == $p->user_id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-ich-ink-400 text-xs mt-1">Tautkan siswa ke akun orang tua yang sudah terdaftar.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.siswa.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
