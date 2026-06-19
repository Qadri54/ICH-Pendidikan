<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    public function test_admin_dashboard_shows_statistics(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/dashboard')
                ->waitForText('Dashboard')
                ->assertSee('Dashboard')
                ->screenshot('dashboard/01-admin-dashboard');
        });
    }

    public function test_admin_dashboard_greeting_banner(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/dashboard')
                ->waitForText($admin->name)
                ->assertSee($admin->name)
                ->screenshot('dashboard/02-greeting-banner');
        });
    }

    public function test_guru_dashboard(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/dashboard')
                ->pause(1000)
                ->screenshot('dashboard/03-guru-dashboard');
        });
    }

    public function test_orangtua_beranda(): void
    {
        $ortu = User::where('email', 'like', '%sitompul%')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('role_name', 'Orang Tua'))->first();

        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/beranda')
                ->pause(1000)
                ->screenshot('dashboard/04-orangtua-beranda');
        });
    }
}
