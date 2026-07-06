<x-main-layout title="Kelola Tabungan">
<div x-data="{ showCreate: false }">

    <div class="mb-6">
        <a href="{{ route('guru.tabungan.index') }}"
           class="text-ich-teal text-sm font-ui font-semibold hover:underline">← Kembali</a>
        <h1 class="text-2xl font-display font-bold text-ich-ink-900 mt-1">{{ $ledger->ledger_name }}</h1>
        <p class="text-sm text-ich-ink-400 mt-0.5">
            {{ $ledger->academicPeriod ? $ledger->academicPeriod->tahun_ajaran . ' — Smt ' . $ledger->academicPeriod->semester : '-' }}
            · Total Saldo: <span class="font-semibold text-ich-green">Rp {{ number_format($ledger->total_balance, 0, ',', '.') }}</span>
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
        <div class="px-5 py-4 border-b border-ich-line flex items-center justify-between">
            <h2 class="font-ui font-bold text-ich-ink-900">Daftar Tabungan Siswa</h2>
            @if($ledger->status === 'Active')
                <button @click="showCreate = true"
                        class="flex items-center gap-1.5 px-4 py-2 bg-ich-green text-white
                               font-ui font-bold text-xs rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark">
                    <x-ich-icon name="plus" :size="14" color="white"/>
                    Buka Buku Tabungan
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Nama Siswa</th>
                        <th class="px-4 py-3 text-left font-ui font-bold text-ich-ink-600">Kelas</th>
                        <th class="px-4 py-3 text-right font-ui font-bold text-ich-ink-600">Saldo Terkini</th>
                        <th class="px-4 py-3 text-center font-ui font-bold text-ich-ink-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($passbooks as $pb)
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-4 py-3 font-ui font-semibold text-ich-ink-900">
                                {{ $pb->student?->nama_siswa ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($pb->student?->classRoom)
                                    <span class="px-2 py-1 bg-ich-green-surface text-ich-green font-ui font-bold text-xs rounded-full">
                                        {{ $pb->student->classRoom->nama_kelas }}
                                    </span>
                                @else
                                    <span class="text-ich-ink-300 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-ui font-semibold text-ich-green">
                                Rp {{ number_format($pb->current_balance, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('guru.tabungan.passbook.show', $pb) }}"
                                   class="text-xs font-ui font-bold text-ich-teal hover:underline">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-ich-ink-300 font-sans">
                                Belum ada buku tabungan siswa di ledger ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Buka Buku Tabungan --}}
    <x-admin-modal show="showCreate" title="Buka Buku Tabungan" maxWidth="lg">
        <form method="POST" action="{{ route('guru.tabungan.passbook.store', $ledger) }}" class="space-y-4"
              x-data="{
                  selectedClass: '{{ $homeroomClassId ?? '' }}',
                  selected: [],
                  allStudents: {{ Js::from($availableStudents->map(fn($s) => [
                      'id' => $s->student_id,
                      'nama' => $s->nama_siswa,
                      'class_id' => $s->class_id,
                      'kelas' => $s->classRoom?->nama_kelas ?? '-',
                  ])) }},
                  get filtered() {
                      if (!this.selectedClass) return this.allStudents;
                      return this.allStudents.filter(s => s.class_id == this.selectedClass);
                  },
                  get allChecked() {
                      return this.filtered.length > 0 && this.filtered.every(s => this.selected.includes(s.id));
                  },
                  toggleAll() {
                      const ids = this.filtered.map(s => s.id);
                      if (this.allChecked) {
                          this.selected = this.selected.filter(id => !ids.includes(id));
                      } else {
                          const merged = new Set([...this.selected, ...ids]);
                          this.selected = [...merged];
                      }
                  },
                  toggle(id) {
                      const idx = this.selected.indexOf(id);
                      if (idx > -1) this.selected.splice(idx, 1);
                      else this.selected.push(id);
                  }
              }">
            @csrf

            <div class="px-3 py-2 bg-ich-info-soft rounded-ich-md text-sm text-ich-teal font-ui font-semibold">
                Ledger: {{ $ledger->ledger_name }}
            </div>

            @if($availableStudents->isEmpty())
                <div class="py-6 text-center text-ich-ink-300 font-sans text-sm">
                    Semua siswa sudah memiliki buku tabungan di ledger ini.
                </div>
            @else
                <div>
                    <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Filter Kelas</label>
                    <select x-model="selectedClass"
                            class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->class_id }}">{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="font-ui font-bold text-sm text-ich-ink-600">
                            Pilih Siswa <span class="text-ich-error">*</span>
                        </label>
                        <span class="text-xs font-ui text-ich-ink-400" x-text="selected.length + ' dipilih'"></span>
                    </div>

                    <div class="border-2 border-ich-line rounded-ich-lg overflow-hidden">
                        <div class="px-3 py-2 bg-ich-surface border-b border-ich-line flex items-center gap-2 cursor-pointer"
                             @click="toggleAll()" x-show="filtered.length > 0">
                            <input type="checkbox" :checked="allChecked" class="rounded border-ich-line text-ich-green focus:ring-ich-green">
                            <span class="text-xs font-ui font-bold text-ich-ink-600">Pilih Semua</span>
                            <span class="text-xs text-ich-ink-400" x-text="'(' + filtered.length + ' siswa)'"></span>
                        </div>
                        <div class="max-h-56 overflow-y-auto divide-y divide-ich-line">
                            <template x-for="s in filtered" :key="s.id">
                                <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-ich-surface cursor-pointer transition-colors">
                                    <input type="checkbox" :value="s.id" :checked="selected.includes(s.id)"
                                           @change="toggle(s.id)"
                                           class="rounded border-ich-line text-ich-green focus:ring-ich-green">
                                    <span class="font-sans text-sm text-ich-ink-900 flex-1" x-text="s.nama"></span>
                                    <span class="px-2 py-0.5 bg-ich-surface text-ich-ink-500 font-ui text-xs rounded-full" x-text="s.kelas"></span>
                                </label>
                            </template>
                            <div x-show="filtered.length === 0" class="px-3 py-6 text-center text-ich-ink-300 text-sm">
                                Tidak ada siswa di kelas ini yang belum punya buku tabungan.
                            </div>
                        </div>
                    </div>
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="student_ids[]" :value="id">
                    </template>
                    @error('student_ids') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Tanggal Buka <span class="text-ich-error">*</span></label>
                        <input type="date" name="opening_date" value="{{ old('opening_date', now()->format('Y-m-d')) }}"
                               class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                        @error('opening_date') <p class="text-ich-error text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">Saldo Awal (Rp)</label>
                        <input type="number" name="opening_balance" value="{{ old('opening_balance', 0) }}" min="0"
                               class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-teal rounded-ich-lg font-sans text-sm focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="selected.length === 0"
                            class="px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="selected.length > 1 ? 'Buka ' + selected.length + ' Buku Tabungan' : 'Buka Buku Tabungan'"></span>
                    </button>
                    <button type="button" @click="showCreate = false"
                            class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600 font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                </div>
            @endif
        </form>
    </x-admin-modal>

</div>
</x-main-layout>
