@php $c = $section->content; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Hero Banner</h3>

    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Badge Text</label>
        <input type="text" name="badge" value="{{ $c['badge'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Judul</label>
        <input type="text" name="title" value="{{ $c['title'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Subjudul</label>
        <textarea name="subtitle" rows="3" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['subtitle'] ?? '' }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Gambar Background</label>
        @if(!empty($c['image']))
            <img src="{{ asset($c['image']) }}" alt="Hero" class="w-40 h-24 object-cover rounded-lg mb-2">
        @endif
        <input type="file" name="image" accept="image/*" class="text-sm">
    </div>
</div>

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-4" x-data="{ stats: {{ json_encode($c['stats'] ?? []) }} }">
    <h3 class="font-display font-bold text-ich-ink-900">Statistik Strip</h3>
    <template x-for="(stat, i) in stats" :key="i">
        <div class="grid grid-cols-2 gap-3 p-3 bg-ich-surface rounded-lg">
            <div>
                <label class="block text-xs font-ui font-bold text-ich-ink-400 mb-1">Judul</label>
                <input type="text" :name="'stats['+i+'][title]'" x-model="stat.title" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-ui font-bold text-ich-ink-400 mb-1">Keterangan</label>
                <input type="text" :name="'stats['+i+'][subtitle]'" x-model="stat.subtitle" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
            </div>
            <input type="hidden" :name="'stats['+i+'][icon]'" x-model="stat.icon">
        </div>
    </template>
</div>
