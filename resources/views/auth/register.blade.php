<x-guest-layout>

    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Daftar</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nama --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input id="name" name="name" type="text"
                       value="{{ old('name') }}"
                       placeholder="Masukkan nama..."
                       required autofocus autocomplete="name"
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('name') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input id="email" name="email" type="email"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email..."
                       required autocomplete="username"
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('email') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- No HP --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">No Hp</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                <input id="no_hp" name="no_hp" type="tel"
                       value="{{ old('no_hp') }}"
                       placeholder="Masukkan No Hp..."
                       required
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('no_hp') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('no_hp')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4" x-data="{ showPassword: false }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input id="password" name="password"
                       :type="showPassword ? 'text' : 'password'"
                       placeholder="Masukkan Password..."
                       required autocomplete="new-password"
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('password') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </span>
                <input id="password_confirmation" name="password_confirmation"
                       type="password"
                       placeholder="Konfirmasi Password..."
                       required autocomplete="new-password"
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('password_confirmation') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol Buat Akun --}}
        <button type="submit"
                class="w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold
                       rounded-lg transition focus:outline-none focus:ring-2 focus:ring-yellow-400">
            Buat Akun
        </button>

        <p class="text-center text-sm text-gray-600 mt-4">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-700 font-semibold hover:underline">Masuk</a>
        </p>

    </form>

</x-guest-layout>
