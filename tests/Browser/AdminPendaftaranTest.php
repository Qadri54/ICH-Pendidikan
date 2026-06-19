<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminPendaftaranTest extends DuskTestCase
{
    public function test_admin_can_view_pendaftaran_list(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/pendaftaran')
                ->waitForText('Pendaftaran')
                ->assertSee('Pendaftaran')
                ->screenshot('admin-pendaftaran/01-list');
        });
    }

    public function test_admin_can_view_pembayaran_pendaftaran(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/pembayaran-pendaftaran')
                ->assertPathIs('/admin/pembayaran-pendaftaran')
                ->screenshot('admin-pendaftaran/02-pembayaran');
        });
    }
}
