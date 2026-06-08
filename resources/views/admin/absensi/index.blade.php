@php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp
<x-main-layout title="Absensi Siswa">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Absensi Siswa</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Lihat dan input absensi per kelas</p>
        </div>
        <a href="{{ route('admin.absensi.recap') }}"
           class="text-sm font-ui font-bold text-ich-teal hover:underline">
            Rekap Bulanan →
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
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
                    <p class="text-xs text-ich-ink-400 mt-0.5">{{ $absences->count() }} tidak hadir dari {{ $students->count() }} siswa</p>
                </div>
                @if($isToday)
                    <span class="px-3 py-1 bg-[#D1FAE5] text-[#009966] font-ui font-bold text-xs rounded-full">Hari Ini</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.absensi.store') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classroom->class_id }}">

                <div class="divide-y divide-ich-line">
                    @forelse($students as $i => $student)
                        @php $existing = $absences->get($student->student_id); @endphp
                        <div class="px-5 py-3 flex items-center gap-4"
                             x-data="{ checked: {{ $existing ? 'true' : 'false' }} }">
                            <div class="flex-1">
                                <p class="font-ui font-semibold text-sm text-ich-ink-900">{{ $student->nama_siswa }}</p>
                                @if($existing)
                                    @php
                                        $cfg = match($existing->status) {
                                            'izin'             => ['label'=>'Izin',             'bg'=>'bg-[#EDE9FE]','text'=>'text-[#8B5CF6]'],
                                            'sakit'            => ['label'=>'Sakit',            'bg'=>'bg-[#FEE2E2]','text'=>'text-ich-error'],
                                            'tanpa keterangan' => ['label'=>'Tanpa Keterangan', 'bg'=>'bg-[#FEF5DC]','text'=>'text-[#E09F17]'],
                                            default            => ['label'=>$existing->status,  'bg'=>'bg-[#F5F6FA]','text'=>'text-ich-ink-400'],
                                        };
                                    @endphp
                                    <span class="text-xs font-ui font-bold px-2 py-0.5 rounded-full mt-0.5 inline-block
                                                 {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                                        {{ $cfg['label'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-ich-green font-ui">Hadir</span>
                                @endif
                            </div>

                            @if($isToday && ! $existing && ! $isReadOnly)
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" x-model="checked" class="accent-ich-error w-4 h-4">
                                        <span class="font-ui text-xs text-ich-ink-600 font-semibold">Tidak Hadir</span>
                                    </label>
                                    <select x-show="checked" name="absences[{{ $i }}][status]"
                                            class="h-8 px-2 bg-white border-2 border-ich-line rounded-lg
                                                   font-sans text-xs focus:outline-none focus:border-ich-teal">
                                        <option value="tanpa keterangan">Tanpa Ket.</option>
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                    </select>
                                    <input x-show="checked" type="hidden"
                                           name="absences[{{ $i }}][student_id]"
                                           value="{{ $student->student_id }}">
                                </div>
                            @elseif(! $isToday && ! $existing)
                                <span class="text-xs text-ich-ink-300 font-sans italic">Tidak dapat diubah</span>
                            @elseif($existing)
                                <span class="text-xs text-ich-ink-300 font-sans italic">Sudah diinput</span>
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
                        <p class="text-xs text-ich-ink-400 font-sans mt-2">
                            Siswa yang tidak dicentang dianggap <strong>Hadir</strong>.
                        </p>
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
