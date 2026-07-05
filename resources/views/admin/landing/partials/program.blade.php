@php $c = $section->content; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Informasi Section</h3>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Label</label>
        <input type="text" name="label" value="{{ $c['label'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Judul</label>
        <input type="text" name="title" value="{{ $c['title'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Subjudul</label>
        <textarea name="subtitle" rows="2" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['subtitle'] ?? '' }}</textarea>
    </div>
</div>

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-4" x-data="{ items: {{ json_encode($c['items'] ?? []) }} }">
    <div class="flex items-center justify-between">
        <h3 class="font-display font-bold text-ich-ink-900">Daftar Program</h3>
        <button type="button" @click="items.push({title:'',description:'',highlighted:false})" class="text-xs px-3 py-1.5 bg-ich-green text-white rounded-lg font-bold">+ Tambah</button>
    </div>
    <template x-for="(item, i) in items" :key="i">
        <div class="p-4 bg-ich-surface rounded-lg space-y-3" :class="item.highlighted ? 'ring-2 ring-ich-green/30' : ''">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-ich-ink-400" x-text="'Program '+(i+1)"></span>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-1.5 text-xs text-ich-ink-500">
                        <input type="checkbox" :name="'items['+i+'][highlighted]'" x-model="item.highlighted" value="1" class="rounded">
                        Highlight
                    </label>
                    <button type="button" @click="items.splice(i,1)" class="text-red-400 hover:text-red-600 text-lg">&times;</button>
                </div>
            </div>
            <input type="text" :name="'items['+i+'][title]'" x-model="item.title" placeholder="Nama program" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
            <textarea :name="'items['+i+'][description]'" x-model="item.description" rows="2" placeholder="Deskripsi program" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm"></textarea>
        </div>
    </template>
</div>
