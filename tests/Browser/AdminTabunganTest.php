<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminTabunganTest extends DuskTestCase
{
    public function test_admin_can_view_tabungan_ledgers(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/tabungan')
                ->waitForText('Tabungan')
                ->assertSee('Tabungan')
                ->screenshot('admin-tabungan/01-ledger-list');
        });
    }

    public function test_admin_can_open_create_ledger_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/tabungan/create')
                ->assertPathIs('/admin/tabungan/create')
                ->screenshot('admin-tabungan/02-create-form');
        });
    }
}
