<x-mobile-layout title="Tabungan" page-title="Tabungan">

    @if($data->isEmpty())
        <div class="bg-white rounded-xl shadow-ich-card p-8 text-center">
            <x-ich-icon name="piggy" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
            <p class="font-ui font-bold text-sm text-ich-ink-900 mb-1">Belum Ada Data Tabungan</p>
            <p class="font-sans text-xs text-ich-ink-400">
                Data tabungan anak Anda belum tersedia. Hubungi pihak sekolah untuk informasi lebih lanjut.
            </p>
        </div>
    @else
        <div class="space-y-3">
        @foreach($data as $item)
            @php
                $student    = $item['student'];
                $totalSaldo = $item['totalSaldo'];
                $jumlahBuku = $item['jumlahBuku'];
            @endphp

            <a href="{{ route('tabungan.detail', $student->student_id) }}"
               class="block bg-white rounded-xl shadow-ich-card p-5 hover:shadow-md transition-shadow active:scale-[0.98]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-ich-warning-soft flex items-center justify-center shrink-0">
                        <span class="font-display font-bold text-lg text-ich-warning">
                            {{ strtoupper(substr($student->nama_siswa, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-ui font-bold text-sm text-ich-ink-900 truncate">
                            {{ $student->nama_siswa }}
                        </p>
                        <p class="font-sans text-xs text-ich-ink-400 mt-0.5">
                            {{ $student->classRoom?->nama_kelas ?? 'Belum ada kelas' }}
                            @if($student->NIS) · NIS: {{ $student->NIS }} @endif
                        </p>

                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @if($jumlahBuku > 0)
                                <span class="px-2 py-0.5 bg-ich-success-soft text-ich-success text-xs font-ui font-bold rounded-full">
                                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                                </span>
                                <span class="px-2 py-0.5 bg-ich-info-soft text-ich-info text-xs font-ui font-bold rounded-full">
                                    {{ $jumlahBuku }} buku
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-ich-surface text-ich-ink-400 text-xs font-ui font-bold rounded-full">
                                    Belum ada tabungan
                                </span>
                            @endif
                        </div>
                    </div>
                    <x-ich-icon name="chevron_right" :size="20" color="#99A1AF"/>
                </div>
            </a>
        @endforeach
        </div>
    @endif

</x-mobile-layout>
