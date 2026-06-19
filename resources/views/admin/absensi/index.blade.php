@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Absensi Siswa">

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-ich-warning-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Absensi Siswa</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Lihat dan input absensi per kelas</p>
            </div>
        </div>
        <a href="{{ route('admin.absensi.recap') }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            Rekap Bulanan →
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.absensi.index') }}"
          class="bg-white rounded-xl shadow-ich-card p-5 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Kelas</label>
            <select name="class_id"
                    class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                           focus:outline-none focus:border-ich-teal">
                <option value="">-- Pilih Kelas --</option>
                @foreach($classes as $c)
                    <option value="{{ $c->class_id }}" {{ $selectedClass == $c->class_id ? 'selected' : '' }}>
                        {{ $c->nama_kelas }}
                        @if($c->homeroomTeacher) ({{ $c->homeroomTeacher->user?->name }}) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Tanggal</label>
            <input type="date" name="tanggal" value="{{ $selectedDate }}"
                   class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                          focus:outline-none focus:border-ich-teal">
        </div>
        <button type="submit"
                class="h-10 px-5 bg-ich-green text-white font-ui font-bold text-sm
                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
            Tampilkan
        </button>
    </form>

    @if($classroom)
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-5 py-4 border-b border-ich-line flex items-center justify-between">
                <div>
                    <h2 class="font-ui font-bold text-ich-ink-900">
                        {{ $classroom->nama_kelas }}
                        <span class="text-ich-ink-400 font-normal text-sm">— {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</span>
                    </h2>
                    <p class="text-xs text-ich-ink-400 mt-0.5">{{ $absences->count() }} sudah diinput dari {{ $students->count() }} siswa</p>
                </div>
                @if($isToday)
                    <span class="px-3 py-1 bg-ich-success-soft text-ich-success font-ui font-bold text-xs rounded-full">Hari Ini</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.absensi.store') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classroom->class_id }}">

                {{-- Header kolom --}}
                <div class="px-5 py-2 bg-ich-surface flex items-center gap-4 text-xs font-ui font-bold text-ich-ink-500">
                    <div class="flex-1">Nama Siswa</div>
                    <div class="w-[280px] grid grid-cols-4 text-center">
                        <span>Hadir</span>
                        <span>Izin</span>
                        <span>Sakit</span>
                        <span>Tanpa Ket.</span>
                    </div>
                </div>

                <div class="divide-y divide-ich-line">
                    @forelse($students as $i => $student)
                        @php $existing = $absences->get($student->student_id); @endphp
                        <div class="px-5 py-3 flex items-center gap-4">
                            <div class="flex-1">
                                <p class="font-ui font-semibold text-sm text-ich-ink-900">{{ $student->nama_siswa }}</p>
                                @if($existing)
                                    @php
                                        $cfg = match($existing->status) {
                                            'hadir'            => ['label'=>'Hadir',            'bg'=>'bg-ich-success-soft','text'=>'text-ich-success'],
                                            'izin'             => ['label'=>'Izin',             'bg'=>'bg-ich-purple-soft','text'=>'text-ich-purple'],
                                            'sakit'            => ['label'=>'Sakit',            'bg'=>'bg-ich-error-soft','text'=>'text-ich-error'],
                                            'tanpa keterangan' => ['label'=>'Tanpa Keterangan', 'bg'=>'bg-ich-warning-soft','text'=>'text-ich-warning'],
                                            default            => ['label'=>$existing->status,  'bg'=>'bg-ich-surface','text'=>'text-ich-ink-400'],
                                        };
                                    @endphp
                                    <span class="text-xs font-ui font-bold px-2 py-0.5 rounded-full mt-0.5 inline-block
                                                 {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                        {{ $cfg['label'] }}
                                    </span>
                                @endif
                            </div>

                            @if($isToday && ! $existing && ! $isReadOnly)
                                <input type="hidden" name="absences[{{ $i }}][student_id]" value="{{ $student->student_id }}">
                                <div class="w-[280px] grid grid-cols-4 text-center">
                                    <label class="flex justify-center cursor-pointer">
                                        <input type="radio" name="absences[{{ $i }}][status]" value="hadir"
                                               class="accent-[#009966] w-4 h-4">
                                    </label>
                                    <label class="flex justify-center cursor-pointer">
                                        <input type="radio" name="absences[{{ $i }}][status]" value="izin"
                                               class="accent-[#8B5CF6] w-4 h-4">
                                    </label>
                                    <label class="flex justify-center cursor-pointer">
                                        <input type="radio" name="absences[{{ $i }}][status]" value="sakit"
                                               class="accent-[#EF4444] w-4 h-4">
                                    </label>
                                    <label class="flex justify-center cursor-pointer">
                                        <input type="radio" name="absences[{{ $i }}][status]" value="tanpa keterangan"
                                               checked class="accent-[#E09F17] w-4 h-4">
                                    </label>
                                </div>
                            @elseif(! $isToday && ! $existing)
                                <div class="w-[280px] text-center">
                                    <span class="text-xs text-ich-ink-300 font-sans italic">Tidak dapat diubah</span>
                                </div>
                            @elseif($existing)
                                <div class="w-[280px] text-center">
                                    <span class="text-xs text-ich-ink-300 font-sans italic">Sudah diinput</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-ich-ink-300 font-sans">Belum ada siswa.</div>
                    @endforelse
                </div>

                @if($isToday && $students->isNotEmpty() && ! $isReadOnly)
                    <div class="px-5 py-4 border-t border-ich-line">
                        <button type="submit"
                                class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                            Simpan Absensi
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @elseif($selectedClass)
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <p class="font-sans text-ich-ink-400">Kelas tidak ditemukan.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <p class="font-sans text-ich-ink-400">Pilih kelas dan tanggal untuk melihat data absensi.</p>
        </div>
    @endif

</x-main-layout>
