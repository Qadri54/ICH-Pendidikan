<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminSiswaTest extends DuskTestCase
{
    public function test_admin_can_view_student_list(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/siswa')
                ->waitForText('Siswa')
                ->assertSee('Siswa')
                ->screenshot('admin-siswa/01-student-list');
        });
    }

    public function test_admin_can_view_student_detail(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/siswa')
                ->waitForText('Siswa')
                ->press('Detail')
                ->pause(500)
                ->screenshot('admin-siswa/02-student-detail');
        });
    }

    public function test_admin_can_open_create_student_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/siswa/create')
                ->assertPathIs('/admin/siswa/create')
                ->screenshot('admin-siswa/03-create-form');
        });
    }
}
