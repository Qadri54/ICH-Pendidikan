@php $c = $section->content; $members = $c['members'] ?? []; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5 max-w-4xl">
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

<div x-data="strukturEditor()">
    <div class="flex gap-6 items-start">

        {{-- Kolom Kiri: Daftar Anggota --}}
        <div class="w-full lg:w-1/2 space-y-4">
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-display font-bold text-ich-ink-900">Anggota Organisasi</h3>
                        <p class="text-xs text-ich-ink-400 mt-0.5">Tambah anggota dan pilih atasan untuk menentukan hierarki</p>
                    </div>
                    <button type="button" @click="addMember()"
                            class="flex items-center gap-1.5 text-xs px-3 py-2 bg-ich-green text-white rounded-lg font-bold hover:bg-ich-green/90 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                        </svg>
                        Tambah
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(m, i) in members" :key="m.id">
                        <div class="border border-ich-line rounded-xl p-4 hover:border-ich-green/40 transition-colors relative group">
                            <input type="hidden" :name="'members['+i+'][id]'" x-model="m.id">
                            <input type="hidden" :name="'members['+i+'][existing_photo]'" x-model="m.photo">
                            <input type="hidden" :name="'members['+i+'][type]'" :value="getAutoType(m)">
                            <input type="hidden" :name="'members['+i+'][order]'" :value="i + 1">

                            <button type="button" @click="removeMember(i)"
                                    class="absolute top-3 right-3 w-7 h-7 rounded-full bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors opacity-0 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <template x-if="m.photo">
                                        <img :src="m.photo.startsWith('landing/') ? '/storage/'+m.photo : '/'+m.photo"
                                             class="w-14 h-14 rounded-xl object-cover border-2 border-ich-line">
                                    </template>
                                    <template x-if="!m.photo">
                                        <div class="w-14 h-14 rounded-xl bg-ich-surface flex items-center justify-center border-2 border-dashed border-ich-line">
                                            <span class="text-lg font-bold text-ich-ink-300" x-text="m.name ? m.name.charAt(0).toUpperCase() : '?'"></span>
                                        </div>
                                    </template>
                                    <label class="block mt-1.5 text-center">
                                        <span class="text-[10px] text-ich-teal font-bold cursor-pointer hover:underline">Ganti Foto</span>
                                        <input type="file" :name="'members['+i+'][photo_file]'" accept="image/*" class="hidden">
                                    </label>
                                </div>

                                <div class="flex-1 space-y-2.5">
                                    <div class="grid grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[11px] font-ui font-bold text-ich-ink-400 mb-1">Nama</label>
                                            <input type="text" :name="'members['+i+'][name]'" x-model="m.name"
                                                   class="w-full border border-ich-line rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green"
                                                   placeholder="Nama lengkap">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-ui font-bold text-ich-ink-400 mb-1">Jabatan</label>
                                            <input type="text" :name="'members['+i+'][position]'" x-model="m.position"
                                                   class="w-full border border-ich-line rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green"
                                                   placeholder="Contoh: Ketua Yayasan">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-ui font-bold text-ich-ink-400 mb-1">Atasan</label>
                                        <select :name="'members['+i+'][parent_id]'" x-model="m.parent_id"
                                                class="w-full border border-ich-line rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
                                            <option value="">— Tidak ada (paling atas) —</option>
                                            <template x-for="(p, j) in members" :key="'p-'+p.id">
                                                <template x-if="p.id != m.id">
                                                    <option :value="p.id" :selected="m.parent_id == p.id"
                                                            x-text="p.name ? p.name + (p.position ? ' — ' + p.position : '') : '(Belum diisi)'">
                                                    </option>
                                                </template>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="members.length === 0">
                        <div class="border-2 border-dashed border-ich-line rounded-xl p-8 text-center">
                            <div class="w-12 h-12 bg-ich-surface rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-ich-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-ich-ink-400 font-ui">Belum ada anggota. Klik <strong>"Tambah"</strong> untuk memulai.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Preview Bagan (Sticky) --}}
        <div class="hidden lg:block lg:w-1/2 lg:sticky lg:top-6">
            <div class="bg-white rounded-xl shadow-ich-card p-6">
                <h3 class="font-display font-bold text-ich-ink-900 mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-ich-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    Preview Bagan
                </h3>
                <p class="text-xs text-ich-ink-400 mb-4">Otomatis diperbarui saat mengedit</p>

                <div class="pv-scroll overflow-x-auto pb-4">
                    <template x-if="getTreeRoots().length > 0">
                        <div class="pv-tree" x-html="renderRoots()"></div>
                    </template>
                    <template x-if="getTreeRoots().length === 0 && members.length > 0">
                        <div class="py-8 text-center">
                            <p class="text-sm text-ich-ink-400 italic">Isi nama anggota untuk melihat bagan</p>
                        </div>
                    </template>
                    <template x-if="members.length === 0">
                        <div class="py-8 text-center">
                            <div class="w-16 h-16 bg-ich-surface rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-ich-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                                </svg>
                            </div>
                            <p class="text-sm text-ich-ink-300 font-ui">Bagan akan muncul di sini</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .pv-tree { width: fit-content; min-width: 100%; padding: 8px 0; }

    /* ── Root level: sejajar tanpa garis (seperti landing page) ── */
    .pv-roots {
        display: flex;
        justify-content: center;
        gap: 28px;
        padding: 0;
    }

    /* ── Rekursif tree menggunakan <ul>/<li> seperti landing page ── */
    .pv-tree ul {
        margin: 0; padding: 20px 0 0; list-style: none;
        display: flex; justify-content: center; position: relative;
    }
    /* Vertical line from parent down to horizontal bar */
    .pv-tree ul::before {
        content: ''; position: absolute; top: 0; left: 50%;
        height: 20px; border-left: 2px solid #B8D8BA;
    }
    /* Each child */
    .pv-tree li {
        display: flex; flex-direction: column; align-items: center;
        position: relative; padding: 20px 10px 0;
    }
    /* Horizontal connector lines */
    .pv-tree li::before, .pv-tree li::after {
        content: ''; position: absolute; top: 0; width: 50%; height: 20px;
    }
    .pv-tree li::before { right: 50%; border-top: 2px solid #B8D8BA; }
    .pv-tree li::after  { left: 50%; border-top: 2px solid #B8D8BA; border-left: 2px solid #B8D8BA; }
    /* Hide outer edges */
    .pv-tree li:first-child::before { border-top: none; }
    .pv-tree li:last-child::after   { border-top: none; }
    .pv-tree li:only-child::before  { display: none; }
    .pv-tree li:only-child::after   { border-top: none; }

    /* Root <ul> has no connectors */
    .pv-roots > .pv-node > ul { padding-top: 20px; }
    .pv-roots > .pv-node > ul::before { display: block; }

    /* ── Card ── */
    .pv-card {
        background: #fff; border-radius: 10px; padding: 10px 12px;
        box-shadow: 0 2px 8px rgba(16,24,40,0.07);
        text-align: center; min-width: 100px; max-width: 140px;
        border-top: 3px solid #4A9E5C;
    }
    .pv-card--head {
        border-top-color: #E8A838;
        background: linear-gradient(to bottom, #FFFBF0, #fff);
        min-width: 120px;
    }
    .pv-card--sub { border-top-color: #d4e8d6; }

    /* Avatar */
    .pv-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        margin: 0 auto 4px; background: #EEF6F0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 14px; color: #4A9E5C;
    }
    .pv-role {
        font-size: 8px; font-weight: 600; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.03em;
        margin-bottom: 2px; line-height: 1.3;
    }
    .pv-name {
        font-weight: 700; font-size: 10.5px; color: #1a1a2e;
        line-height: 1.3; word-break: break-word;
    }
    .pv-node {
        display: flex; flex-direction: column; align-items: center;
    }
</style>

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
        },

        removeMember(index) {
            const removed = this.members[index];
            this.members.forEach(m => {
                if (m.parent_id == removed.id) m.parent_id = null;
            });
            this.members.splice(index, 1);
        },

        getAutoType(m) {
            if (!m.parent_id) return 'head';
            const children = this.members.filter(c => c.parent_id == m.id);
            if (children.length > 0) return 'unit';
            return 'staff';
        },

        getTreeRoots() {
            return this.members
                .filter(m => !m.parent_id && m.name)
                .sort((a, b) => (a.order || 0) - (b.order || 0));
        },

        getChildren(parentId) {
            return this.members
                .filter(m => m.parent_id == parentId && m.name)
                .sort((a, b) => (a.order || 0) - (b.order || 0));
        },

        renderRoots() {
            const roots = this.getTreeRoots();
            let html = '<div class="pv-roots">';
            for (const root of roots) {
                html += this.renderNode(root, true);
            }
            html += '</div>';
            return html;
        },

        renderNode(node, isRoot) {
            const children = this.getChildren(node.id);
            const cardClass = isRoot ? 'pv-card pv-card--head' : (children.length > 0 ? 'pv-card' : 'pv-card pv-card--sub');
            const initial = node.name ? node.name.charAt(0).toUpperCase() : '?';

            let html = '<div class="pv-node">';

            html += '<div class="' + cardClass + '">';
            html += '<div class="pv-avatar">' + this.esc(initial) + '</div>';
            if (node.position) {
                html += '<div class="pv-role">' + this.esc(node.position) + '</div>';
            }
            html += '<div class="pv-name">' + this.esc(node.name) + '</div>';
            html += '</div>';

            if (children.length > 0) {
                html += '<ul>';
                for (const child of children) {
                    html += '<li>' + this.renderNode(child, false) + '</li>';
                }
                html += '</ul>';
            }

            html += '</div>';
            return html;
        },

        esc(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }
    };
}
</script>
