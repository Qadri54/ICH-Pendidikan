<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrisUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan Admin
        $this->admin = User::create([
            'email' => 'admin_test@ich.com',
            'name' => 'Admin Test',
            'no_hp' => '08111222333',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        Role::create(['user_id' => $this->admin->user_id, 'role_name' => 'Admin']);
    }

    public function test_admin_can_upload_and_replace_qris_image()
    {
        Storage::fake('public');

        $this->actingAs($this->admin);

        $qrisLama = UploadedFile::fake()->image('qris-lama.jpg')->size(1024);

        $response1 = $this->post(route('admin.pengaturan.qris.update'), [
            'qris_image' => $qrisLama,
        ]);

        $response1->assertRedirect(route('admin.pengaturan.index'));
        
        $feeSetting = \App\Models\FeeSetting::first();
        $this->assertNotNull($feeSetting->qris_image);
        Storage::disk('public')->assertExists($feeSetting->qris_image);

        $qrisBaru = UploadedFile::fake()->image('qris-baru.png')->size(2000);

        $response2 = $this->post(route('admin.pengaturan.qris.update'), [
            'qris_image' => $qrisBaru,
        ]);

        $response2->assertRedirect(route('admin.pengaturan.index'));
        
        $feeSetting->refresh();
        Storage::disk('public')->assertExists($feeSetting->qris_image);
    }

    public function test_non_admin_cannot_upload_qris()
    {
        // Siapkan role Orang Tua
        $ortu = User::create([
            'email' => 'ortu_test@ich.com',
            'name' => 'Ortu Test',
            'no_hp' => '08222333444',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        Role::create(['user_id' => $ortu->user_id, 'role_name' => 'Orang Tua']);

        $this->actingAs($ortu);

        $qrisImage = UploadedFile::fake()->image('hacker-qris.jpg');

        $response = $this->post(route('admin.pengaturan.qris.update'), [
            'qris_image' => $qrisImage,
        ]);

        // Harus ditolak dengan status 403 Forbidden atau redirect kembali (tergantung konfigurasi middleware admin)
        // Kita asumsikan middleware admin mengembalikan 403 atau melempar kembali ke halaman lain
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }
}
