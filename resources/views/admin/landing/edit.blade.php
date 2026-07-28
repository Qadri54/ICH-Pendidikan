<x-main-layout title="Edit {{ $section->title }}">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.landing.index') }}" class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
            <svg class="w-4 h-4 text-ich-ink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-display font-bold text-ich-ink-900">Edit {{ $section->title }}</h1>
            <p class="text-sm text-ich-ink-400 mt-0.5">Ubah konten section "{{ $section->key }}" pada landing page</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 bg-ich-success-soft text-ich-success rounded-lg text-sm font-semibold"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 px-4 py-3 bg-ich-error-soft text-ich-error rounded-lg text-sm">
            @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
        </div>
    @endif

    <form action="{{ route('admin.landing.update', $section->key) }}" method="POST" enctype="multipart/form-data"
          class="{{ $section->key === 'struktur' ? '' : 'max-w-4xl' }} space-y-6">
        @csrf
        @method('PUT')

        @include('admin.landing.partials.' . $section->key)

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-ich-green text-white rounded-lg font-ui font-bold text-sm hover:bg-ich-green/90 transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.landing.index') }}" class="px-6 py-2.5 bg-gray-100 text-ich-ink-600 rounded-lg font-ui font-bold text-sm hover:bg-gray-200 transition-colors">
                Batal
            </a>
        </div>
    </form>

</x-main-layout>
