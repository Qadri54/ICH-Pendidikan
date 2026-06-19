<x-main-layout title="Sistem Raport">

    @php $isReadOnly = in_array(auth()->user()->role?->role_name, ['Kepala Sekolah', 'Kepala Yayasan']); @endphp

<div x-data="{
    showCreate: {{ $errors->any() && old('_modal') === 'create' ? 'true' : 'false' }},
    showDelete: false,
    deleteId: null,
    deleteName: '',
    openDelete(id, name) {
        this.deleteId = id;
        this.deleteName = name;
        this.showDelete = true;
    }
}">

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-ich-purple-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Sistem Raport</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Kelola raport siswa per periode akademik</p>
            </div>
        </div>
        @if(! $isReadOnly)
            <button @click="showCreate = true"
                    class="flex items-center gap-2 px-4 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                <x-ich-icon name="plus" :size="16" color="white"/>
                Buat Raport
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
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
                @foreach($classes as $kelas)
                    <option value="{{ $kelas->class_id }}"
                            {{ ($filters['class_id'] ?? '') == $kelas->class_id ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
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
                    <thead class="bg-ich-surface">
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
                                    'draft'     => ['label' => 'Draft',      'bg' => 'bg-ich-surface',  'text' => 'text-ich-ink-500'],
                                    'submitted' => ['label' => 'Disubmit',   'bg' => 'bg-ich-warning-soft',  'text' => 'text-ich-warning'],
                                    'approved'  => ['label' => 'Disetujui',  'bg' => 'bg-ich-success-soft',  'text' => 'text-ich-success'],
                                    default     => ['label' => $raport->status, 'bg' => 'bg-ich-surface', 'text' => 'text-ich-ink-400'],
                                };
                            @endphp
                            <tr class="hover:bg-ich-surface transition-colors">
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
                                            {{ $isReadOnly ? 'Lihat' : 'Edit' }}
                                        </a>
                                        @if(! $isReadOnly)
                                            @if($raport->status === 'submitted')
                                                <button type="button"
                                                        @click="$dispatch('open-confirm', {
                                                            title: 'Setujui Raport',
                                                            message: 'Setujui raport {{ $raport->student->nama_siswa }}?',
                                                            action: '{{ route('admin.raport.approve', $raport->report_card_id) }}',
                                                            btnText: 'Setujui'
                                                        })"
                                                        class="text-xs font-ui font-bold text-ich-success hover:underline">
                                                    Setujui
                                                </button>
                                            @endif
                                            @if($raport->status === 'draft')
                                                <button type="button"
                                                        @click="$dispatch('open-confirm', {
                                                            title: 'Submit Raport',
                                                            message: 'Submit raport {{ $raport->student->nama_siswa }} untuk disetujui?',
                                                            action: '{{ route('admin.raport.submit', $raport->report_card_id) }}',
                                                            btnText: 'Submit'
                                                        })"
                                                        class="text-xs font-ui font-bold text-ich-warning hover:underline">
                                                    Submit
                                                </button>
                                                <button @click="openDelete('{{ $raport->report_card_id }}', '{{ $raport->student->nama_siswa }}')"
                                                        class="text-xs font-ui font-bold text-ich-error hover:underline">
                                                    Hapus
                                                </button>
                                            @endif
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

    {{-- Modal Create --}}
    <x-admin-modal show="showCreate" title="Buat Raport Baru" maxWidth="md">
        <form method="POST" action="{{ route('admin.raport.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create">

            <div>
                <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Siswa <span class="text-ich-error">*</span></label>
                <select name="student_id" class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $s)
                        <option value="{{ $s->student_id }}" {{ old('student_id') == $s->student_id ? 'selected' : '' }}>
                            {{ $s->nama_siswa }} — {{ $s->classRoom?->nama_kelas ?? 'Tanpa kelas' }}
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

    {{-- Modal Delete --}}
    <x-admin-modal show="showDelete" title="Konfirmasi Hapus" maxWidth="sm">
        <p class="text-sm text-ich-ink-600 mb-4">Yakin ingin menghapus raport untuk <strong x-text="deleteName"></strong>?</p>
        <form method="POST" :action="'{{ route('admin.raport.destroy', ':id') }}'.replace(':id', deleteId)">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm rounded-ich-lg hover:opacity-90 transition-opacity">Hapus</button>
                <button type="button" @click="showDelete = false" class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">Batal</button>
            </div>
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
