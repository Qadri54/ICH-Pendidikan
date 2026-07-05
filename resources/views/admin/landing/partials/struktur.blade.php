@php $c = $section->content; $members = $c['members'] ?? []; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Informasi Section</h3>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Label</label>
        <input type="text" name="label" value="{{ $c['label'] ?? 'Organisasi' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
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

<div class="bg-white rounded-xl shadow-ich-card p-6" x-data="strukturEditor()">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display font-bold text-ich-ink-900">Anggota Struktur Organisasi</h3>
        <button type="button" @click="addMember()" class="text-xs px-3 py-1.5 bg-ich-green text-white rounded-lg font-bold">+ Tambah Anggota</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-ich-surface">
                    <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Foto</th>
                    <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Nama</th>
                    <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Jabatan</th>
                    <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Atasan</th>
                    <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Tipe</th>
                    <th class="px-3 py-2 text-left font-ui font-bold text-ich-ink-600 text-xs">Urutan</th>
                    <th class="px-3 py-2 text-center font-ui font-bold text-ich-ink-600 text-xs w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ich-line">
                <template x-for="(m, i) in members" :key="i">
                    <tr class="hover:bg-ich-surface/50">
                        <td class="px-3 py-2">
                            <input type="hidden" :name="'members['+i+'][id]'" x-model="m.id">
                            <input type="hidden" :name="'members['+i+'][existing_photo]'" x-model="m.photo">
                            <div class="flex items-center gap-2">
                                <template x-if="m.photo">
                                    <img :src="m.photo.startsWith('landing/') ? '/storage/'+m.photo : '/'+m.photo" class="w-8 h-8 rounded-full object-cover">
                                </template>
                                <template x-if="!m.photo">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-ich-ink-400" x-text="m.name ? m.name.charAt(0) : '?'"></div>
                                </template>
                                <input type="file" :name="'members['+i+'][photo_file]'" accept="image/*" class="text-xs w-24">
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" :name="'members['+i+'][name]'" x-model="m.name" class="w-full border border-ich-line rounded px-2 py-1 text-sm" placeholder="Nama">
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" :name="'members['+i+'][position]'" x-model="m.position" class="w-full border border-ich-line rounded px-2 py-1 text-sm" placeholder="Jabatan">
                        </td>
                        <td class="px-3 py-2">
                            <select :name="'members['+i+'][parent_id]'" x-model="m.parent_id" class="w-full border border-ich-line rounded px-2 py-1 text-sm">
                                <option value="">— Tidak ada —</option>
                                <template x-for="(p, j) in members" :key="'parent-'+j">
                                    <template x-if="p.id != m.id">
                                        <option :value="p.id" :selected="m.parent_id == p.id" x-text="(p.position ? p.position+': ' : '')+p.name"></option>
                                    </template>
                                </template>
                            </select>
                        </td>
                        <td class="px-3 py-2">
                            <select :name="'members['+i+'][type]'" x-model="m.type" class="w-full border border-ich-line rounded px-2 py-1 text-sm">
                                <option value="default">Default</option>
                                <option value="head">Head (Ketua)</option>
                                <option value="advisory">Advisory (Pembina/Pengawas)</option>
                                <option value="staff">Staff</option>
                                <option value="unit">Unit</option>
                            </select>
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" :name="'members['+i+'][order]'" x-model="m.order" class="w-16 border border-ich-line rounded px-2 py-1 text-sm text-center">
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button type="button" @click="members.splice(i,1)" class="text-red-400 hover:text-red-600 text-lg">&times;</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <p class="text-xs text-ich-ink-400 mt-3">Pilih "Atasan" untuk menentukan hierarki. Anggota tanpa atasan ditampilkan di level teratas.</p>
</div>

<script>
function strukturEditor() {
    return {
        members: @json($members),
        nextId: {{ collect($members)->max('id') + 1 }},
        addMember() {
            this.members.push({
                id: this.nextId++,
                parent_id: null,
                position: '',
                name: '',
                photo: null,
                type: 'default',
                order: this.members.length + 1
            });
        }
    };
}
</script>
