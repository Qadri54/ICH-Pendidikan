<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminAbsensiTest extends DuskTestCase
{
    public function test_admin_can_view_absensi_siswa(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/absensi')
                ->waitForText('Absensi')
                ->assertSee('Absensi')
                ->screenshot('admin-absensi/01-absensi-siswa');
        });
    }

    public function test_admin_can_view_recap_absensi_siswa(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/absensi/recap')
                ->assertPathIs('/admin/absensi/recap')
                ->screenshot('admin-absensi/02-recap-siswa');
        });
    }

    public function test_admin_can_view_absensi_guru(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/absensi-guru')
                ->assertPathIs('/admin/absensi-guru')
                ->screenshot('admin-absensi/03-absensi-guru');
        });
    }

    public function test_admin_can_view_recap_absensi_guru(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/absensi-guru/recap')
                ->assertPathIs('/admin/absensi-guru/recap')
                ->screenshot('admin-absensi/04-recap-guru');
        });
    }
}
