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
        User::factory()->orangTua()->create(['email' => 'ortu@ich.test']);

        $this->post('/login', ['email' => 'ortu@ich.test', 'password' => 'password'])
             ->assertRedirect('/beranda');
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
    public function register_berhasil_membuat_user_dengan_role_orang_tua(): void
    {
        $this->post('/register', [
            'name'                  => 'Ibu Siti',
            'email'                 => 'siti@ich.test',
            'no_hp'                 => '081234567890',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect('/beranda');

        $this->assertDatabaseHas('users', ['email' => 'siti@ich.test']);

        $user = User::where('email', 'siti@ich.test')->first();
        $this->assertEquals('Orang Tua', $user->role?->role_name);
        $this->assertAuthenticatedAs($user);
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
        $this->actingAs(User::factory()->admin()->create());

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
