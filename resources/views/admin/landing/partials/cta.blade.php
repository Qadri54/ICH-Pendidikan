@php $c = $section->content; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Call to Action</h3>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Judul</label>
        <textarea name="title" rows="2" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['title'] ?? '' }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Subjudul</label>
        <textarea name="subtitle" rows="2" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['subtitle'] ?? '' }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Nomor WhatsApp (tanpa +)</label>
        <input type="text" name="whatsapp" value="{{ $c['whatsapp'] ?? '' }}" placeholder="6281360765971" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">
    </div>
</div>
