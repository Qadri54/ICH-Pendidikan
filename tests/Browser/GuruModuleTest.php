<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GuruModuleTest extends DuskTestCase
{
    public function test_guru_can_view_absensi_siswa(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/guru/absensi-siswa')
                ->assertPathIs('/guru/absensi-siswa')
                ->screenshot('guru/01-absensi-siswa');
        });
    }

    public function test_guru_can_view_absensi_guru(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/guru/absensi-guru')
                ->assertPathIs('/guru/absensi-guru')
                ->screenshot('guru/02-absensi-guru');
        });
    }

    public function test_guru_can_view_tabungan(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/guru/tabungan')
                ->assertPathIs('/guru/tabungan')
                ->screenshot('guru/03-tabungan');
        });
    }

    public function test_guru_can_view_raport(): void
    {
        $guru = User::where('email', 'guru@iqra.com')->first();

        if (!$guru) {
            $this->markTestSkipped('guru@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guru) {
            $browser->loginAs($guru->user_id, 'web')
                ->visit('/guru/raport')
                ->assertPathIs('/guru/raport')
                ->screenshot('guru/04-raport');
        });
    }

    public function test_guru_ngaji_can_access_absensi(): void
    {
        $guruNgaji = User::where('email', 'guruNgaji@iqra.com')->first();

        if (!$guruNgaji) {
            $this->markTestSkipped('guruNgaji@iqra.com not found');
        }

        $this->browse(function (Browser $browser) use ($guruNgaji) {
            $browser->loginAs($guruNgaji->user_id, 'web')
                ->visit('/guru/absensi-guru')
                ->assertPathIs('/guru/absensi-guru')
                ->screenshot('guru/05-guru-ngaji-absensi');
        });
    }
}
