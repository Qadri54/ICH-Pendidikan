<x-main-layout title="Absensi Siswa">

    <div class="mb-6">
        <h1 class="text-2xl font-display font-bold text-ich-ink-900">Absensi Siswa</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(! $classroom)
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <x-ich-icon name="school" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-ich-ink-600">Anda belum ditugaskan sebagai wali kelas.</p>
            <p class="font-sans text-sm text-ich-ink-400 mt-1">Hubungi admin untuk mengatur wali kelas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form Input Absensi --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                    <div class="px-5 py-4 border-b border-ich-line flex items-center justify-between">
                        <div>
                            <h2 class="font-ui font-bold text-ich-ink-900">{{ $classroom->nama_kelas }}</h2>
                            <p class="text-xs text-ich-ink-400 mt-0.5">{{ $students->count() }} siswa terdaftar</p>
                        </div>
                        <span class="px-3 py-1 bg-[#E8F5EA] text-ich-green font-ui font-bold text-xs rounded-full">
                            {{ $todayAbsences->count() }} tidak hadir
                        </span>
                    </div>

                    <form method="POST" action="{{ route('guru.absensi.store') }}">
                        @csrf
                        <div class="divide-y divide-ich-line">
                            @forelse($students as $i => $student)
                                @php
                                    $existing = $todayAbsences->get($student->student_id);
                                @endphp
                                <div class="px-5 py-3 flex items-center gap-4" x-data="{ checked: {{ $existing ? 'true' : 'false' }} }">
                                    <div class="flex-1">
                                        <p class="font-ui font-semibold text-sm text-ich-ink-900">{{ $student->nama_siswa }}</p>
                                        @if($existing)
                                            @php
                                                $statusCfg = match($existing->status) {
                                                    'izin'              => ['label' => 'Izin',              'bg' => 'bg-[#EDE9FE]', 'text' => 'text-[#8B5CF6]'],
                                                    'sakit'             => ['label' => 'Sakit',             'bg' => 'bg-[#FEE2E2]', 'text' => 'text-ich-error'],
                                                    'tanpa keterangan'  => ['label' => 'Tanpa Keterangan',  'bg' => 'bg-[#FEF5DC]', 'text' => 'text-[#E09F17]'],
                                                    default             => ['label' => $existing->status,   'bg' => 'bg-[#F5F6FA]', 'text' => 'text-ich-ink-400'],
                                                };
                                            @endphp
                                            <span class="text-xs font-ui font-bold px-2 py-0.5 rounded-full mt-0.5 inline-block
                                                         {{ $statusCfg['bg'] }} {{ $statusCfg['text'] }}">
                                                {{ $statusCfg['label'] }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($existing)
                                        {{-- Sudah terinput, tidak bisa diubah --}}
                                        <span class="text-xs text-ich-ink-300 font-sans italic">Sudah diinput</span>
                                    @else
                                        {{-- Belum ada record — bisa ditandai tidak hadir --}}
                                        <div class="flex items-center gap-3">
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox"
                                                       x-model="checked"
                                                       class="accent-ich-error w-4 h-4">
                                                <span class="font-ui text-xs text-ich-ink-600 font-semibold">Tidak Hadir</span>
                                            </label>

                                            <select x-show="checked"
                                                    name="absences[{{ $i }}][status]"
                                                    class="h-8 px-2 bg-white border-2 border-ich-line rounded-lg font-sans text-xs
                                                           focus:outline-none focus:border-ich-teal">
                                                <option value="tanpa keterangan">Tanpa Ket.</option>
                                                <option value="izin">Izin</option>
                                                <option value="sakit">Sakit</option>
                                            </select>

                                            <input x-show="checked" type="hidden"
                                                   name="absences[{{ $i }}][student_id]"
                                                   value="{{ $student->student_id }}">
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="px-5 py-10 text-center text-ich-ink-300 font-sans">
                                    Belum ada siswa di kelas ini.
                                </div>
                            @endforelse
                        </div>

                        @if($students->isNotEmpty())
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
            </div>

            {{-- Ringkasan Hari Ini --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-ich-card p-5">
                    <h3 class="font-ui font-bold text-ich-ink-900 mb-4">Ringkasan Hari Ini</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-sans text-sm text-ich-ink-600">Hadir</span>
                            <span class="font-ui font-bold text-ich-green">
                                {{ $students->count() - $todayAbsences->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-sans text-sm text-ich-ink-600">Izin</span>
                            <span class="font-ui font-bold text-[#8B5CF6]">
                                {{ $todayAbsences->where('status', 'izin')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-sans text-sm text-ich-ink-600">Sakit</span>
                            <span class="font-ui font-bold text-ich-error">
                                {{ $todayAbsences->where('status', 'sakit')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-sans text-sm text-ich-ink-600">Tanpa Ket.</span>
                            <span class="font-ui font-bold text-[#E09F17]">
                                {{ $todayAbsences->where('status', 'tanpa keterangan')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif

</x-main-layout>
