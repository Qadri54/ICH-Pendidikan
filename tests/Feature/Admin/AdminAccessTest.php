<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Memastikan semua route admin:
 *  - Mengarahkan tamu ke halaman login
 *  - Menolak user yang bukan Admin (403)
 *  - Dapat diakses oleh Admin (200)
 */
class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $orangTua;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = User::factory()->admin()->create();
        $this->orangTua = User::factory()->orangTua()->create();
    }

    private function adminGetRoutes(): array
    {
        return [
            '/admin/siswa',
            '/admin/siswa/create',
            '/admin/kelas',
            '/admin/kelas/create',
            '/admin/guru',
            '/admin/guru/create',
            '/admin/user',
            '/admin/user/create',
            '/admin/pendaftaran',
            '/admin/keuangan',
            '/admin/keuangan/create',
            '/admin/tabungan',
            '/admin/tabungan/create',
            '/admin/laporan',
            '/admin/pengaturan',
        ];
    }

    #[Test]
    public function tamu_diarahkan_ke_login_untuk_semua_route_admin(): void
    {
        foreach ($this->adminGetRoutes() as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    #[Test]
    public function orang_tua_tidak_bisa_akses_area_admin(): void
    {
        $this->actingAs($this->orangTua);

        foreach ($this->adminGetRoutes() as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    #[Test]
    public function admin_bisa_akses_semua_halaman_admin(): void
    {
        $this->actingAs($this->admin);

        foreach ($this->adminGetRoutes() as $url) {
            $this->get($url)->assertOk();
        }
    }

    #[Test]
    public function admin_bisa_akses_dashboard(): void
    {
        $this->actingAs($this->admin);
        $this->get('/dashboard')->assertOk();
    }
}
