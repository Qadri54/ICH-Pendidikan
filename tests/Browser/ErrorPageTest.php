<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ErrorPageTest extends DuskTestCase
{
    public function test_404_page_for_unknown_url(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/halaman-tidak-ada-xyz')
                ->assertSee('404')
                ->screenshot('error-pages/01-404');
        });
    }

    public function test_403_page_for_unauthorized_access(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/admin/siswa')
                ->assertSee('403')
                ->screenshot('error-pages/02-403');
        });
    }
}
