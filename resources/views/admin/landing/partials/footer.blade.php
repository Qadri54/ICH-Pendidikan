@php $c = $section->content; @endphp

<div class="bg-white rounded-xl shadow-ich-card p-6 space-y-5">
    <h3 class="font-display font-bold text-ich-ink-900">Footer</h3>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-ich-green/30 focus:border-ich-green">{{ $c['description'] ?? '' }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ $c['email'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Telepon</label>
            <input type="text" name="phone" value="{{ $c['phone'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Alamat</label>
        <input type="text" name="address" value="{{ $c['address'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Jam Operasional</label>
        <textarea name="hours" rows="2" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">{{ $c['hours'] ?? '' }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Nomor WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ $c['whatsapp'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-ui font-bold text-ich-ink-600 mb-1">Copyright</label>
            <input type="text" name="copyright" value="{{ $c['copyright'] ?? '' }}" class="w-full border border-ich-line rounded-lg px-3 py-2 text-sm">
        </div>
    </div>
</div>
