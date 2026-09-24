<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    // ── LOGIN ──────────────────────────────────────────────────────────────────

    #[Test]
    public function halaman_login_bisa_diakses(): void
    {
        $this->get('/login')->assertOk();
    }

    #[Test]
    public function login_berhasil_dengan_kredensial_valid(): void
    {
        $user = User::factory()->admin()->create(['email' => 'admin@ich.test']);

        $this->post('/login', ['email' => 'admin@ich.test', 'password' => 'password'])
             ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_gagal_dengan_password_salah(): void
    {
        User::factory()->admin()->create(['email' => 'admin@ich.test']);

        $this->post('/login', ['email' => 'admin@ich.test', 'password' => 'salah'])
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function login_gagal_dengan_email_tidak_terdaftar(): void
    {
        $this->post('/login', ['email' => 'tidakada@ich.test', 'password' => 'password'])
             ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function orang_tua_diarahkan_ke_beranda_setelah_login(): void
    {
        $user = User::factory()->orangTua()->create(['email' => 'ortu@ich.test', 'status' => 'active']);

        // POST /login selalu diarahkan ke /dashboard oleh Laravel Breeze
        $this->post('/login', ['email' => 'ortu@ich.test', 'password' => 'password'])
             ->assertRedirect('/dashboard');
             
        // Dashboard Controller bertugas meredirect Orang Tua ke /beranda
        $this->actingAs($user)->get('/dashboard')->assertRedirect('/beranda');
    }

    #[Test]
    public function admin_diarahkan_ke_dashboard_setelah_login(): void
    {
        User::factory()->admin()->create(['email' => 'admin2@ich.test']);

        $this->post('/login', ['email' => 'admin2@ich.test', 'password' => 'password'])
             ->assertRedirect('/dashboard');
    }

    // ── REGISTER ───────────────────────────────────────────────────────────────

    #[Test]
    public function halaman_register_bisa_diakses(): void
    {
        $this->get('/register')->assertOk();
    }

    #[Test]
    public function register_berhasil_membuat_user_dengan_role_orang_tua_dan_mengarahkan_ke_otp(): void
    {
        $this->post('/register', [
            'name'                  => 'Ibu Siti',
            'email'                 => 'siti@ich.test',
            'no_hp'                 => '081234567890',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect('/verify-otp');

        $this->assertDatabaseHas('users', [
            'email'    => 'siti@ich.test',
            'verified' => false,
        ]);

        $user = User::where('email', 'siti@ich.test')->first();
        $this->assertEquals('Orang Tua', $user->role?->role_name);
        $this->assertNotNull($user->otp);
        $this->assertAuthenticatedAs($user);

        // Verifikasi kode OTP
        $this->actingAs($user)
            ->post('/verify-otp', ['otp_code' => $user->otp->otp_code])
            ->assertRedirect('/beranda');

        $this->assertTrue($user->fresh()->verified);
        $this->assertDatabaseMissing('otps', ['user_id' => $user->user_id]);
    }

    #[Test]
    public function user_belum_terverifikasi_login_ulang_otomatis_generate_otp_baru(): void
    {
        $user = User::factory()->orangTua()->create([
            'email'    => 'unverified@ich.test',
            'verified' => false,
        ]);

        // Login pertama kali (belum ada OTP) -> otomatis generate OTP ke-1
        $this->post('/login', [
            'email'    => 'unverified@ich.test',
            'password' => 'password',
        ])->assertRedirect('/verify-otp');

        $firstOtp = $user->fresh()->otp;
        $this->assertNotNull($firstOtp);
        $this->assertEquals(1, $firstOtp->send_count);
        $firstCode = $firstOtp->otp_code;

        // Refresh halaman /verify-otp dalam waktu < 10 menit -> kode OTP & send_count TETAP (hemat token Fonnte)
        $this->actingAs($user)
            ->get('/verify-otp')
            ->assertOk();
        $this->assertEquals($firstCode, $user->fresh()->otp->otp_code);
        $this->assertEquals(1, $user->fresh()->otp->send_count);

        // Logout lalu login ulang dalam waktu < 10 menit -> kode OTP & send_count TETAP
        $this->post('/logout');
        $this->post('/login', [
            'email'    => 'unverified@ich.test',
            'password' => 'password',
        ])->assertRedirect('/verify-otp');

        $this->assertEquals($firstCode, $user->fresh()->otp->otp_code);
        $this->assertEquals(1, $user->fresh()->otp->send_count);

        // Jika sudah lewat > 10 menit (expired), login ulang otomatis generate kode OTP baru (send_count = 2)
        $user->otp()->update(['expires_at' => now()->subMinute()]);
        $this->post('/logout');
        $this->post('/login', [
            'email'    => 'unverified@ich.test',
            'password' => 'password',
        ])->assertRedirect('/verify-otp');

        $this->assertEquals(2, $user->fresh()->otp->send_count);
    }

    #[Test]
    public function user_bisa_ubah_nomor_hp_dan_kirim_ulang_otp(): void
    {
        $user = User::factory()->orangTua()->create([
            'email'    => 'wrongphone@ich.test',
            'no_hp'    => '081234567801',
            'verified' => false,
        ]);

        $otpService = app(\App\Services\Auth\OtpService::class);
        $firstOtp   = $otpService->generateAndSend($user);
        $this->assertEquals(1, $firstOtp->send_count);

        // User memperbaiki nomor HP dan klik Kirim Ulang OTP (meskipun masih < 10 menit)
        $this->actingAs($user)
            ->post('/verify-otp/resend', [
                'no_hp' => '081234567899',
            ])
            ->assertRedirect();

        $this->assertEquals('081234567899', $user->fresh()->no_hp);
        $this->assertEquals(2, $user->fresh()->otp->send_count);
    }

    #[Test]
    public function user_belum_terverifikasi_lebih_dari_30_hari_otomatis_terhapus_saat_login(): void
    {
        $user = User::factory()->orangTua()->create([
            'email'      => 'expired30days@ich.test',
            'verified'   => false,
            'created_at' => now()->subDays(31),
        ]);

        $this->post('/login', [
            'email'    => 'expired30days@ich.test',
            'password' => 'password',
        ])->assertRedirect('/login');

        $this->assertDatabaseMissing('users', ['user_id' => $user->user_id]);
        $this->assertGuest();
    }

    #[Test]
    public function pengiriman_otp_dibatasi_maksimal_4_kali_per_user_dalam_24_jam(): void
    {
        $user = User::factory()->orangTua()->create([
            'email'    => 'limit4@ich.test',
            'verified' => false,
        ]);

        $otpService = app(\App\Services\Auth\OtpService::class);

        // Pengiriman ke-1 s/d ke-4 (kirim ulang / forceNew) dalam 24 jam berhasil
        for ($i = 1; $i <= 4; $i++) {
            $otp = $otpService->generateAndSend($user, forceNew: true);
            $this->assertNotNull($otp, "Pengiriman ke-{$i} seharusnya berhasil.");
            $this->assertEquals($i, $otp->send_count);
        }

        $this->assertFalse($otpService->canSendOtp($user));
        $this->assertEquals(0, $otpService->getRemainingDailySends($user));

        // Pengiriman ke-5 dalam 24 jam ditolak (null)
        $fifthAttempt = $otpService->generateAndSend($user, forceNew: true);
        $this->assertNull($fifthAttempt);

        // Setelah lewat 24 jam, kuota otomatis ter-reset kembali ke 1/4
        $user->otp()->update(['send_window_started_at' => now()->subHours(25)]);
        $this->assertTrue($otpService->canSendOtp($user));
        $resetOtp = $otpService->generateAndSend($user, forceNew: true);
        $this->assertNotNull($resetOtp);
        $this->assertEquals(1, $resetOtp->send_count);
    }

    #[Test]
    public function register_gagal_jika_email_sudah_terdaftar(): void
    {
        User::factory()->create(['email' => 'existing@ich.test']);

        $this->post('/register', [
            'name'                  => 'Nama Lain',
            'email'                 => 'existing@ich.test',
            'no_hp'                 => '081234567891',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertSessionHasErrors('email');
    }

    #[Test]
    public function register_gagal_jika_password_tidak_cocok(): void
    {
        $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@ich.test',
            'no_hp'                 => '081234567892',
            'password'              => 'Password123!',
            'password_confirmation' => 'BedaPassword!',
        ])->assertSessionHasErrors('password');
    }

    #[Test]
    public function register_gagal_jika_format_no_hp_salah(): void
    {
        $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test2@ich.test',
            'no_hp'                 => '12345',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertSessionHasErrors('no_hp');
    }

    // ── LOGOUT ─────────────────────────────────────────────────────────────────

    #[Test]
    public function user_bisa_logout(): void
    {
        $this->actingAs(User::factory()->admin()->create(['status' => 'active']));

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    // ── PROTEKSI HALAMAN ───────────────────────────────────────────────────────

    #[Test]
    public function dashboard_tidak_bisa_diakses_tanpa_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    #[Test]
    public function user_login_tidak_bisa_akses_halaman_login_lagi(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/login')->assertRedirect();
    }
}
