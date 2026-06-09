<x-main-layout title="Raport Siswa">

<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
}">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Raport Siswa</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">
                @if($classroom)
                    Kelas {{ $classroom->nama_kelas }}
                @else
                    Anda belum ditugaskan sebagai wali kelas
                @endif
            </p>
        </div>
        @if($classroom)
            <button @click="showCreate = true"
                    class="flex items-center gap-2 px-4 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                <x-ich-icon name="plus" :size="16" color="white"/>
                Buat Raport
            </button>
        @endif
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

    @if(! $classroom)
        <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
            <x-ich-icon name="school" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-ich-ink-600">Anda belum ditugaskan sebagai wali kelas.</p>
            <p class="font-sans text-sm text-ich-ink-400 mt-1">Hubungi admin untuk mengatur wali kelas.</p>
        </div>
    @else

        {{-- Filter --}}
        <form method="GET" action="{{ route('guru.raport.index') }}"
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

        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="px-5 py-4 border-b border-ich-line">
                <p class="font-sans text-sm text-ich-ink-400">{{ $raports->count() }} raport ditemukan</p>
            </div>

            @if($raports->isEmpty())
                <div class="px-5 py-12 text-center">
                    <x-ich-icon name="document" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
                    <p class="font-sans text-ich-ink-400">Belum ada raport untuk kelas ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[#F5F6FA]">
                            <tr>
                                <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Siswa</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Periode</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Status</th>
                                <th class="px-4 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ich-line">
                            @foreach($raports as $raport)
                                @php
                                    $stCfg = match($raport->status) {
                                        'draft'     => ['label' => 'Draft',    'bg' => 'bg-[#F5F6FA]', 'text' => 'text-ich-ink-500'],
                                        'submitted' => ['label' => 'Disubmit', 'bg' => 'bg-[#FEF5DC]', 'text' => 'text-[#E09F17]'],
                                        'approved'  => ['label' => 'Disetujui','bg' => 'bg-[#D1FAE5]', 'text' => 'text-[#009966]'],
                                        default     => ['label' => $raport->status, 'bg' => 'bg-[#F5F6FA]', 'text' => 'text-ich-ink-400'],
                                    };
                                @endphp
                                <tr class="hover:bg-[#F5F6FA] transition-colors">
                                    <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                        {{ $raport->student->nama_siswa }}
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
                                        <div class="flex items-center gap-3">
                                            @if($raport->status !== 'approved')
                                                <a href="{{ route('guru.raport.edit', $raport->report_card_id) }}"
                                                   class="text-xs font-ui font-bold text-ich-teal hover:underline">
                                                    Edit
                                                </a>
                                            @endif
                                            @if($raport->status === 'draft')
                                                <form method="POST"
                                                      action="{{ route('guru.raport.submit', $raport->report_card_id) }}"
                                                      onsubmit="return confirm('Submit raport ini untuk persetujuan admin?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="text-xs font-ui font-bold text-[#E09F17] hover:underline">
                                                        Submit
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
    @endif

    {{-- Modal Create --}}
    @if($classroom)
    <x-admin-modal show="showCreate" title="Buat Raport Baru" maxWidth="md">
        <form method="POST" action="{{ route('guru.raport.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div class="px-3 py-2 bg-[#F4F7FC] rounded-ich-md text-sm text-ich-teal font-ui font-semibold">
                Kelas {{ $classroom->nama_kelas }}
            </div>

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Siswa <span class="text-ich-error">*</span></label>
                <select name="student_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->student_id }}" {{ old('student_id') == $s->student_id ? 'selected' : '' }}>
                            {{ $s->nama_siswa }}
                        </option>
                    @endforeach
                </select>
                @error('student_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Periode Akademik <span class="text-ich-error">*</span></label>
                <select name="period_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    @foreach($periods as $period)
                        <option value="{{ $period->period_id }}"
                                {{ old('period_id', $active?->period_id) == $period->period_id ? 'selected' : '' }}>
                            {{ $period->tahun_ajaran }} — Semester {{ $period->semester }}
                            @if($period->is_active) (Aktif) @endif
                        </option>
                    @endforeach
                </select>
                @error('period_id') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">Buat Raport</button>
                <button type="button" @click="showCreate = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>
    @endif

</div>
</x-main-layout>
