<?php

namespace Tests\Feature\Admin;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test CRUD operasi untuk modul Admin: Siswa, Kelas, User
 */
class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    // ── KELAS ──────────────────────────────────────────────────────────────────

    #[Test]
    public function admin_bisa_melihat_daftar_kelas(): void
    {
        ClassRoom::create(['nama_kelas' => 'Kelas 1A', 'nama_ruangan' => 'Ruang Merah']);

        $this->get('/admin/kelas')->assertOk()->assertSee('Kelas 1A');
    }

    #[Test]
    public function admin_bisa_membuat_kelas_baru(): void
    {
        $this->post('/admin/kelas', [
            'nama_kelas'   => 'Kelas 2B',
            'nama_ruangan' => 'Ruang Biru',
        ])->assertRedirect('/admin/kelas');

        $this->assertDatabaseHas('classes', ['nama_kelas' => 'Kelas 2B']);
    }

    #[Test]
    public function pembuatan_kelas_gagal_jika_nama_kosong(): void
    {
        $this->post('/admin/kelas', ['nama_kelas' => '', 'nama_ruangan' => 'R1'])
             ->assertSessionHasErrors('nama_kelas');
    }

    #[Test]
    public function admin_bisa_mengubah_kelas(): void
    {
        $kelas = ClassRoom::create(['nama_kelas' => 'Kelas 3A', 'nama_ruangan' => 'Lama']);

        $this->put("/admin/kelas/{$kelas->class_id}", [
            'nama_kelas'   => 'Kelas 3A Update',
            'nama_ruangan' => 'Baru',
        ])->assertRedirect('/admin/kelas');

        $this->assertDatabaseHas('classes', ['nama_kelas' => 'Kelas 3A Update']);
    }

    #[Test]
    public function admin_bisa_menghapus_kelas_yang_kosong(): void
    {
        $kelas = ClassRoom::create(['nama_kelas' => 'Kelas Hapus', 'nama_ruangan' => 'R1']);

        $this->delete("/admin/kelas/{$kelas->class_id}")
             ->assertRedirect('/admin/kelas');

        $this->assertDatabaseMissing('classes', ['class_id' => $kelas->class_id]);
    }

    #[Test]
    public function kelas_tidak_bisa_dihapus_jika_masih_ada_siswa(): void
    {
        $kelas = ClassRoom::create(['nama_kelas' => 'Kelas Isi', 'nama_ruangan' => 'R2']);

        Student::create([
            'class_id' => $kelas->class_id, 'nama_siswa' => 'Test',
            'NIS' => '001', 'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-01-01', 'tempat_lahir' => 'Jakarta',
            'nama_ayah' => 'Ayah', 'nama_ibu' => 'Ibu',
        ]);

        $this->delete("/admin/kelas/{$kelas->class_id}")
             ->assertRedirect('/admin/kelas');

        $this->assertDatabaseHas('classes', ['class_id' => $kelas->class_id]);
    }

    // ── SISWA ──────────────────────────────────────────────────────────────────

    private function buatKelas(): ClassRoom
    {
        return ClassRoom::create(['nama_kelas' => 'Kelas Test', 'nama_ruangan' => 'R1']);
    }

    private function payloadSiswa(int $classId): array
    {
        return [
            'nama_siswa'    => 'Ahmad Tes',
            'NIS'           => '12345',
            'class_id'      => $classId,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-05-15',
            'tempat_lahir'  => 'Bandung',
            'nama_ayah'     => 'Bapak Ahmad',
            'nama_ibu'      => 'Ibu Ahmad',
        ];
    }

    #[Test]
    public function admin_bisa_melihat_daftar_siswa(): void
    {
        $kelas = $this->buatKelas();
        Student::create($this->payloadSiswa($kelas->class_id));

        $this->get('/admin/siswa')->assertOk()->assertSee('Ahmad Tes');
    }

    #[Test]
    public function admin_bisa_menambah_siswa(): void
    {
        $kelas = $this->buatKelas();

        $this->post('/admin/siswa', $this->payloadSiswa($kelas->class_id))
             ->assertRedirect('/admin/siswa');

        $this->assertDatabaseHas('students', ['NIS' => '12345']);
    }

    #[Test]
    public function penambahan_siswa_gagal_jika_nis_duplikat(): void
    {
        $kelas = $this->buatKelas();
        Student::create($this->payloadSiswa($kelas->class_id));

        $this->post('/admin/siswa', $this->payloadSiswa($kelas->class_id))
             ->assertSessionHasErrors('NIS');
    }

    #[Test]
    public function admin_bisa_melihat_detail_siswa(): void
    {
        $kelas = $this->buatKelas();
        $siswa = Student::create($this->payloadSiswa($kelas->class_id));

        $this->get("/admin/siswa/{$siswa->student_id}")
             ->assertOk()->assertSee('Ahmad Tes');
    }

    #[Test]
    public function admin_bisa_mengubah_data_siswa(): void
    {
        $kelas = $this->buatKelas();
        $siswa = Student::create($this->payloadSiswa($kelas->class_id));

        $update = $this->payloadSiswa($kelas->class_id);
        $update['nama_siswa'] = 'Ahmad Updated';

        $this->put("/admin/siswa/{$siswa->student_id}", $update)
             ->assertRedirect('/admin/siswa');

        $this->assertDatabaseHas('students', ['nama_siswa' => 'Ahmad Updated']);
    }

    #[Test]
    public function admin_bisa_menghapus_siswa(): void
    {
        $kelas = $this->buatKelas();
        $siswa = Student::create($this->payloadSiswa($kelas->class_id));

        $this->delete("/admin/siswa/{$siswa->student_id}")
             ->assertRedirect('/admin/siswa');

        $this->assertDatabaseMissing('students', ['student_id' => $siswa->student_id]);
    }

    // ── USER ───────────────────────────────────────────────────────────────────

    #[Test]
    public function admin_bisa_melihat_daftar_user(): void
    {
        $this->get('/admin/user')->assertOk()->assertSee($this->admin->name);
    }

    #[Test]
    public function admin_bisa_membuat_user_baru(): void
    {
        $this->post('/admin/user', [
            'name'                  => 'User Baru',
            'email'                 => 'userbaru@test.com',
            'no_hp'                 => '081298765432',
            'role_name'             => 'Orang Tua',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect('/admin/user');

        $this->assertDatabaseHas('users', ['email' => 'userbaru@test.com']);

        $user = User::where('email', 'userbaru@test.com')->first();
        $this->assertEquals('Orang Tua', $user->role?->role_name);
    }

    #[Test]
    public function admin_tidak_bisa_menghapus_diri_sendiri(): void
    {
        $this->delete("/admin/user/{$this->admin->user_id}")
             ->assertRedirect('/admin/user');

        $this->assertDatabaseHas('users', ['user_id' => $this->admin->user_id]);
    }

    #[Test]
    public function admin_bisa_menghapus_user_lain(): void
    {
        $user = User::factory()->orangTua()->create();

        $this->delete("/admin/user/{$user->user_id}")
             ->assertRedirect('/admin/user');

        $this->assertDatabaseMissing('users', ['user_id' => $user->user_id]);
    }

    #[Test]
    public function pembuatan_user_gagal_jika_email_duplikat(): void
    {
        $this->post('/admin/user', [
            'name'                  => 'Duplikat',
            'email'                 => $this->admin->email,
            'no_hp'                 => '081200000001',
            'role_name'             => 'Orang Tua',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertSessionHasErrors('email');
    }
}
