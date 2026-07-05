@php $c = $section->content; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Informasi Section</h3>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Label</label>
        <input type="text" name="label" value="{{ $c['label'] ?? 'Testimoni' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Judul</label>
        <input type="text" name="title" value="{{ $c['title'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
</div>

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-4" x-data="{ items: {{ json_encode($c['items'] ?? []) }} }">
    <div class="flex items-center justify-between">
        <h3 class="font-display font-bold text-ich-ink-900">Daftar Testimoni</h3>
        <button type="button" @click="items.push({text:'',name:'',role:'',avatar:null})" class="text-xs px-3 py-1.5 bg-ich-green text-white rounded-lg font-bold">+ Tambah</button>
    </div>
    <template x-for="(item, i) in items" :key="i">
        <div class="p-4 bg-ich-surface rounded-lg space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-ich-ink-400" x-text="'Testimoni '+(i+1)"></span>
                <button type="button" @click="items.splice(i,1)" class="text-red-400 hover:text-red-600 text-lg">&times;</button>
            </div>
            <textarea :name="'items['+i+'][text]'" x-model="item.text" rows="3" placeholder="Isi testimoni" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm"></textarea>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" :name="'items['+i+'][name]'" x-model="item.name" placeholder="Nama" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
                <input type="text" :name="'items['+i+'][role]'" x-model="item.role" placeholder="Peran (misal: Orang Tua Siswa)" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-ich-ink-400 mb-1">Foto (opsional)</label>
                <input type="hidden" :name="'items['+i+'][existing_avatar]'" x-model="item.avatar">
                <input type="file" :name="'items['+i+'][avatar_file]'" accept="image/*" class="text-xs">
            </div>
        </div>
    </template>
</div>
