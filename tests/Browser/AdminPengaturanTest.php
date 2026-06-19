<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminPengaturanTest extends DuskTestCase
{
    public function test_admin_can_view_pengaturan_page(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/pengaturan')
                ->waitForText('Pengaturan')
                ->assertSee('Pengaturan')
                ->screenshot('admin-pengaturan/01-index');
        });
    }

    public function test_admin_can_view_laporan_page(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/laporan')
                ->waitForText('Laporan')
                ->assertSee('Laporan')
                ->screenshot('admin-pengaturan/02-laporan');
        });
    }
}
