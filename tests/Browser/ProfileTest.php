<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileTest extends DuskTestCase
{
    public function test_admin_can_view_profile(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/profile')
                ->assertPathIs('/profile')
                ->assertSee($admin->name)
                ->screenshot('profile/01-view-profile');
        });
    }

    public function test_orangtua_can_view_profile_via_pengaturan(): void
    {
        $ortu = User::whereHas('role', fn ($q) => $q->where('role_name', 'Orang Tua'))->first();

        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/pengaturan')
                ->assertPathIs('/pengaturan')
                ->screenshot('profile/02-orangtua-pengaturan');
        });
    }
}
