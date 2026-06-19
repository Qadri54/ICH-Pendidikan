<?php

namespace Tests\Browser;

use App\Models\StudentReportCard;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminRaportTest extends DuskTestCase
{
    public function test_admin_can_view_raport_list(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/raport')
                ->waitForText('Raport')
                ->assertSee('Raport')
                ->screenshot('admin-raport/01-raport-list');
        });
    }

    public function test_admin_can_view_raport_detail(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();
        $reportCard = StudentReportCard::first();

        if (!$reportCard) {
            $this->markTestSkipped('No report cards in database');
        }

        $this->browse(function (Browser $browser) use ($admin, $reportCard) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit("/admin/raport/{$reportCard->report_card_id}/edit")
                ->assertPathIs("/admin/raport/{$reportCard->report_card_id}/edit")
                ->screenshot('admin-raport/02-raport-detail');
        });
    }

    public function test_admin_can_view_raport_narrative_section(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();
        $reportCard = StudentReportCard::first();

        if (!$reportCard) {
            $this->markTestSkipped('No report cards in database');
        }

        $this->browse(function (Browser $browser) use ($admin, $reportCard) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit("/admin/raport/{$reportCard->report_card_id}/edit")
                ->waitForText('Nilai Agama')
                ->assertSee('Nilai Agama')
                ->screenshot('admin-raport/03-narrative-section');
        });
    }

    public function test_admin_can_open_create_raport_form(): void
    {
        $admin = User::where('email', 'admin@iqra.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin->user_id, 'web')
                ->visit('/admin/raport/create')
                ->assertPathIs('/admin/raport/create')
                ->screenshot('admin-raport/04-create-form');
        });
    }
}
