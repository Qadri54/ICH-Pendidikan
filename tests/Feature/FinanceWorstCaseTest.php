<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\SavingLedger;
use App\Models\StudentPassbook;
use App\Models\ClassRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinanceWorstCaseTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $orangTua;
    private User $orangTuaLain;
    private Student $student;
    private Student $studentLain;
    private ClassRoom $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create(['status' => 'active']);
        $this->admin->markEmailAsVerified();

        $this->orangTua = User::factory()->orangTua()->create(['status' => 'active']);
        $this->orangTua->markEmailAsVerified();

        $this->orangTuaLain = User::factory()->orangTua()->create(['status' => 'active']);
        $this->orangTuaLain->markEmailAsVerified();

        $this->kelas = ClassRoom::create(['nama_kelas' => 'TK A', 'nama_ruangan' => 'R1']);
        
        $this->student = Student::create([
            'user_id' => $this->orangTua->user_id,
            'class_id' => $this->kelas->class_id,
            'nama_siswa' => 'Budi',
            'NIS' => '123456',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-01-01',
            'tempat_lahir' => 'Jakarta',
            'nama_ayah' => 'Bapak Budi',
            'nama_ibu' => 'Ibu Budi',
            'status' => 'aktif'
        ]);

        $this->studentLain = Student::create([
            'user_id' => $this->orangTuaLain->user_id,
            'class_id' => $this->kelas->class_id,
            'nama_siswa' => 'Andi',
            'NIS' => '654321',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-01-02',
            'tempat_lahir' => 'Bandung',
            'nama_ayah' => 'Bapak Andi',
            'nama_ibu' => 'Ibu Andi',
            'status' => 'aktif'
        ]);
    }

    private function buatLedger(): SavingLedger
    {
        $teacher = \App\Models\Teacher::create([
            'user_id' => $this->admin->user_id,
            'NIP' => '123456789',
            'tipe' => 'Guru TK',
            'hire_date' => '2020-01-01',
        ]);

        return SavingLedger::create([
            'teacher_id' => $teacher->teacher_id,
            'ledger_name' => 'Tabungan Wajib',
            'academic_year' => '2026-01-01',
            'opening_date' => now(),
            'status' => 'Active'
        ]);
    }

    #[Test]
    public function manipulasi_nominal_negatif_saat_setor_tabungan_akan_ditolak(): void
    {
        $ledger = $this->buatLedger();
        $passbook = StudentPassbook::create([
            'student_id' => $this->student->student_id,
            'ledger_id' => $ledger->ledger_id,
            'opening_balance' => 0,
            'current_balance' => 50000,
            'opening_date' => now(),
        ]);

        // Hacker mencoba deposit -50000 agar saldo server kacau
        $this->actingAs($this->admin)
             ->post("/admin/tabungan/passbooks/{$passbook->passbook_id}/deposit", [
                 'amount' => -50000,
                 'description' => 'Hacking',
                 'transaction_date' => now()->toDateString(),
             ])
             ->assertSessionHasErrors('amount'); // Harus tertolak validasi min:1000

        // Saldo tidak boleh berubah
        $this->assertDatabaseHas('student_passbooks', [
            'passbook_id' => $passbook->passbook_id,
            'current_balance' => 50000, // Tetap utuh
        ]);
    }

    #[Test]
    public function orang_tua_tidak_bisa_membayar_spp_anak_orang_lain(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('bukti.jpg');

        $invoiceAnakLain = SppInvoice::create([
            'student_id' => $this->studentLain->student_id,
            'tanggal_tahun' => '2026-08-01',
            'jumlah' => 300000,
            'jatuh_tempo' => '2026-08-10',
            'status' => 'unpaid',
        ]);

        // Orang Tua Budi mencoba akses dan bayar invoice milik Andi (IDOR Attack)
        $this->actingAs($this->orangTua)
             ->post('/pembayaran/spp/' . $invoiceAnakLain->invoice_id, [
                 'jumlah_bayar' => 300000,
                 'gambar_bukti_pembayaran' => $file,
             ])
             ->assertForbidden(); // Sistem harus memblokirnya dengan abort(403)
    }

    #[Test]
    public function orang_tua_dilarang_menyetujui_pembayarannya_sendiri(): void
    {
        $invoice = SppInvoice::create([
            'student_id' => $this->student->student_id,
            'tanggal_tahun' => '2026-08-01',
            'jumlah' => 300000,
            'jatuh_tempo' => '2026-08-10',
            'status' => 'pending',
        ]);

        $payment = SppPayment::create([
            'student_id' => $this->student->student_id,
            'invoice_id' => $invoice->invoice_id,
            'jumlah_bayar' => 300000,
            'gambar_bukti_pembayaran' => 'fake/path.jpg',
            'payment_date' => now(),
            'status' => 'pending'
        ]);

        // Orang tua memaksa kirim request ke URL admin untuk approve pembayaran
        $this->actingAs($this->orangTua)
             ->post('/admin/keuangan/pembayaran/' . $payment->payment_id . '/approve')
             ->assertForbidden();

        // Status pembayaran di database tidak boleh berubah menjadi approved
        $this->assertDatabaseHas('spp_payments', [
            'payment_id' => $payment->payment_id,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function sistem_menolak_upload_script_berbahaya_sebagai_bukti_bayar(): void
    {
        Storage::fake('public');

        $invoice = SppInvoice::create([
            'student_id' => $this->student->student_id,
            'tanggal_tahun' => '2026-08-01',
            'jumlah' => 300000,
            'jatuh_tempo' => '2026-08-10',
            'status' => 'unpaid',
        ]);

        // Hacker membuat file palsu berekstensi .php yang berbahaya
        $maliciousFile = UploadedFile::fake()->create('shell.php', 100, 'text/x-php');

        $this->actingAs($this->orangTua)
             ->post('/pembayaran/spp/' . $invoice->invoice_id, [
                 'jumlah_bayar' => 300000,
                 'gambar_bukti_pembayaran' => $maliciousFile,
             ])
             ->assertSessionHasErrors('gambar_bukti_pembayaran'); // Gagal validasi image
    }
}

