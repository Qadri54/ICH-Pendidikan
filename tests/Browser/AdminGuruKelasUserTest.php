<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminGuruKelasUserTest extends DuskTestCase
{
    public function test_admin_can_view_guru_list(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/guru')
                ->waitForText('Guru')
                ->assertSee('Guru')
                ->screenshot('admin-guru-kelas-user/01-guru-list');
        });
    }

    public function test_admin_can_open_create_guru_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/guru/create')
                ->assertPathIs('/admin/guru/create')
                ->screenshot('admin-guru-kelas-user/02-guru-create');
        });
    }

    public function test_admin_can_view_kelas_list(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/kelas')
                ->waitForText('Kelas')
                ->assertSee('Kelas')
                ->screenshot('admin-guru-kelas-user/03-kelas-list');
        });
    }

    public function test_admin_can_open_create_kelas_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/kelas/create')
                ->assertPathIs('/admin/kelas/create')
                ->screenshot('admin-guru-kelas-user/04-kelas-create');
        });
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/user')
                ->waitForText('User')
                ->assertSee('User')
                ->screenshot('admin-guru-kelas-user/05-user-list');
        });
    }

    public function test_admin_can_open_create_user_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/user/create')
                ->assertPathIs('/admin/user/create')
                ->screenshot('admin-guru-kelas-user/06-user-create');
        });
    }
}
