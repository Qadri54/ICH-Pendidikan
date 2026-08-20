<x-main-layout title="Manajemen Backup Data">

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-ich-blue-soft flex items-center justify-center">
                <svg class="w-5 h-5 text-ich-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-display font-bold text-ich-ink-900">Manajemen Backup Data</h1>
                <p class="text-sm text-ich-ink-400 mt-0.5">Pencadangan database dan storage secara berkala</p>
            </div>
        </div>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-ich-green-soft flex items-center justify-center">
                <svg class="w-6 h-6 text-ich-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-ui font-bold text-ich-ink-400 uppercase tracking-wider mb-1">Total File Backup</p>
                <h3 class="text-2xl font-display font-bold text-ich-ink-900">{{ $stats['count'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-ich-orange-soft flex items-center justify-center">
                <svg class="w-6 h-6 text-ich-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
            </div>
            <div>
                <p class="text-xs font-ui font-bold text-ich-ink-400 uppercase tracking-wider mb-1">Total Ukuran</p>
                <h3 class="text-2xl font-display font-bold text-ich-ink-900">{{ $stats['size'] }}</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-ich-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-ich-purple-soft flex items-center justify-center">
                <svg class="w-6 h-6 text-ich-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-ui font-bold text-ich-ink-400 uppercase tracking-wider mb-1">Backup Terakhir</p>
                <h3 class="text-lg font-ui font-bold text-ich-ink-900 mt-1 line-clamp-1">{{ $stats['latest'] }}</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-ich-card overflow-hidden" x-data="{ loading: false }">
        <div class="px-6 py-4 border-b border-ich-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-ui font-bold text-ich-ink-900">Daftar File Backup</h2>
                <p class="text-xs text-ich-ink-400 mt-0.5">Semua hasil pencadangan format ZIP.</p>
            </div>
            
            <form method="POST" action="{{ route('admin.backup.store') }}" @submit="loading = true">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 bg-ich-teal text-white font-ui font-bold text-sm rounded-ich-lg shadow-ich-btn hover:bg-ich-teal-dark transition-colors flex items-center gap-2"
                        :disabled="loading"
                        :class="{'opacity-70 cursor-not-allowed': loading}">
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <svg x-show="loading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Mencadangkan...' : 'Buat Backup Sekarang'"></span>
                </button>
            </form>
        </div>
        
        <div x-show="loading" x-cloak class="px-6 py-3 bg-ich-info-soft text-ich-info text-sm font-semibold flex items-center gap-2">
            <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Proses pencadangan (Database & Storage) sedang berjalan, mohon jangan tutup halaman ini.
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ich-surface">
                    <tr>
                        <th class="px-6 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Nama File</th>
                        <th class="px-6 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Ukuran</th>
                        <th class="px-6 py-3 text-left font-ui font-bold text-xs text-ich-ink-500">Tanggal Dibuat</th>
                        <th class="px-6 py-3 text-right font-ui font-bold text-xs text-ich-ink-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ich-line">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-ich-surface transition-colors">
                            <td class="px-6 py-4 font-ui font-bold text-ich-ink-900">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-ich-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    {{ $backup['file_name'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-sans text-ich-ink-600">{{ $backup['file_size'] }}</td>
                            <td class="px-6 py-4 font-sans text-ich-ink-600">{{ $backup['last_modified'] }}</td>
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                <a href="{{ route('admin.backup.download', ['file' => $backup['file_name']]) }}" 
                                   class="text-ich-teal hover:text-ich-teal-dark transition-colors"
                                   title="Download ZIP">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                                <form action="{{ route('admin.backup.destroy', ['file' => $backup['file_name']]) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus file backup ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-ich-error hover:text-ich-error-dark transition-colors" title="Hapus Backup">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <x-ich-icon name="database" :size="40" color="#99A1AF" class="mx-auto mb-3"/>
                                <p class="font-ui font-bold text-ich-ink-900 mb-1">Belum Ada Backup</p>
                                <p class="font-sans text-sm text-ich-ink-400">Silakan klik "Buat Backup Sekarang" untuk mencadangkan data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-main-layout>
