@php
    $defaultNarratives = [
        ['kategori' => 'intrakurikuler', 'judul' => 'Nilai Agama dan Budi Pekerti'],
        ['kategori' => 'intrakurikuler', 'judul' => 'Jati Diri'],
        ['kategori' => 'intrakurikuler', 'judul' => 'Dasar-dasar Literasi, Matematika, Sains, Teknologi, Rekayasa, dan Seni'],
        ['kategori' => 'kokurikuler',    'judul' => 'Proyek Penguatan Profil Pelajar Pancasila (P5)'],
    ];
@endphp

<x-main-layout title="Edit Raport">

    <div class="mb-5">
        <a href="{{ route('guru.raport.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-ich-ink-400 hover:text-ich-teal mb-3">
            <x-ich-icon name="arrow_left" :size="14" color="currentColor"/>
            Kembali ke Daftar Raport
        </a>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-xl font-display font-bold text-ich-ink-900">
                    Raport — {{ $raport->student->nama_siswa }}
                </h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">
                    {{ $raport->classRoom?->nama_kelas }} ·
                    {{ $raport->period->tahun_ajaran }} Semester {{ $raport->period->semester }}
                </p>
            </div>
            @php
                $stCfg = match($raport->status) {
                    'draft'     => ['label' => 'Draft',    'bg' => 'bg-[#F5F6FA]', 'text' => 'text-ich-ink-500'],
                    'submitted' => ['label' => 'Disubmit', 'bg' => 'bg-[#FEF5DC]', 'text' => 'text-[#E09F17]'],
                    'approved'  => ['label' => 'Disetujui','bg' => 'bg-[#D1FAE5]', 'text' => 'text-[#009966]'],
                    default     => ['label' => $raport->status, 'bg' => 'bg-[#F5F6FA]', 'text' => 'text-ich-ink-400'],
                };
            @endphp
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="px-3 py-1 rounded-full text-xs font-ui font-bold {{ $stCfg['bg'] }} {{ $stCfg['text'] }}">
                    {{ $stCfg['label'] }}
                </span>
                @if($raport->status === 'draft')
                    <form method="POST" action="{{ route('guru.raport.submit', $raport->report_card_id) }}"
                          onsubmit="return confirm('Submit raport ini untuk persetujuan admin?')">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 bg-[#E09F17] text-white font-ui font-bold text-xs
                                       rounded-lg hover:bg-[#c08a13] transition-colors">
                            Submit untuk Persetujuan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="{
            tab: (new URLSearchParams(window.location.search)).get('tab') || 'narasi'
         }"
         class="space-y-5">

        {{-- Tab Buttons --}}
        <div class="bg-white rounded-xl shadow-ich-card p-1.5 flex gap-1">
            @foreach([
                ['id' => 'narasi',   'label' => 'Narasi'],
                ['id' => 'checklist','label' => 'Checklist'],
                ['id' => 'fisik',    'label' => 'Fisik & Kesehatan'],
            ] as $t)
                <button @click="tab = '{{ $t['id'] }}'" type="button"
                        :class="tab === '{{ $t['id'] }}' ? 'bg-ich-green text-white' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                        class="flex-1 py-2 text-xs font-ui font-bold rounded-lg transition-colors">
                    {{ $t['label'] }}
                </button>
            @endforeach
        </div>

        {{-- Tab Narasi --}}
        <div x-show="tab === 'narasi'" x-cloak>
            <form method="POST"
                  action="{{ route('guru.raport.narrative', $raport->report_card_id) }}?tab=narasi">
                @csrf
                <div class="space-y-4">
                    @foreach($defaultNarratives as $i => $default)
                        @php $existing = $narratives->get($default['judul']); @endphp
                        <div class="bg-white rounded-xl shadow-ich-card p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-ui font-bold
                                             {{ $default['kategori'] === 'intrakurikuler' ? 'bg-[#E8F5EA] text-ich-green' : 'bg-[#F0F4FF] text-ich-teal' }}">
                                    {{ ucfirst($default['kategori']) }}
                                </span>
                                <h3 class="font-ui font-bold text-sm text-ich-ink-900">{{ $default['judul'] }}</h3>
                            </div>
                            <input type="hidden" name="narratives[{{ $i }}][judul]"    value="{{ $default['judul'] }}">
                            <input type="hidden" name="narratives[{{ $i }}][kategori]" value="{{ $default['kategori'] }}">
                            <textarea name="narratives[{{ $i }}][isi_naratif]" rows="4"
                                      placeholder="Tulis penilaian narasi di sini..."
                                      class="w-full px-3 py-2.5 bg-[#F9FAFB] border-2 border-ich-line rounded-ich-lg
                                             font-sans text-sm focus:outline-none focus:border-ich-teal resize-none">{{ $existing?->isi_naratif }}</textarea>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5">
                    <button type="submit"
                            class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                   rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        Simpan Narasi
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Checklist --}}
        <div x-show="tab === 'checklist'" x-cloak>
            <form method="POST"
                  action="{{ route('guru.raport.checklist', $raport->report_card_id) }}?tab=checklist">
                @csrf
                @php $idx = 0; @endphp
                @forelse($categories as $category)
                    <div class="bg-white rounded-xl shadow-ich-card mb-4 overflow-hidden">
                        <div class="px-5 py-3 bg-[#F5F6FA] border-b border-ich-line">
                            <h3 class="font-ui font-bold text-sm text-ich-ink-900">{{ $category->nama }}</h3>
                        </div>
                        <div class="divide-y divide-ich-line">
                            @foreach($category->children as $child)
                                @php $existing = $checklists->get($child->category_id); @endphp
                                <div class="px-5 py-4">
                                    <div class="flex items-start gap-4">
                                        <p class="flex-1 font-sans text-sm text-ich-ink-800">{{ $child->nama }}</p>
                                        <div class="flex gap-2 flex-shrink-0">
                                            @foreach(['BM' => ['bg-[#FEE2E2]','text-ich-error','border-ich-error'], 'MM' => ['bg-[#FEF5DC]','text-[#E09F17]','border-[#E09F17]'], 'SM' => ['bg-[#D1FAE5]','text-[#009966]','border-[#009966]']] as $val => $cfg)
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                           name="checklists[{{ $idx }}][status]"
                                                           value="{{ $val }}"
                                                           {{ ($existing?->status === $val) ? 'checked' : '' }}
                                                           class="sr-only peer">
                                                    <span class="px-2.5 py-1 text-xs font-ui font-bold rounded-lg border-2 border-ich-line
                                                                 peer-checked:{{ $cfg[0] }} peer-checked:{{ $cfg[1] }} peer-checked:{{ $cfg[2] }}
                                                                 hover:border-ich-ink-300 transition-colors cursor-pointer block">
                                                        {{ $val }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" name="checklists[{{ $idx }}][category_id]" value="{{ $child->category_id }}">
                                    <input type="hidden" name="checklists[{{ $idx }}][catatan]"     value="">
                                </div>
                                @php $idx++; @endphp
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-ich-card p-10 text-center">
                        <p class="font-sans text-ich-ink-400">Belum ada kategori checklist.</p>
                    </div>
                @endforelse
                @if($categories->isNotEmpty())
                    <div class="mt-2">
                        <button type="submit"
                                class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                       rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                            Simpan Checklist
                        </button>
                        <p class="text-xs text-ich-ink-400 font-sans mt-2">BM = Belum Muncul · MM = Mulai Muncul · SM = Sudah Muncul</p>
                    </div>
                @endif
            </form>
        </div>

        {{-- Tab Fisik & Kesehatan --}}
        <div x-show="tab === 'fisik'" x-cloak>
            <form method="POST"
                  action="{{ route('guru.raport.physical', $raport->report_card_id) }}?tab=fisik">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="bg-white rounded-xl shadow-ich-card p-5">
                        <h3 class="font-ui font-bold text-ich-ink-900 mb-4">Pengukuran Fisik</h3>
                        <div class="space-y-4">
                            @foreach([['tinggi_badan','Tinggi Badan (cm)','105.5'], ['berat_badan','Berat Badan (kg)','18.5'], ['lingkar_kepala','Lingkar Kepala (cm)','50.0']] as [$field, $label, $ph])
                                <div>
                                    <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">{{ $label }}</label>
                                    <input type="number" name="{{ $field }}" step="0.1"
                                           value="{{ old($field, $physical?->$field) }}"
                                           placeholder="{{ $ph }}"
                                           class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                                  font-sans text-sm focus:outline-none focus:border-ich-teal">
                                </div>
                            @endforeach
                            <div>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Tanggal Pengukuran</label>
                                <input type="date" name="tanggal_ukur"
                                       value="{{ old('tanggal_ukur', $physical?->tanggal_ukur) }}"
                                       class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                              font-sans text-sm focus:outline-none focus:border-ich-teal">
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-ich-card p-5">
                        <h3 class="font-ui font-bold text-ich-ink-900 mb-4">Kondisi Kesehatan</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Pendengaran</label>
                                <input type="text" name="pendengaran" required
                                       value="{{ old('pendengaran', $health?->pendengaran) }}"
                                       placeholder="misal: Normal"
                                       class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                              font-sans text-sm focus:outline-none focus:border-ich-teal">
                            </div>
                            <div>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Penglihatan</label>
                                <input type="text" name="penglihatan" required
                                       value="{{ old('penglihatan', $health?->penglihatan) }}"
                                       placeholder="misal: Normal"
                                       class="w-full h-10 px-3 bg-white border-2 border-ich-line rounded-ich-lg
                                              font-sans text-sm focus:outline-none focus:border-ich-teal">
                            </div>
                            <div>
                                <label class="block font-ui font-bold text-xs text-ich-ink-600 mb-1.5">Catatan Tambahan</label>
                                <textarea name="catatan_tambahan" rows="3"
                                          placeholder="Catatan kondisi kesehatan lainnya (opsional)"
                                          class="w-full px-3 py-2.5 bg-white border-2 border-ich-line rounded-ich-lg
                                                 font-sans text-sm focus:outline-none focus:border-ich-teal resize-none">{{ old('catatan_tambahan', $health?->catatan_tambahan) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <button type="submit"
                            class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                   rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                        Simpan Data Fisik & Kesehatan
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-main-layout>
