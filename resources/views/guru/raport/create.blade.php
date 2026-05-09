<x-main-layout title="Buat Raport Baru">

    <div class="mb-6">
        <a href="{{ route('guru.raport.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-ich-ink-400 hover:text-ich-teal mb-3">
            <x-ich-icon name="arrow_left" :size="14" color="currentColor"/>
            Kembali ke Daftar Raport
        </a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Buat Raport Baru</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">Kelas {{ $classroom->nama_kelas }}</p>
    </div>

    @if($errors->has('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ $errors->first('error') }}
        </div>
    @endif

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow-ich-card p-6">
            <form method="POST" action="{{ route('guru.raport.store') }}">
                @csrf
                <div class="mb-5">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Siswa</label>
                    <select name="student_id" required
                            class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                   font-sans text-sm focus:outline-none focus:border-ich-teal
                                   @error('student_id') border-ich-error @enderror">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->student_id }}"
                                    {{ old('student_id') == $student->student_id ? 'selected' : '' }}>
                                {{ $student->nama_siswa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Periode Akademik</label>
                    <select name="period_id" required
                            class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                   font-sans text-sm focus:outline-none focus:border-ich-teal">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->period_id }}"
                                    {{ (old('period_id') ?? $active?->period_id) == $period->period_id ? 'selected' : '' }}>
                                {{ $period->tahun_ajaran }} — Semester {{ $period->semester }}
                                @if($period->is_active) (Aktif) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('guru.raport.index') }}"
                       class="flex-1 py-2.5 border-2 border-ich-line text-ich-ink-600 font-ui font-bold text-sm
                              rounded-ich-lg text-center hover:bg-[#F5F6FA] transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                   rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        Buat Raport
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-main-layout>
