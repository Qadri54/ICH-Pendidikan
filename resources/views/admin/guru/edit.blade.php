<x-main-layout title="Edit Guru">

    <div class="mb-6">
        <a href="{{ route('admin.guru.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">Edit Guru — {{ $guru->user?->name }}</h1>
    </div>

    <div class="max-w-xl bg-white rounded-xl shadow-ich-card p-6">
        <form method="POST" action="{{ route('admin.guru.update', $guru->teacher_id ?? $guru->religious_teacher_id) }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="px-3 py-2 bg-[#F4F7FC] rounded-ich-md text-sm text-ich-teal font-ui font-bold">
                Tipe: {{ $tipe }}
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $guru->user?->name) }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">No HP</label>
                    <input type="tel" name="no_hp" value="{{ old('no_hp', $guru->user?->no_hp) }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">NIP</label>
                    <input type="text" name="NIP" value="{{ old('NIP', $guru->NIP) }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Bergabung</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $guru->hire_date) }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            </div>

            @if($tipe === 'Guru')
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Mata Pelajaran</label>
                    <input type="text" name="subject" value="{{ old('subject', $guru->subject ?? '') }}"
                           class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                </div>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                               rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                    Perbarui
                </button>
                <a href="{{ route('admin.guru.index') }}"
                   class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                          rounded-ich-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

</x-main-layout>
