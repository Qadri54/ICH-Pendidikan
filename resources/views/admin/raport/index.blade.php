<x-main-layout title="Sistem Raport">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Sistem Raport</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Kelola raport siswa per periode akademik</p>
        </div>
        <a href="{{ route('admin.raport.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                  rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
            <x-ich-icon name="plus" :size="16" color="white"/>
            Buat Raport
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="mb-4 px-4 py-3 bg-[#FEE2E2] text-ich-error rounded-lg text-sm font-semibold">
            {{ $errors->first('error') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.raport.index') }}"
          class="bg-white rounded-xl shadow-ich-card p-5 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Periode</label>
            <select name="period_id"
                    class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                           font-sans text-sm focus:outline-none focus:border-ich-teal">
                <option value="">Semua Periode</option>
                @foreach($periods as $period)
                    <option value="{{ $period->period_id }}"
                            {{ ($filters['period_id'] ?? '') == $period->period_id ? 'selected' : '' }}>
                        {{ $period->tahun_ajaran }} — Semester {{ $period->semester }}
                        @if($period->is_active) (Aktif) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Kelas</label>
            <select name="class_id"
                    class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                           font-sans text-sm focus:outline-none focus:border-ich-teal">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class->class_id }}"
                            {{ ($filters['class_id'] ?? '') == $class->class_id ? 'selected' : '' }}>
                        {{ $class->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Status</label>
            <select name="status"
                    class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                           font-sans text-sm focus:outline-none focus:border-ich-teal">
                <option value="">Semua Status</option>
                <option value="draft"     {{ ($filters['status'] ?? '') === 'draft'     ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ ($filters['status'] ?? '') === 'submitted' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved"  {{ ($filters['status'] ?? '') === 'approved'  ? 'selected' : '' }}>Disetujui</option>
            </select>
        </div>
        <button type="submit"
                class="h-10 px-5 bg-ich-green text-white font-ui font-bold text-sm
                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
            Tampilkan
        </button>
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="px-5 py-4 border-b border-ich-line">
            <p class="font-sans text-sm text-ich-ink-400">{{ $raports->count() }} raport ditemukan</p>
        </div>

        @if($raports->isEmpty())
            <div class="px-5 py-12 text-center">
                <x-ich-icon name="document" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
                <p class="font-sans text-ich-ink-400">Belum ada raport.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#F5F6FA]">
                        <tr>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Siswa</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Kelas</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Periode</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Status</th>
                            <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ich-line">
                        @foreach($raports as $raport)
                            @php
                                $stCfg = match($raport->status) {
                                    'draft'     => ['label' => 'Draft',      'bg' => 'bg-[#F5F6FA]',  'text' => 'text-ich-ink-500'],
                                    'submitted' => ['label' => 'Disubmit',   'bg' => 'bg-[#FEF5DC]',  'text' => 'text-[#E09F17]'],
                                    'approved'  => ['label' => 'Disetujui',  'bg' => 'bg-[#D1FAE5]',  'text' => 'text-[#009966]'],
                                    default     => ['label' => $raport->status, 'bg' => 'bg-[#F5F6FA]', 'text' => 'text-ich-ink-400'],
                                };
                            @endphp
                            <tr class="hover:bg-[#F9FAFB]">
                                <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                    {{ $raport->student->nama_siswa }}
                                </td>
                                <td class="px-4 py-3 font-sans text-ich-ink-600">
                                    {{ $raport->classRoom?->nama_kelas ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-sans text-ich-ink-600">
                                    {{ $raport->period->tahun_ajaran }} / Sem {{ $raport->period->semester }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold
                                                 {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                                        {{ $stCfg['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.raport.edit', $raport->report_card_id) }}"
                                           class="text-xs font-ui font-bold text-ich-teal hover:underline">
                                            Edit
                                        </a>
                                        @if($raport->status === 'submitted')
                                            <form method="POST"
                                                  action="{{ route('admin.raport.approve', $raport->report_card_id) }}"
                                                  onsubmit="return confirm('Setujui raport ini?')">
                                                @csrf
                                                <button type="submit"
                                                        class="text-xs font-ui font-bold text-[#009966] hover:underline">
                                                    Setujui
                                                </button>
                                            </form>
                                        @endif
                                        @if($raport->status === 'draft')
                                            <form method="POST"
                                                  action="{{ route('admin.raport.submit', $raport->report_card_id) }}"
                                                  onsubmit="return confirm('Submit raport ini?')">
                                                @csrf
                                                <button type="submit"
                                                        class="text-xs font-ui font-bold text-[#E09F17] hover:underline">
                                                    Submit
                                                </button>
                                            </form>
                                            <form method="POST"
                                                  action="{{ route('admin.raport.destroy', $raport->report_card_id) }}"
                                                  onsubmit="return confirm('Hapus raport ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs font-ui font-bold text-ich-error hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-main-layout>
