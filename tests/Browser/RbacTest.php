<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RbacTest extends DuskTestCase
{
    public function test_admin_can_access_admin_pages(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/siswa')
                ->assertPathIs('/admin/siswa')
                ->assertDontSee('403')
                ->screenshot('rbac/01-admin-access-siswa');
        });
    }

    public function test_admin_can_access_all_admin_modules(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web');

            $pages = [
                '/admin/siswa'      => 'rbac/02-admin-siswa',
                '/admin/guru'       => 'rbac/02-admin-guru',
                '/admin/kelas'      => 'rbac/02-admin-kelas',
                '/admin/user'       => 'rbac/02-admin-user',
                '/admin/keuangan'   => 'rbac/02-admin-keuangan',
                '/admin/raport'     => 'rbac/02-admin-raport',
                '/admin/pengaturan' => 'rbac/02-admin-pengaturan',
            ];

            foreach ($pages as $path => $screenshotName) {
                $browser->visit($path)
                    ->assertPathIs($path)
                    ->assertDontSee('403')
                    ->screenshot($screenshotName);
            }
        });
    }

    public function test_kepala_sekolah_can_read_admin_area(): void
    {
        $kepsek = User::where('email', 'kepsek@iqra.com')->first();

        if (!$kepsek) {
            $this->markTestSkipped('kepsek@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($kepsek) {
            $browser->loginAs($kepsek->user_id, 'web')
                ->visit('/admin/siswa')
                ->assertPathIs('/admin/siswa')
                ->assertDontSee('403')
                ->screenshot('rbac/03-kepsek-read-siswa');
        });
    }

    public function test_guru_cannot_access_admin_area(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/admin/siswa')
                ->assertSee('403')
                ->screenshot('rbac/04-guru-blocked-admin');
        });
    }

    public function test_guru_can_access_guru_pages(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/guru/absensi-siswa')
                ->assertPathIs('/guru/absensi-siswa')
                ->assertDontSee('403')
                ->screenshot('rbac/05-guru-access-absensi');
        });
    }

    public function test_orangtua_can_access_mobile_portal(): void
    {
        $ortu = User::where('email', 'binsar.sitompul@iqra.com')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('role_name', 'Orang Tua'))->first();

        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/beranda')
                ->assertPathIs('/beranda')
                ->assertDontSee('403')
                ->screenshot('rbac/06-ortu-beranda');
        });
    }

    public function test_orangtua_cannot_access_admin_area(): void
    {
        $ortu = User::whereHas('role', fn ($q) => $q->where('role_name', 'Orang Tua'))->first();

        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/admin/siswa')
                ->assertSee('403')
                ->screenshot('rbac/07-ortu-blocked-admin');
        });
    }

    public function test_guest_cannot_access_admin_area(): void
    {
        $guest = User::where('email', 'guest@iqra.com')->first();

        if (!$guest) {
            $this->markTestSkipped('guest@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guest) {
            $browser->loginAs($guest->user_id, 'web')
                ->visit('/admin/siswa')
                ->assertSee('403')
                ->screenshot('rbac/08-guest-blocked-admin');
        });
    }
}
