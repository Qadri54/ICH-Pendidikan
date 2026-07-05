@php $c = $section->content; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Tentang Kami</h3>

    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Label</label>
        <input type="text" name="label" value="{{ $c['label'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Judul</label>
        <textarea name="title" rows="2" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['title'] ?? '' }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Deskripsi</label>
        <textarea name="description" rows="3" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['description'] ?? '' }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Gambar</label>
        @if(!empty($c['image']))
            <img src="{{ asset($c['image']) }}" alt="Tentang" class="w-40 h-28 object-cover rounded-lg mb-2">
        @endif
        <input type="file" name="image" accept="image/*" class="text-sm">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Badge Angka</label>
            <input type="text" name="badge_number" value="{{ $c['badge_number'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Badge Teks</label>
            <input type="text" name="badge_text" value="{{ str_replace("\n", ' ', $c['badge_text'] ?? '') }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-4" x-data="{ features: {{ json_encode($c['features'] ?? []) }} }">
    <div class="flex items-center justify-between">
        <h3 class="font-display font-bold text-ich-ink-900">Fitur Unggulan</h3>
        <button type="button" @click="features.push({title:'',description:''})" class="text-xs px-3 py-1.5 bg-ich-green text-white rounded-lg font-bold">+ Tambah</button>
    </div>
    <template x-for="(f, i) in features" :key="i">
        <div class="p-3 bg-ich-surface rounded-lg space-y-2">
            <div class="flex gap-2 items-start">
                <div class="flex-1">
                    <input type="text" :name="'features['+i+'][title]'" x-model="f.title" placeholder="Judul fitur" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
                </div>
                <button type="button" @click="features.splice(i,1)" class="text-red-400 hover:text-red-600 text-lg px-1">&times;</button>
            </div>
            <textarea :name="'features['+i+'][description]'" x-model="f.description" rows="2" placeholder="Deskripsi" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm"></textarea>
        </div>
    </template>
</div>
