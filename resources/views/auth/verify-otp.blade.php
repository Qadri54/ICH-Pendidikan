<x-guest-layout>

{{--
  Mobile  (< lg): full-screen hero bg + green overlay, white text labels
  Desktop (≥ lg): no bg, dark text labels — the guest layout provides the white right panel
--}}
@php
    $sisaKuotaHarian = (int) ($remainingDailySends ?? 4);
    $maksKuotaHarian = (int) ($maxDailySends ?? 4);
    $terpakaiHarian  = max(0, $maksKuotaHarian - $sisaKuotaHarian);
    $sisaDetikAktif  = (int) ($expirySeconds ?? 0);
@endphp
<div class="relative flex flex-col min-h-screen lg:min-h-0 lg:static"
     x-data="{
         digits: ['', '', '', '', '', ''],
         cooldown: {{ (int) ($cooldownSeconds ?? 0) }},
         expiry: {{ $sisaDetikAktif }},
         remainingSends: {{ $sisaKuotaHarian }},
         editPhone: {{ $errors->has('no_hp') ? 'true' : 'false' }},
         timer: null,
         expiryTimer: null,
         init() {
             if (this.cooldown > 0 && this.remainingSends > 0) {
                 this.startTimer();
             }
             if (this.expiry > 0) {
                 this.startExpiryTimer();
             }
             this.$nextTick(() => {
                 if (this.$refs.digit0) {
                     this.$refs.digit0.focus();
                 }
             });
         },
         startTimer() {
             if (this.timer) clearInterval(this.timer);
             this.timer = setInterval(() => {
                 if (this.cooldown > 0) {
                     this.cooldown--;
                 } else {
                     clearInterval(this.timer);
                 }
             }, 1000);
         },
         startExpiryTimer() {
             if (this.expiryTimer) clearInterval(this.expiryTimer);
             this.expiryTimer = setInterval(() => {
                 if (this.expiry > 0) {
                     this.expiry--;
                 } else {
                     clearInterval(this.expiryTimer);
                 }
             }, 1000);
         },
         get formattedExpiry() {
             const m = String(Math.floor(this.expiry / 60)).padStart(2, '0');
             const s = String(this.expiry % 60).padStart(2, '0');
             return `${m}:${s}`;
         },
         handleInput(index, event) {
             const val = event.target.value.replace(/[^0-9]/g, '');
             this.digits[index] = val ? val.slice(-1) : '';
             event.target.value = this.digits[index];

             if (this.digits[index] && index < 5) {
                 this.$refs['digit' + (index + 1)].focus();
             }
         },
         handleKeydown(index, event) {
             if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                 this.digits[index - 1] = '';
                 this.$refs['digit' + (index - 1)].focus();
             } else if (event.key === 'ArrowLeft' && index > 0) {
                 this.$refs['digit' + (index - 1)].focus();
             } else if (event.key === 'ArrowRight' && index < 5) {
                 this.$refs['digit' + (index + 1)].focus();
             }
         },
         handlePaste(event) {
             event.preventDefault();
             const pasted = (event.clipboardData || window.clipboardData)
                 .getData('text')
                 .replace(/[^0-9]/g, '')
                 .slice(0, 6);
             if (!pasted) return;
             for (let i = 0; i < 6; i++) {
                 this.digits[i] = pasted[i] || '';
                 if (this.$refs['digit' + i]) {
                     this.$refs['digit' + i].value = this.digits[i];
                 }
             }
             const focusIdx = Math.min(pasted.length, 5);
             if (this.$refs['digit' + focusIdx]) {
                 this.$refs['digit' + focusIdx].focus();
             }
         },
         get fullCode() {
             return this.digits.join('');
         }
     }">

    {{-- Mobile-only: hero photo background --}}
    <div class="lg:hidden absolute inset-0 bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('images/hero-students.jpg') }}')"></div>
    {{-- Mobile-only: #51B059 green overlay --}}
    <div class="lg:hidden absolute inset-0" style="background: var(--ich-green-soft); opacity: 0.45"></div>

    {{-- Content container --}}
    <div class="relative z-10 lg:static flex flex-col flex-1 lg:flex-none
                px-5 pt-10 pb-6 lg:px-0 lg:pt-0 lg:pb-0 gap-4">

        {{-- Mobile header: logo + title + subtitle --}}
        <div class="lg:hidden flex flex-col items-center gap-2.5 mt-4 mb-1">
            <div class="w-[72px] h-[72px] rounded-[18px] bg-white flex items-center justify-center shadow-ich-logo overflow-hidden">
                <img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" class="w-14 h-14 object-contain">
            </div>
            <div class="font-special font-bold text-[28px] text-white leading-tight text-center"
                 style="text-shadow:0 4px 4px rgba(0,0,0,0.25)">
                Verifikasi Akun
            </div>
            <div class="font-sans text-[12px] text-white text-center max-w-xs" style="opacity:.95">
                Masukkan 6 digit kode OTP yang telah dikirimkan ke WhatsApp <span class="font-bold">{{ $maskedPhone }}</span>
            </div>
        </div>

        {{-- Desktop header --}}
        <div class="hidden lg:block mb-1">
            <div class="flex items-center justify-between gap-2 mb-2.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-ich-teal/10 text-ich-teal font-ui font-bold text-[11px]">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Verifikasi WhatsApp OTP
                </div>
                <span class="px-2.5 py-1 rounded-full font-ui font-bold text-[11px] {{ $sisaKuotaHarian > 0 ? 'bg-ich-surface text-ich-ink-600' : 'bg-ich-error-soft text-ich-error' }}">
                    Kuota 24 Jam: {{ $terpakaiHarian }}/{{ $maksKuotaHarian }}
                </span>
            </div>
            <h2 class="font-special font-bold text-[28px] text-ich-ink-900 leading-tight">Verifikasi Akun</h2>
            <p class="font-sans text-[13px] text-ich-ink-400 mt-1 leading-relaxed">
                Kami telah mengirimkan 6 digit kode OTP ke nomor WhatsApp
                <span class="font-bold text-ich-ink-800">{{ $maskedPhone }}</span>.
            </p>
        </div>

        {{-- Alerts / Status --}}
        @if (session('status'))
            <div class="px-4 py-3 bg-ich-success-soft text-ich-success rounded-ich-lg text-[12px] font-semibold text-center shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="px-4 py-3 bg-ich-error-soft text-ich-error rounded-ich-lg text-[12px] font-semibold text-center shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form Verifikasi OTP --}}
        <form method="POST" action="{{ route('otp.verify') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="otp_code" :value="fullCode">

            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <label class="font-ui font-bold text-[12px] text-white lg:text-ich-ink-600 mobile-label-glow">
                        Kode OTP (6 Digit)
                    </label>
                    <span x-show="expiry > 0" class="font-ui font-bold text-[11px] text-white lg:text-ich-teal mobile-label-glow">
                        Berlaku: <span x-text="formattedExpiry"></span>
                    </span>
                    <span x-show="expiry <= 0" x-cloak class="font-ui font-bold text-[11px] text-red-200 lg:text-ich-error">
                        Kode OTP Kedaluwarsa (> 10 menit)
                    </span>
                </div>

                <div class="grid grid-cols-6 gap-2 sm:gap-2.5" @paste="handlePaste($event)">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text"
                               inputmode="numeric"
                               maxlength="1"
                               x-ref="digit{{ $i }}"
                               @input="handleInput({{ $i }}, $event)"
                               @keydown="handleKeydown({{ $i }}, $event)"
                               class="w-full h-[52px] bg-white border-2 rounded-ich-lg text-center
                                      font-display font-bold text-[20px] text-ich-ink-900
                                      shadow-ich-lift transition-all
                                      focus:outline-none focus:ring-2 focus:ring-ich-teal/30 focus:border-ich-teal
                                      {{ $errors->has('otp_code') ? 'border-ich-error' : 'border-ich-teal' }}">
                    @endfor
                </div>

                @error('otp_code')
                    <p class="font-sans text-[12px] font-semibold text-red-200 lg:text-ich-error text-center lg:text-left mt-0.5">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Info Masa Berlaku 10 Menit, Kuota 4x/24 Jam & Penghapusan Otomatis 30 Hari --}}
            <div class="bg-white/90 lg:bg-ich-surface border border-ich-line rounded-ich-lg p-3.5 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-ich-teal shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="font-sans text-[11.5px] text-ich-ink-600 leading-relaxed space-y-1">
                    <p>
                        Kode OTP berlaku tetap selama <span class="font-bold text-ich-ink-800">10 menit</span> (tidak berubah saat refresh atau login ulang) dengan batas pengiriman maksimal <span class="font-bold text-ich-ink-800">{{ $maksKuotaHarian }} kali per 24 jam</span> (sisa <span class="font-bold {{ $sisaKuotaHarian > 0 ? 'text-ich-teal' : 'text-ich-error' }}">{{ $sisaKuotaHarian }} kali</span>).
                    </p>
                    <p>
                        Harap verifikasi akun Anda (tersisa <span class="font-bold text-ich-teal">{{ $daysRemaining }} hari</span> sebelum akun yang belum terverifikasi dihapus otomatis dalam 30 hari).
                    </p>
                </div>
            </div>

            {{-- Tombol Verifikasi --}}
            <button type="submit"
                    :disabled="fullCode.length < 6"
                    :class="fullCode.length === 6
                        ? 'bg-ich-teal hover:bg-ich-teal-dark cursor-pointer'
                        : 'bg-ich-ink-300 cursor-not-allowed opacity-80'"
                    class="w-full h-[46px] text-white font-sans font-bold text-[14px]
                           rounded-ich-lg border-none flex items-center justify-center
                           shadow-ich-btn transition-colors">
                Verifikasi Akun
            </button>
        </form>

        {{-- Form Kirim Ulang OTP & Opsi Ubah Nomor WhatsApp --}}
        <div class="flex flex-col items-center gap-2.5 mt-1">
            <form method="POST" action="{{ route('otp.resend') }}" class="w-full flex flex-col gap-2.5">
                @csrf

                {{-- Toggle Input Nomor HP Baru jika salah ketik saat registrasi --}}
                <div x-show="editPhone" x-cloak class="bg-white/95 lg:bg-ich-surface border border-ich-line rounded-ich-lg p-3 flex flex-col gap-1.5">
                    <label for="resend_no_hp" class="font-ui font-bold text-[11.5px] text-ich-ink-700">
                        Perbarui Nomor WhatsApp
                    </label>
                    <input id="resend_no_hp"
                           type="text"
                           name="no_hp"
                           :disabled="!editPhone"
                           value="{{ old('no_hp', $user->no_hp) }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full h-[40px] px-3 bg-white border border-ich-teal rounded-ich-md
                                  font-sans text-[13px] text-ich-ink-900 focus:outline-none focus:ring-2 focus:ring-ich-teal/30">
                    @error('no_hp')
                        <p class="font-sans text-[11px] font-semibold text-ich-error">{{ $message }}</p>
                    @enderror
                    <p class="font-sans text-[11px] text-ich-ink-400">
                        Masukkan nomor WhatsApp yang benar lalu klik tombol di bawah untuk mengirim kode OTP baru.
                    </p>
                </div>

                <button type="submit"
                        :disabled="(!editPhone && cooldown > 0) || remainingSends <= 0"
                        :class="((!editPhone && cooldown > 0) || remainingSends <= 0)
                            ? 'bg-white/20 lg:bg-gray-100 text-white/80 lg:text-ich-ink-400 cursor-not-allowed'
                            : 'bg-ich-yellow hover:bg-ich-yellow-dark text-white cursor-pointer shadow-ich-btn'"
                        class="w-full h-[44px] font-sans font-bold text-[13px]
                               rounded-ich-lg border-none flex items-center justify-center transition-colors px-3 text-center">
                    <span x-show="remainingSends <= 0" x-cloak>
                        Batas Kirim 24 Jam Habis ({{ $maksKuotaHarian }}/{{ $maksKuotaHarian }}) — Reset dalam {{ $resetWaitTime ?? '24 jam' }}
                    </span>
                    <span x-show="remainingSends > 0 && editPhone" x-cloak>
                        Simpan Nomor & Kirim Ulang OTP (Sisa {{ $sisaKuotaHarian }}/{{ $maksKuotaHarian }})
                    </span>
                    <span x-show="remainingSends > 0 && !editPhone && cooldown <= 0">
                        Kirim Ulang Kode OTP (Sisa {{ $sisaKuotaHarian }}/{{ $maksKuotaHarian }})
                    </span>
                    <span x-show="remainingSends > 0 && !editPhone && cooldown > 0" x-cloak>
                        Kirim Ulang Kode (<span x-text="cooldown"></span>d)
                    </span>
                </button>
            </form>

            {{-- Link Toggle Salah Nomor HP & Logout --}}
            <div class="flex flex-wrap items-center justify-center gap-3 mt-0.5">
                <button type="button"
                        @click="editPhone = !editPhone"
                        class="bg-transparent border-none cursor-pointer font-sans font-bold text-[12px]
                               text-white lg:text-ich-teal hover:underline">
                    <span x-text="editPhone ? 'Batal Ubah Nomor WhatsApp' : 'Salah Nomor WhatsApp? Ubah Nomor'"></span>
                </button>

                <span class="text-white/60 lg:text-ich-ink-300 text-[12px]">|</span>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="bg-transparent border-none cursor-pointer font-sans font-bold text-[12px]
                                   text-white lg:text-ich-ink-500 hover:lg:text-ich-teal underline">
                        Keluar / Gunakan Akun Lain
                    </button>
                </form>
            </div>
        </div>

        {{-- Mobile-only: spacer + copyright --}}
        <div class="lg:hidden flex-1"></div>
        <div class="lg:hidden text-center font-sans text-[10px] text-white py-3" style="opacity:.9">
            Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.
        </div>

    </div>
</div>

</x-guest-layout>
