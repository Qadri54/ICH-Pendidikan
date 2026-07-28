<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\Admin;
use App\Models\ClassRoom;
use App\Models\GeofenceZone;
use App\Models\PhysicalMeasurement;
use App\Models\Registration;
use App\Models\RegistrationFee;
use App\Models\RegistrationSetting;
use App\Models\RegistrationTransaction;
use App\Models\Role;
use App\Models\SavingLedger;
use App\Models\SavingTransaction;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\StudentPassbook;
use App\Models\StudentReportCard;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    private int $txnSeq = 1;

    public function run(): void
    {
        $this->setupInfrastructure();
        $this->seedRegistrations();
        $this->seedSavings();
        $this->seedPhysicalMeasurements();

        $this->printDemoAccounts();
    }

    private function setupInfrastructure(): void
    {
        if (GeofenceZone::count() === 0) {
            GeofenceZone::create([
                'center_latitude'  => 3.5952000,
                'center_longitude' => 98.6722000,
                'radius_meter'     => 100.00,
            ]);
        }

        RegistrationSetting::first()?->update(['is_registration_period' => true]);

        $guruNgaji = Teacher::whereHas('user', fn ($q) => $q->where('email', 'guruNgaji@iqra.com'))->first();
        if ($guruNgaji) {
            ClassRoom::where('class_id', 3)
                ->whereNull('homeroom_teacher_id')
                ->update(['homeroom_teacher_id' => $guruNgaji->teacher_id]);
        }

        $this->command->info('Infrastruktur: geofence, pendaftaran dibuka, wali Kelas C');
    }

    // ─── PPDB ────────────────────────────────────────────────────────────

    private function seedRegistrations(): void
    {
        if (Registration::count() > 0) return;

        $admin     = Admin::first();
        $adminUser = User::whereHas('role', fn ($q) => $q->where('role_name', 'Admin'))->first();
        $banks     = ['BRI', 'BCA', 'Mandiri', 'BNI', 'Bank Sumut', 'BSI'];

        foreach ($this->getRegistrationData() as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'no_hp'    => $data['no_hp'],
                'password' => Hash::make('password123'),
                'status'   => 'active',
            ]);

            Role::create([
                'user_id'   => $user->user_id,
                'role_name' => 'Orang Tua',
            ]);

            $reg = $data['registration'];
            $reg['user_id'] = $user->user_id;
            Registration::create($reg);

            if (empty($data['create_student'])) continue;

            $student = Student::create([
                'user_id'       => $user->user_id,
                'class_id'      => 1,
                'nama_siswa'    => $reg['nama_siswa'],
                'NIS'           => '2338',
                'jenis_kelamin' => $reg['jenis_kelamin'],
                'tanggal_lahir' => $reg['tanggal_lahir'],
                'tempat_lahir'  => $reg['tempat_lahir'],
                'nama_ayah'     => $reg['nama_ayah'],
                'nama_ibu'      => $reg['nama_ibu'],
                'status'        => 'aktif',
            ]);

            $this->seedStudentFees($student, $admin);
            $this->seedStudentSpp($student, $adminUser, $banks);
        }

        $this->command->info('PPDB: 2 pending + 1 diterima (siswa + cicilan + SPP)');
    }

    private function seedStudentFees(Student $student, Admin $admin): void
    {
        $fee = RegistrationFee::create([
            'student_id'   => $student->student_id,
            'total_jumlah' => 3000000,
            'status'       => 'installments',
        ]);

        RegistrationTransaction::create([
            'registration_fee_id'     => $fee->registration_fee_id,
            'approved_by'             => $admin->admin_id,
            'payment_date'            => Carbon::create(2025, 12, 5),
            'jumlah_bayar'            => 1500000,
            'nama_bank'               => 'BCA',
            'gambar_bukti_pembayaran' => 'bukti/demo-reg-1.jpg',
            'payment_category'        => 'installment',
            'status'                  => 'approved',
        ]);

        RegistrationTransaction::create([
            'registration_fee_id'     => $fee->registration_fee_id,
            'payment_date'            => Carbon::create(2026, 1, 10),
            'jumlah_bayar'            => 1000000,
            'nama_bank'               => 'Mandiri',
            'gambar_bukti_pembayaran' => 'bukti/demo-reg-2.jpg',
            'payment_category'        => 'installment',
            'status'                  => 'pending',
        ]);
    }

    private function seedStudentSpp(Student $student, User $adminUser, array $banks): void
    {
        $sppAmount = 350000;

        for ($month = 1; $month <= 6; $month++) {
            $tanggal    = Carbon::create(2026, $month, 1);
            $jatuhTempo = Carbon::create(2026, $month, 10);
            $isPaid     = $month <= 4;
            $isPending  = $month === 5;

            $status = match (true) {
                $isPaid    => 'paid',
                $isPending => 'pending',
                default    => 'unpaid',
            };

            $invoice = SppInvoice::create([
                'student_id'    => $student->student_id,
                'tanggal_tahun' => $tanggal,
                'jumlah'        => $sppAmount,
                'jatuh_tempo'   => $jatuhTempo,
                'status'        => $status,
            ]);

            if ($isPaid || $isPending) {
                SppPayment::create([
                    'student_id'              => $student->student_id,
                    'invoice_id'              => $invoice->invoice_id,
                    'approved_by'             => $isPaid ? $adminUser->user_id : null,
                    'payment_date'            => $tanggal->copy()->addDays(rand(1, 8)),
                    'jumlah_bayar'            => $sppAmount,
                    'nama_bank'               => $banks[array_rand($banks)],
                    'gambar_bukti_pembayaran' => 'bukti/demo-spp-' . $month . '.jpg',
                    'status'                  => $isPaid ? 'paid' : 'pending',
                ]);
            }
        }
    }

    // ─── TABUNGAN ────────────────────────────────────────────────────────

    private function seedSavings(): void
    {
        if (SavingLedger::count() > 0) return;

        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        if (!$activePeriod) return;

        $lisma = Teacher::whereHas('user', fn ($q) => $q->where('email', 'lisma.pane@iqra.com'))->first();
        $sofia = Teacher::whereHas('user', fn ($q) => $q->where('email', 'guru@iqra.com'))->first();

        $configs = [
            ['teacher' => $lisma, 'class_id' => 1, 'name' => 'Tabungan Kelas A Sem 1 2025/2026'],
            ['teacher' => $sofia, 'class_id' => 2, 'name' => 'Tabungan Kelas B Sem 1 2025/2026'],
        ];

        foreach ($configs as $cfg) {
            if (!$cfg['teacher']) continue;

            $ledger = SavingLedger::create([
                'teacher_id'      => $cfg['teacher']->teacher_id,
                'class_id'        => $cfg['class_id'],
                'period_id'       => $activePeriod->period_id,
                'ledger_name'     => $cfg['name'],
                'opening_date'    => $activePeriod->tanggal_mulai,
                'opening_balance' => 0,
                'total_balance'   => 0,
                'status'          => 'Active',
            ]);

            $students = Student::where('class_id', $cfg['class_id'])
                ->where('status', 'aktif')
                ->get();

            $ledgerTotal = 0;

            foreach ($students as $idx => $student) {
                $passbook = StudentPassbook::create([
                    'student_id'      => $student->student_id,
                    'ledger_id'       => $ledger->ledger_id,
                    'opening_date'    => $activePeriod->tanggal_mulai,
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]);

                $balance = $this->seedTransactions($ledger, $passbook, $student, $idx);
                $ledgerTotal += $balance;
            }

            $ledger->update(['total_balance' => $ledgerTotal]);
        }

        $this->command->info('Tabungan: 2 ledger + passbook + transaksi per siswa');
    }

    private function seedTransactions(
        SavingLedger $ledger,
        StudentPassbook $passbook,
        Student $student,
        int $idx,
    ): int {
        $depositSets = [
            [10000, 5000, 15000, 20000, 10000],
            [5000, 10000, 10000, 15000, 5000],
            [20000, 15000, 10000, 5000, 15000],
            [15000, 20000, 5000, 10000, 20000],
        ];

        $amounts = $depositSets[$idx % count($depositSets)];
        $dates   = ['2025-08-04', '2025-09-01', '2025-09-15', '2025-10-06', '2025-11-03'];
        $balance = 0;

        foreach ($dates as $i => $date) {
            $balance += $amounts[$i];
            SavingTransaction::create([
                'student_id'         => $student->student_id,
                'ledger_id'          => $ledger->ledger_id,
                'passbook_id'        => $passbook->passbook_id,
                'transaction_date'   => $date,
                'transaction_type'   => 'deposit',
                'amount'             => $amounts[$i],
                'description'        => 'Setoran mingguan',
                'transaction_number' => $this->generateTxnNumber($date),
            ]);
        }

        if ($idx % 2 === 0) {
            $withdraw = min(10000, $balance);
            $balance -= $withdraw;
            SavingTransaction::create([
                'student_id'         => $student->student_id,
                'ledger_id'          => $ledger->ledger_id,
                'passbook_id'        => $passbook->passbook_id,
                'transaction_date'   => '2025-11-17',
                'transaction_type'   => 'withdrawal',
                'amount'             => $withdraw,
                'description'        => 'Penarikan',
                'transaction_number' => $this->generateTxnNumber('2025-11-17'),
            ]);
        }

        $passbook->update([
            'current_balance' => $balance,
            'last_update'     => '2025-11-17',
        ]);

        return $balance;
    }

    private function generateTxnNumber(string $date): string
    {
        $d = Carbon::parse($date)->format('Ymd');
        return 'TRX-' . $d . '-' . str_pad($this->txnSeq++, 6, '0', STR_PAD_LEFT);
    }

    // ─── RAPORT: PENGUKURAN FISIK ────────────────────────────────────────

    private function seedPhysicalMeasurements(): void
    {
        $cards = StudentReportCard::where('status', 'approved')
            ->whereDoesntHave('physicalMeasurement')
            ->get();

        if ($cards->isEmpty()) return;

        foreach ($cards as $rc) {
            PhysicalMeasurement::create([
                'report_card_id' => $rc->report_card_id,
                'tinggi_badan'   => round(rand(1050, 1200) / 10, 1),
                'berat_badan'    => round(rand(160, 220) / 10, 1),
                'lingkar_kepala' => round(rand(490, 520) / 10, 1),
                'tanggal_ukur'   => '2025-10-15',
            ]);
        }

        $this->command->info("Raport: {$cards->count()} pengukuran fisik ditambahkan");
    }

    // ─── SUMMARY ─────────────────────────────────────────────────────────

    private function printDemoAccounts(): void
    {
        $this->command->newLine();
        $this->command->info('=== AKUN DEMO (password: password123) ===');
        $this->command->table(
            ['Role', 'Email', 'Keterangan'],
            [
                ['Admin',          'admin@iqra.com',          'Full CRUD semua modul'],
                ['Guru (Kelas A)', 'lisma.pane@iqra.com',     'Absensi, tabungan, raport Kelas A'],
                ['Guru (Kelas B)', 'guru@iqra.com',           'Absensi, tabungan, raport Kelas B'],
                ['Kepala Sekolah', 'kepsek@iqra.com',         'Read-only admin area'],
                ['Kepala Yayasan', 'yayasan@iqra.com',        'Read-only admin area'],
                ['Orang Tua',      'aswan.lubis@iqra.com',    'Portal lengkap: SPP, kehadiran, raport, tabungan'],
                ['Orang Tua',      'binsar.sitompul@iqra.com','2 anak (1 alumni + 1 aktif)'],
                ['Calon Ortu 1',   'demo.ortu1@iqra.com',     'Pendaftaran PENDING (untuk approve demo)'],
                ['Calon Ortu 2',   'demo.ortu2@iqra.com',     'Pendaftaran PENDING (untuk reject demo)'],
                ['Calon Ortu 3',   'demo.ortu3@iqra.com',     'Diterima, cicilan PENDING (untuk approve demo)'],
            ],
        );
    }

    // ─── DATA PENDAFTARAN ────────────────────────────────────────────────

    private function getRegistrationData(): array
    {
        return [
            [
                'name'  => 'Sari Dewi',
                'email' => 'demo.ortu1@iqra.com',
                'no_hp' => '081299000001',
                'registration' => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Khalisa Zahra',
                    'tempat_lahir'      => 'Medan',
                    'tanggal_lahir'     => '2021-03-15',
                    'jenis_kelamin'     => 'P',
                    'alamat'            => 'Jl. Gatot Subroto No. 10, Medan',
                    'anak_ke'           => 1,
                    'ukuran_baju'       => 'S',
                    'nama_ayah'         => 'Andi Kusuma',
                    'tempat_lahir_ayah' => 'Medan',
                    'tanggal_lahir_ayah' => '1990-05-20',
                    'alamat_ayah'       => 'Jl. Gatot Subroto No. 10, Medan',
                    'pendidikan_ayah'   => 'S1',
                    'pekerjaan_ayah'    => 'Wiraswasta',
                    'no_telp_ayah'      => '081299000011',
                    'nama_ibu'          => 'Sari Dewi',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1992-08-10',
                    'alamat_ibu'        => 'Jl. Gatot Subroto No. 10, Medan',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'Ibu Rumah Tangga',
                    'no_telp_ibu'       => '081299000001',
                    'status'            => 'pending',
                ],
            ],
            [
                'name'  => 'Fitri Handayani',
                'email' => 'demo.ortu2@iqra.com',
                'no_hp' => '081299000002',
                'registration' => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Arjuna Prasetyo',
                    'tempat_lahir'      => 'Binjai',
                    'tanggal_lahir'     => '2021-07-22',
                    'jenis_kelamin'     => 'L',
                    'alamat'            => 'Jl. Perjuangan No. 45, Binjai',
                    'anak_ke'           => 2,
                    'ukuran_baju'       => 'M',
                    'nama_ayah'         => 'Dimas Prasetyo',
                    'tempat_lahir_ayah' => 'Binjai',
                    'tanggal_lahir_ayah' => '1988-11-05',
                    'alamat_ayah'       => 'Jl. Perjuangan No. 45, Binjai',
                    'pendidikan_ayah'   => 'S1',
                    'pekerjaan_ayah'    => 'PNS',
                    'no_telp_ayah'      => '081299000022',
                    'nama_ibu'          => 'Fitri Handayani',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1990-02-14',
                    'alamat_ibu'        => 'Jl. Perjuangan No. 45, Binjai',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'Guru',
                    'no_telp_ibu'       => '081299000002',
                    'status'            => 'pending',
                ],
            ],
            [
                'name'           => 'Maya Putri',
                'email'          => 'demo.ortu3@iqra.com',
                'no_hp'          => '081299000003',
                'create_student' => true,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Khadijah Azzahra',
                    'tempat_lahir'      => 'Medan',
                    'tanggal_lahir'     => '2021-01-08',
                    'jenis_kelamin'     => 'P',
                    'alamat'            => 'Jl. Setiabudi No. 88, Medan',
                    'anak_ke'           => 1,
                    'ukuran_baju'       => 'M',
                    'nama_ayah'         => 'Reza Gunawan',
                    'tempat_lahir_ayah' => 'Medan',
                    'tanggal_lahir_ayah' => '1989-09-12',
                    'alamat_ayah'       => 'Jl. Setiabudi No. 88, Medan',
                    'pendidikan_ayah'   => 'S2',
                    'pekerjaan_ayah'    => 'Dokter',
                    'no_telp_ayah'      => '081299000033',
                    'nama_ibu'          => 'Maya Putri',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1991-04-25',
                    'alamat_ibu'        => 'Jl. Setiabudi No. 88, Medan',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'Apoteker',
                    'no_telp_ibu'       => '081299000003',
                    'status'            => 'accepted',
                ],
            ],
        ];
    }
}
