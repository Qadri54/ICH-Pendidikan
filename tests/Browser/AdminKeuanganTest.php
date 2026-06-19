<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminKeuanganTest extends DuskTestCase
{
    public function test_admin_can_view_spp_invoices(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/keuangan')
                ->waitForText('Keuangan')
                ->assertSee('Keuangan')
                ->screenshot('admin-keuangan/01-spp-list');
        });
    }

    public function test_admin_can_open_create_invoice_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/keuangan/create')
                ->assertPathIs('/admin/keuangan/create')
                ->screenshot('admin-keuangan/02-create-form');
        });
    }

    public function test_admin_can_view_payment_proof(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/keuangan/bukti-pembayaran')
                ->assertPathIs('/admin/keuangan/bukti-pembayaran')
                ->screenshot('admin-keuangan/03-bukti-pembayaran');
        });
    }
}
