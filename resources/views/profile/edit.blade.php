@php
$isMobile = auth()->user()?->role?->role_name === 'Orang Tua';
$layout   = $isMobile ? 'mobile-layout' : 'main-layout';
$user     = auth()->user();
$role     = $user->role?->role_name ?? 'User';
$initials = collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
@endphp

<x-dynamic-component :component="$layout" title="Profil Saya" page-title="Profil Saya">

    @if(session('status') === 'profile-updated')
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            Profil berhasil diperbarui.
        </div>
    @endif
    @if(session('status') === 'password-updated')
        <div class="mb-4 px-4 py-3 bg-[#D1FAE5] text-[#009966] rounded-lg text-sm font-semibold"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            Password berhasil diperbarui.
        </div>
    @endif

    <div class="{{ $isMobile ? 'pb-6 space-y-4' : 'max-w-3xl space-y-6' }}">

        {{-- Profile Header Card --}}
        <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
            <div class="h-24 bg-gradient-to-r from-ich-teal to-ich-green"></div>
            <div class="px-6 pb-6 -mt-10">
                <div class="flex {{ $isMobile ? 'flex-col items-center text-center' : 'items-end gap-5' }}">
                    <div class="w-20 h-20 rounded-full bg-white shadow-lg flex items-center justify-center ring-4 ring-white flex-shrink-0">
                        <span class="text-2xl font-display font-bold text-ich-teal">{{ $initials }}</span>
                    </div>
                    <div class="{{ $isMobile ? 'mt-3' : 'pb-1' }}">
                        <h1 class="text-xl font-display font-bold text-ich-ink-900">{{ $user->name }}</h1>
                        <div class="flex {{ $isMobile ? 'justify-center' : '' }} items-center gap-2 mt-1">
                            <span class="px-2.5 py-0.5 bg-ich-teal/10 text-ich-teal text-xs font-ui font-bold rounded-full">
                                {{ $role }}
                            </span>
                            <span class="text-xs text-ich-ink-400 font-sans">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid {{ $isMobile ? 'grid-cols-2' : 'grid-cols-3' }} gap-3 mt-5">
                    <div class="bg-[#F5F6FA] rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <x-ich-icon name="mail" :size="14" color="#6B7280"/>
                            <span class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Email</span>
                        </div>
                        <p class="text-sm font-sans text-ich-ink-900 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="bg-[#F5F6FA] rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <x-ich-icon name="phone" :size="14" color="#6B7280"/>
                            <span class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">No. HP</span>
                        </div>
                        <p class="text-sm font-sans text-ich-ink-900">{{ $user->no_hp ?: '-' }}</p>
                    </div>
                    @unless($isMobile)
                    <div class="bg-[#F5F6FA] rounded-lg p-3">
                        <div class="flex items-center gap-2 mb-1">
                            <x-ich-icon name="calendar" :size="14" color="#6B7280"/>
                            <span class="text-[10px] font-ui font-bold text-ich-ink-400 uppercase tracking-wider">Bergabung</span>
                        </div>
                        <p class="text-sm font-sans text-ich-ink-900">{{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</p>
                    </div>
                    @endunless
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: '{{ $errors->updatePassword->isNotEmpty() ? 'password' : ($errors->userDeletion->isNotEmpty() ? 'hapus' : 'profil') }}' }">

            {{-- Tab Navigation --}}
            <div class="flex gap-1 bg-white rounded-xl shadow-ich-card p-1.5 mb-4">
                <button @click="tab = 'profil'" type="button"
                        :class="tab === 'profil' ? 'bg-ich-teal text-white shadow-sm' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-ui font-bold text-sm transition-all">
                    <x-ich-icon name="user" :size="16"/>
                    <span>Informasi</span>
                </button>
                <button @click="tab = 'password'" type="button"
                        :class="tab === 'password' ? 'bg-ich-teal text-white shadow-sm' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-ui font-bold text-sm transition-all">
                    <x-ich-icon name="lock" :size="16"/>
                    <span>Password</span>
                </button>
                <button @click="tab = 'hapus'" type="button"
                        :class="tab === 'hapus' ? 'bg-ich-error text-white shadow-sm' : 'text-ich-ink-500 hover:bg-[#F5F6FA]'"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg font-ui font-bold text-sm transition-all">
                    <x-ich-icon name="alert" :size="16"/>
                    <span>Hapus Akun</span>
                </button>
            </div>

            {{-- Tab: Informasi Profil --}}
            <div x-show="tab === 'profil'" x-cloak>
                <div class="bg-white rounded-xl shadow-ich-card p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-ich-teal/10 flex items-center justify-center">
                            <x-ich-icon name="user" :size="18" color="#009688"/>
                        </div>
                        <div>
                            <h2 class="font-display font-bold text-ich-ink-900">Informasi Profil</h2>
                            <p class="text-xs text-ich-ink-400 font-sans">Perbarui nama, email, dan nomor HP Anda</p>
                        </div>
                    </div>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="name" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                                Nama <span class="text-ich-error">*</span>
                            </label>
                            <input id="name" name="name" type="text"
                                   value="{{ old('name', $user->name) }}"
                                   required autofocus autocomplete="name"
                                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal
                                          @error('name') border-ich-error @else border-ich-line @enderror">
                            @error('name')
                                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                                Email <span class="text-ich-error">*</span>
                            </label>
                            <input id="email" name="email" type="email"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="username"
                                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal
                                          @error('email') border-ich-error @else border-ich-line @enderror">
                            @error('email')
                                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-ich-lg">
                                    <p class="text-sm text-yellow-800">
                                        Email Anda belum diverifikasi.
                                        <button form="send-verification"
                                                class="underline font-semibold hover:text-yellow-900">
                                            Kirim ulang email verifikasi
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-1 text-sm font-medium text-green-700">
                                            Link verifikasi baru telah dikirim ke email Anda.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="no_hp" class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                                No. HP
                            </label>
                            <input id="no_hp" name="no_hp" type="tel"
                                   value="{{ old('no_hp', $user->no_hp) }}"
                                   autocomplete="tel"
                                   placeholder="Contoh: 08123456789"
                                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal
                                          @error('no_hp') border-ich-error @else border-ich-line @enderror">
                            @error('no_hp')
                                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-2.5 bg-ich-green text-white font-ui font-bold text-sm
                                           rounded-ich-lg shadow-ich-btn hover:bg-ich-green-dark transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tab: Password --}}
            <div x-show="tab === 'password'" x-cloak>
                <div class="bg-white rounded-xl shadow-ich-card p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-lg bg-[#EDE9FE] flex items-center justify-center">
                            <x-ich-icon name="lock" :size="18" color="#8B5CF6"/>
                        </div>
                        <div>
                            <h2 class="font-display font-bold text-ich-ink-900">Perbarui Password</h2>
                            <p class="text-xs text-ich-ink-400 font-sans">Gunakan password yang panjang dan unik</p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="update_password_current_password"
                                   class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                                Password Saat Ini <span class="text-ich-error">*</span>
                            </label>
                            <input id="update_password_current_password" name="current_password" type="password"
                                   autocomplete="current-password"
                                   class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-teal
                                          @error('current_password', 'updatePassword') border-ich-error @else border-ich-line @enderror">
                            @error('current_password', 'updatePassword')
                                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid {{ $isMobile ? 'grid-cols-1' : 'grid-cols-2' }} gap-4">
                            <div>
                                <label for="update_password_password"
                                       class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                                    Password Baru <span class="text-ich-error">*</span>
                                </label>
                                <input id="update_password_password" name="password" type="password"
                                       autocomplete="new-password"
                                       class="w-full h-[46px] px-3.5 bg-white border-2 rounded-ich-lg font-sans text-sm
                                              focus:outline-none focus:border-ich-teal
                                              @error('password', 'updatePassword') border-ich-error @else border-ich-line @enderror">
                                @error('password', 'updatePassword')
                                    <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="update_password_password_confirmation"
                                       class="block font-ui font-bold text-sm text-ich-ink-600 mb-1.5">
                                    Konfirmasi Password <span class="text-ich-error">*</span>
                                </label>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                       autocomplete="new-password"
                                       class="w-full h-[46px] px-3.5 bg-white border-2 border-ich-line rounded-ich-lg font-sans text-sm
                                              focus:outline-none focus:border-ich-teal">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-2.5 bg-[#8B5CF6] text-white font-ui font-bold text-sm
                                           rounded-ich-lg shadow-sm hover:bg-[#7C3AED] transition-colors">
                                Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tab: Hapus Akun --}}
            <div x-show="tab === 'hapus'" x-cloak>
                <div class="bg-white rounded-xl shadow-ich-card overflow-hidden">
                    <div class="bg-red-50 border-b border-red-100 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                                <x-ich-icon name="alert" :size="18" color="#EF4444"/>
                            </div>
                            <div>
                                <h2 class="font-display font-bold text-ich-error">Zona Bahaya</h2>
                                <p class="text-xs text-ich-ink-500 font-sans">Tindakan ini tidak dapat dibatalkan</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6" x-data="{ confirmDelete: false }">
                        <div class="bg-red-50/50 rounded-lg p-4 mb-5">
                            <p class="text-sm text-ich-ink-700 font-sans leading-relaxed">
                                Menghapus akun akan menghapus <strong>semua data</strong> secara permanen, termasuk riwayat pembayaran,
                                data kehadiran, dan informasi lainnya. Pastikan Anda sudah mengunduh data penting sebelum melanjutkan.
                            </p>
                        </div>

                        <button @click="confirmDelete = true" x-show="!confirmDelete"
                                class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm
                                       rounded-ich-lg hover:bg-red-700 transition-colors">
                            Hapus Akun Saya
                        </button>

                        <form method="post" action="{{ route('profile.destroy') }}"
                              x-show="confirmDelete" x-cloak x-transition class="space-y-4">
                            @csrf
                            @method('delete')

                            <p class="text-sm font-ui font-bold text-ich-ink-700">Masukkan password Anda untuk konfirmasi:</p>

                            <input name="password" type="password"
                                   placeholder="Password Anda"
                                   class="w-full h-[46px] px-3.5 bg-white border-2 border-red-300 rounded-ich-lg font-sans text-sm
                                          focus:outline-none focus:border-ich-error
                                          @error('password', 'userDeletion') border-ich-error @enderror">
                            @error('password', 'userDeletion')
                                <p class="text-ich-error text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <div class="flex gap-3">
                                <button type="submit"
                                        class="px-6 py-2.5 bg-ich-error text-white font-ui font-bold text-sm
                                               rounded-ich-lg hover:bg-red-700 transition-colors">
                                    Ya, Hapus Akun
                                </button>
                                <button type="button" @click="confirmDelete = false"
                                        class="px-6 py-2.5 bg-white border border-ich-line text-ich-ink-600
                                               font-ui font-bold text-sm rounded-ich-lg hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dynamic-component>
