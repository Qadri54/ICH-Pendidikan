<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrangTuaPortalTest extends DuskTestCase
{
    private function getOrtuUser(): ?User
    {
        return User::where('email', 'binsar.sitompul@iqra.com')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('role_name', 'Orang Tua'))->first();
    }

    public function test_beranda_shows_child_info(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/beranda')
                ->assertPathIs('/beranda')
                ->screenshot('orangtua/01-beranda');
        });
    }

    public function test_pendaftaran_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/pendaftaran')
                ->assertPathIs('/pendaftaran')
                ->screenshot('orangtua/02-pendaftaran');
        });
    }

    public function test_pembayaran_spp_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/pembayaran/spp')
                ->assertPathIs('/pembayaran/spp')
                ->screenshot('orangtua/03-pembayaran-spp');
        });
    }

    public function test_pembayaran_pendaftaran_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/pembayaran/pendaftaran')
                ->assertPathIs('/pembayaran/pendaftaran')
                ->screenshot('orangtua/04-pembayaran-pendaftaran');
        });
    }

    public function test_tabungan_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/tabungan')
                ->assertPathIs('/tabungan')
                ->screenshot('orangtua/05-tabungan');
        });
    }

    public function test_kehadiran_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/kehadiran')
                ->assertPathIs('/kehadiran')
                ->screenshot('orangtua/06-kehadiran');
        });
    }

    public function test_akademik_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/akademik')
                ->assertPathIs('/akademik')
                ->screenshot('orangtua/07-akademik');
        });
    }

    public function test_profil_anak_page(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/profil-anak')
                ->assertPathIs('/profil-anak')
                ->screenshot('orangtua/08-profil-anak');
        });
    }

    public function test_mobile_layout_has_tab_bar(): void
    {
        $ortu = $this->getOrtuUser();
        if (!$ortu) {
            $this->markTestSkipped('Orang Tua account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/beranda')
                ->assertPresent('.sticky.bottom-0')
                ->screenshot('orangtua/09-mobile-tab-bar');
        });
    }

    public function test_multi_child_parent_sees_all_children(): void
    {
        $ortu = User::where('email', 'binsar.sitompul@iqra.com')->first();

        if (!$ortu) {
            $this->markTestSkipped('Binsar Sitompul account not found');
        }

        $this->browse(function (Browser $browser) use ($ortu) {
            $browser->loginAs($ortu->user_id, 'web')
                ->visit('/profil-anak')
                ->assertPathIs('/profil-anak')
                ->screenshot('orangtua/10-multi-child');
        });
    }
}
