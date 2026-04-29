<x-guest-layout>

    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Masuk</h2>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
        @csrf

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
                       placeholder="Email"
                       required autofocus autocomplete="username"
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('email') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
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
                       placeholder="Password"
                       required autocomplete="current-password"
                       class="w-full pl-10 pr-4 py-3 border-2 rounded-lg focus:outline-none focus:border-green-600
                              {{ $errors->has('password') ? 'border-red-400' : 'border-green-400' }}">
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Show password + Lupa password --}}
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" x-model="showPassword"
                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                Perlihatkan Password
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-gray-600 hover:text-green-700">
                    Lupa Password?
                </a>
            @endif
        </div>

        {{-- Tombol Masuk --}}
        <button type="submit"
                class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg
                       transition focus:outline-none focus:ring-2 focus:ring-blue-500">
            Masuk
        </button>

        {{-- Daftar --}}
        <div class="mt-4">
            <p class="text-center text-sm text-gray-600 mb-2">Belum Punya Akun?</p>
            <a href="{{ route('register') }}"
               class="block w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-white font-semibold
                      rounded-lg text-center transition focus:outline-none focus:ring-2 focus:ring-yellow-400">
                Buat Akun
            </a>
        </div>

    </form>

</x-guest-layout>
