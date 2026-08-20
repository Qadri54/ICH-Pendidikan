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

class FinanceAndSavingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $orangTua;
    private Student $student;
    private ClassRoom $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan data dasar
        $this->admin = User::factory()->admin()->create(['status' => 'active']);
        $this->orangTua = User::factory()->orangTua()->create(['status' => 'active']);

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
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // SKENARIO KEUANGAN (SPP)
    // ────────────────────────────────────────────────────────────────────────

    #[Test]
    public function orang_tua_bisa_melihat_tagihan_spp_anaknya(): void
    {
        // Supaya user dianggap verified (agar tidak ditendang middleware verified)
        $this->orangTua->markEmailAsVerified();

        $invoice = SppInvoice::create([
            'student_id' => $this->student->student_id,
            'tanggal_tahun' => '2026-08-01',
            'jumlah' => 300000,
            'jatuh_tempo' => '2026-08-10',
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->orangTua)
             ->get('/pembayaran/spp/' . $this->student->student_id);

        $response->assertOk()->assertSee('300.000');
    }

    #[Test]
    public function orang_tua_bisa_mengunggah_bukti_bayar_spp(): void
    {
        $this->orangTua->markEmailAsVerified();
        Storage::fake('public');

        $invoice = SppInvoice::create([
            'student_id' => $this->student->student_id,
            'tanggal_tahun' => '2026-08-01',
            'jumlah' => 300000,
            'jatuh_tempo' => '2026-08-10',
            'status' => 'unpaid',
        ]);

        $file = UploadedFile::fake()->image('bukti.jpg');

        $this->actingAs($this->orangTua)
             ->post('/pembayaran/spp/' . $invoice->invoice_id, [
                 'jumlah_bayar' => 300000,
                 'gambar_bukti_pembayaran' => $file,
             ])
             ->assertRedirect(); // Bisa tambahkan assertSessionHas jika ada with('success')

        // Pastikan tabel spp_payments tersimpan
        $this->assertDatabaseHas('spp_payments', [
            'invoice_id' => $invoice->invoice_id,
            'jumlah_bayar' => 300000,
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // SKENARIO TABUNGAN
    // ────────────────────────────────────────────────────────────────────────

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
    public function admin_bisa_membuka_buku_tabungan_baru_untuk_siswa(): void
    {
        $this->admin->markEmailAsVerified();
        $ledger = $this->buatLedger();

        $this->actingAs($this->admin)
             ->post("/admin/tabungan/{$ledger->ledger_id}/passbooks", [
                 'student_ids' => [$this->student->student_id],
                 'opening_date' => now()->toDateString(),
                 'opening_balance' => 50000,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('student_passbooks', [
            'student_id' => $this->student->student_id,
            'ledger_id' => $ledger->ledger_id,
            'opening_balance' => 50000,
            'current_balance' => 50000,
        ]);
    }

    #[Test]
    public function admin_bisa_menyetor_uang_ke_tabungan_siswa(): void
    {
        $this->admin->markEmailAsVerified();
        $ledger = $this->buatLedger();
        $passbook = StudentPassbook::create([
            'student_id' => $this->student->student_id,
            'ledger_id' => $ledger->ledger_id,
            'opening_balance' => 0,
            'current_balance' => 100000,
            'opening_date' => now(),
        ]);

        $response = $this->actingAs($this->admin)
             ->post("/admin/tabungan/passbooks/{$passbook->passbook_id}/deposit", [
                 'amount' => 50000,
                 'description' => 'Setor tunai',
                 'transaction_date' => now()->toDateString(),
             ]);
             
        $response->assertRedirect();

        $this->assertDatabaseHas('student_passbooks', [
            'passbook_id' => $passbook->passbook_id,
            'current_balance' => 150000,
        ]);

        $this->assertDatabaseHas('saving_transactions', [
            'passbook_id' => $passbook->passbook_id,
            'transaction_type' => 'deposit',
            'amount' => 50000,
        ]);
    }

    #[Test]
    public function admin_bisa_menarik_uang_dari_tabungan_siswa(): void
    {
        $this->admin->markEmailAsVerified();
        $ledger = $this->buatLedger();
        $passbook = StudentPassbook::create([
            'student_id' => $this->student->student_id,
            'ledger_id' => $ledger->ledger_id,
            'opening_balance' => 0,
            'current_balance' => 200000,
            'opening_date' => now(),
        ]);

        $this->actingAs($this->admin)
             ->post("/admin/tabungan/passbooks/{$passbook->passbook_id}/withdraw", [
                 'amount' => 50000,
                 'description' => 'Tarik tunai',
                 'transaction_date' => now()->toDateString(),
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('student_passbooks', [
            'passbook_id' => $passbook->passbook_id,
            'current_balance' => 150000,
        ]);
    }

    #[Test]
    public function penarikan_gagal_jika_saldo_tidak_mencukupi(): void
    {
        $this->admin->markEmailAsVerified();
        $ledger = $this->buatLedger();
        $passbook = StudentPassbook::create([
            'student_id' => $this->student->student_id,
            'ledger_id' => $ledger->ledger_id,
            'opening_balance' => 0,
            'current_balance' => 10000, // Hanya ada 10 ribu
            'opening_date' => now(),
        ]);

        // Coba tarik 50 ribu
        $this->actingAs($this->admin)
             ->post("/admin/tabungan/passbooks/{$passbook->passbook_id}/withdraw", [
                 'amount' => 50000,
                 'description' => 'Tarik tunai',
                 'transaction_date' => now()->toDateString(),
             ])
             ->assertSessionHas('error'); // Diharapkan kembali dengan error flash message

        // Saldo tidak boleh berubah
        $this->assertDatabaseHas('student_passbooks', [
            'passbook_id' => $passbook->passbook_id,
            'current_balance' => 10000,
        ]);
    }

    #[Test]
    public function orang_tua_tidak_bisa_mengakses_halaman_admin_tabungan(): void
    {
        $this->orangTua->markEmailAsVerified();
        $ledger = $this->buatLedger();
        $passbook = StudentPassbook::create([
            'student_id' => $this->student->student_id,
            'ledger_id' => $ledger->ledger_id,
            'opening_balance' => 0,
            'current_balance' => 10000,
            'opening_date' => now(),
        ]);

        // Orang tua mengakses endpoint POST milik admin
        $this->actingAs($this->orangTua)
             ->post("/admin/tabungan/passbooks/{$passbook->passbook_id}/withdraw", [
                 'amount' => 5000,
                 'description' => 'Tarik paksa',
                 'transaction_date' => now()->toDateString(),
             ])
             ->assertForbidden(); // Role middleware menggunakan abort(403)
    }
}
