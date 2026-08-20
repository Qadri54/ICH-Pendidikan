<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\Admin;
use App\Models\ClassRoom;
use App\Models\DevelopmentCategory;
use App\Models\GeofenceZone;
use App\Models\HealthCondition;
use App\Models\NarrativeAssessment;
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
use App\Models\StudentAttendance;
use App\Models\StudentChecklistAssessment;
use App\Models\StudentPassbook;
use App\Models\StudentReportCard;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    private int $txnSeq = 1;

    public function run(): void
    {
        $this->setupInfrastructure();
        $this->adjustAcademicPeriods();
        $this->seedPreviousSemesterSpp();
        $this->adjustCurrentSppVariety();
        $this->redistributeRegistrationRevenue();
        $this->seedStudentChurn();
        $this->seedRegistrationsWithVariety();
        $this->seedDemoStudentAttendance();
        $this->seedSavings();
        $this->seedPhysicalMeasurements();
        $this->seedSubmittedReportCard();

        $this->printDemoAccounts();
    }

    // ─── INFRASTRUKTUR ───────────────────────────────────────────────────

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

    // ─── PERIODE AKADEMIK ────────────────────────────────────────────────

    private function adjustAcademicPeriods(): void
    {
        AcademicPeriod::query()->update(['is_active' => false]);

        AcademicPeriod::where('tahun_ajaran', '2025/2026')
            ->where('semester', 2)
            ->update(['is_active' => true, 'tanggal_mulai' => '2026-01-01']);

        Student::whereIn('status', ['aktif', 'alumni'])
            ->where('created_at', '>', '2025-07-01')
            ->update(['created_at' => Carbon::create(2025, 7, 15)]);

        $this->command->info('Periode aktif: 2025/2026 Semester 2 (Jan-Jun 2026) + backdate siswa');
    }

    // ─── SPP SEMESTER SEBELUMNYA (untuk perbandingan revenue) ─────────

    private function seedPreviousSemesterSpp(): void
    {
        $prevPeriod = AcademicPeriod::where('tahun_ajaran', '2025/2026')
            ->where('semester', 1)->first();
        if (!$prevPeriod) return;

        $existingPrev = SppInvoice::whereBetween('tanggal_tahun', [
            $prevPeriod->tanggal_mulai, $prevPeriod->tanggal_selesai,
        ])->exists();
        if ($existingPrev) return;

        $students  = Student::where('status', 'aktif')->whereNotNull('class_id')->get();
        $adminUser = User::whereHas('role', fn ($q) => $q->where('role_name', 'Admin'))->first();

        $sppAmount     = 300000;
        $poorPayerNis  = ['2318', '2336'];

        foreach ($students as $student) {
            $isPoorPayer = in_array($student->NIS, $poorPayerNis);

            for ($month = 7; $month <= 12; $month++) {
                $tanggal    = Carbon::create(2025, $month, 1);
                $jatuhTempo = Carbon::create(2025, $month, 10);

                $unpaidMonth = $isPoorPayer && $month >= 11;

                $status = $unpaidMonth ? 'overdue' : 'paid';

                $invoice = SppInvoice::create([
                    'student_id'    => $student->student_id,
                    'tanggal_tahun' => $tanggal,
                    'jumlah'        => $sppAmount,
                    'jatuh_tempo'   => $jatuhTempo,
                    'status'        => $status,
                    'created_at'    => $tanggal,
                    'updated_at'    => $unpaidMonth ? $tanggal : $tanggal->copy()->addDays(rand(1, 8)),
                ]);

                if (!$unpaidMonth) {
                    $payDate = $tanggal->copy()->addDays(rand(1, 8));
                    SppPayment::create([
                        'student_id'              => $student->student_id,
                        'invoice_id'              => $invoice->invoice_id,
                        'approved_by'             => $adminUser?->user_id,
                        'payment_date'            => $payDate,
                        'jumlah_bayar'            => $sppAmount,
                        'gambar_bukti_pembayaran' => 'bukti/prev_spp_' . $student->student_id . '_' . $month . '.jpg',
                        'status'                  => 'paid',
                        'created_at'              => $payDate,
                        'updated_at'              => $payDate,
                    ]);
                }
            }
        }

        $this->command->info('SPP semester sebelumnya: ' . $students->count() . ' siswa × 6 bulan (Jul-Des 2025)');
    }

    // ─── VARIASI SPP SEMESTER INI ────────────────────────────────────────

    private function adjustCurrentSppVariety(): void
    {
        $adminUser = User::whereHas('role', fn ($q) => $q->where('role_name', 'Admin'))->first();

        $perfectPayers = ['2320', '2321', '2322', '2323', '2317', '2327', '2328', '2329'];
        $poorPayers    = ['2332', '2336', '2337'];
        $worstPayer    = ['2318'];

        foreach ($perfectPayers as $nis) {
            $student = Student::where('NIS', $nis)->first();
            if (!$student) continue;

            foreach ([5, 6] as $month) {
                $invoice = SppInvoice::where('student_id', $student->student_id)
                    ->whereMonth('tanggal_tahun', $month)
                    ->whereYear('tanggal_tahun', 2026)
                    ->first();
                if (!$invoice || $invoice->status === 'paid') continue;

                SppPayment::where('invoice_id', $invoice->invoice_id)->delete();

                $payDate = Carbon::create(2026, $month, rand(3, 9));
                SppPayment::create([
                    'student_id'              => $student->student_id,
                    'invoice_id'              => $invoice->invoice_id,
                    'approved_by'             => $adminUser?->user_id,
                    'payment_date'            => $payDate,
                    'jumlah_bayar'            => $invoice->jumlah,
                    'gambar_bukti_pembayaran' => 'bukti/spp_' . $student->student_id . '_' . $month . '.jpg',
                    'status'                  => 'paid',
                    'created_at'              => $payDate,
                    'updated_at'              => $payDate,
                ]);

                $invoice->update(['status' => 'paid']);
            }
        }

        foreach ($poorPayers as $nis) {
            $student = Student::where('NIS', $nis)->first();
            if (!$student) continue;

            foreach ([3, 4, 5, 6] as $month) {
                $invoice = SppInvoice::where('student_id', $student->student_id)
                    ->whereMonth('tanggal_tahun', $month)
                    ->whereYear('tanggal_tahun', 2026)
                    ->first();
                if (!$invoice) continue;

                SppPayment::where('invoice_id', $invoice->invoice_id)->delete();
                $invoice->update(['status' => 'overdue']);
            }
        }

        foreach ($worstPayer as $nis) {
            $student = Student::where('NIS', $nis)->first();
            if (!$student) continue;

            foreach ([2, 3, 4, 5, 6] as $month) {
                $invoice = SppInvoice::where('student_id', $student->student_id)
                    ->whereMonth('tanggal_tahun', $month)
                    ->whereYear('tanggal_tahun', 2026)
                    ->first();
                if (!$invoice) continue;

                SppPayment::where('invoice_id', $invoice->invoice_id)->delete();
                $invoice->update(['status' => 'overdue']);
            }
        }

        $this->command->info('SPP: 8 siswa lunas sempurna, 3 penunggak, 1 penunggak berat');
    }

    // ─── REDISTRIBUSI PENDAPATAN PENDAFTARAN ─────────────────────────────

    private function redistributeRegistrationRevenue(): void
    {
        $fullPayments = RegistrationTransaction::where('status', 'approved')
            ->where('payment_category', 'full')
            ->whereMonth('payment_date', 11)
            ->whereYear('payment_date', 2025)
            ->get();

        foreach ($fullPayments as $i => $tx) {
            $newMonth = ($i % 2 === 0) ? 1 : 2;
            $tx->update(['payment_date' => Carbon::create(2026, $newMonth, rand(5, 15))]);
        }

        $this->command->info('Revenue: ' . $fullPayments->count() . ' pembayaran pendaftaran dipindah ke Jan-Feb 2026');
    }

    // ─── SISWA KELUAR (churn) ────────────────────────────────────────────

    private function seedStudentChurn(): void
    {
        if (Student::where('status', 'keluar')->exists()) return;

        $churnData = [
            [
                'parent_name'  => 'Dewi Anggraini',
                'parent_email' => 'dewi.anggraini@iqra.com',
                'parent_phone' => '081200001111',
                'student_name' => 'Rafa Aqila Pratama',
                'nis'          => '2340',
                'gender'       => 'L',
                'dob'          => '2020-11-05',
                'pob'          => 'Medan',
                'father'       => 'Budi Pratama',
                'mother'       => 'Dewi Anggraini',
                'left_at'      => '2026-03-15',
            ],
            [
                'parent_name'  => 'Nurul Hidayah',
                'parent_email' => 'nurul.hidayah@iqra.com',
                'parent_phone' => '081200002222',
                'student_name' => 'Aisyah Putri Hidayah',
                'nis'          => '2341',
                'gender'       => 'P',
                'dob'          => '2021-02-18',
                'pob'          => 'Binjai',
                'father'       => 'Ahmad Hidayah',
                'mother'       => 'Nurul Hidayah',
                'left_at'      => '2026-04-20',
            ],
        ];

        foreach ($churnData as $data) {
            $user = User::create([
                'name'     => $data['parent_name'],
                'email'    => $data['parent_email'],
                'no_hp'    => $data['parent_phone'],
                'password' => Hash::make('password123'),
                'status'   => 'active',
            ]);
            Role::create(['user_id' => $user->user_id, 'role_name' => 'Orang Tua']);

            Student::create([
                'user_id'       => $user->user_id,
                'class_id'      => null,
                'nama_siswa'    => $data['student_name'],
                'NIS'           => $data['nis'],
                'jenis_kelamin' => $data['gender'],
                'tanggal_lahir' => $data['dob'],
                'tempat_lahir'  => $data['pob'],
                'nama_ayah'     => $data['father'],
                'nama_ibu'      => $data['mother'],
                'status'        => 'keluar',
                'created_at'    => Carbon::create(2025, 7, 15),
                'updated_at'    => Carbon::parse($data['left_at']),
            ]);
        }

        $this->command->info('Churn: 2 siswa keluar (Rafa & Aisyah) untuk metrik churn rate');
    }

    // ─── PPDB DENGAN VARIASI STATUS ──────────────────────────────────────

    private function seedRegistrationsWithVariety(): void
    {
        if (Registration::count() > 0) return;

        $admin     = Admin::first();
        $adminUser = User::whereHas('role', fn ($q) => $q->where('role_name', 'Admin'))->first();

        foreach ($this->getRegistrationData() as $data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'no_hp'    => $data['no_hp'],
                'password' => Hash::make('password123'),
                'status'   => 'active',
            ]);
            Role::create(['user_id' => $user->user_id, 'role_name' => 'Orang Tua']);

            $reg = $data['registration'];
            $reg['user_id'] = $user->user_id;
            Registration::create($reg);

            if (empty($data['create_student'])) continue;

            $student = Student::create([
                'user_id'       => $user->user_id,
                'class_id'      => 1,
                'nama_siswa'    => $reg['nama_siswa'],
                'NIS'           => $data['nis'],
                'jenis_kelamin' => $reg['jenis_kelamin'],
                'tanggal_lahir' => $reg['tanggal_lahir'],
                'tempat_lahir'  => $reg['tempat_lahir'],
                'nama_ayah'     => $reg['nama_ayah'],
                'nama_ibu'      => $reg['nama_ibu'],
                'status'        => 'aktif',
            ]);

            $this->seedStudentFees($student, $admin);
            $this->seedStudentSpp($student, $adminUser);
        }

        $accepted = collect($this->getRegistrationData())->where('registration.status', 'accepted')->count();
        $total    = count($this->getRegistrationData());
        $this->command->info("PPDB: {$total} pendaftaran ({$accepted} diterima, konversi " . round($accepted / $total * 100) . '%)');
    }

    private function seedStudentFees(Student $student, Admin $admin): void
    {
        $fee = RegistrationFee::create([
            'student_id'   => $student->student_id,
            'total_jumlah' => 3000000,
            'status'       => 'installments',
        ]);

        app(\App\Services\Registration\FeeInstallmentService::class)->createInstallments(
            $fee->registration_fee_id,
            $fee->total_jumlah,
            Carbon::now()
        );

        RegistrationTransaction::create([
            'registration_fee_id'     => $fee->registration_fee_id,
            'approved_by'             => $admin->admin_id,
            'payment_date'            => Carbon::create(2025, 12, 5),
            'jumlah_bayar'            => 1500000,
            'gambar_bukti_pembayaran' => 'bukti/demo-reg-1.jpg',
            'payment_category'        => 'installment',
            'status'                  => 'approved',
        ]);

        RegistrationTransaction::create([
            'registration_fee_id'     => $fee->registration_fee_id,
            'payment_date'            => Carbon::create(2026, 1, 10),
            'jumlah_bayar'            => 1000000,
            'gambar_bukti_pembayaran' => 'bukti/demo-reg-2.jpg',
            'payment_category'        => 'installment',
            'status'                  => 'pending',
        ]);
    }

    private function seedStudentSpp(Student $student, User $adminUser): void
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

        $lisma    = Teacher::whereHas('user', fn ($q) => $q->where('email', 'lisma.pane@iqra.com'))->first();
        $sofia    = Teacher::whereHas('user', fn ($q) => $q->where('email', 'guru@iqra.com'))->first();
        $guruNgaji = Teacher::whereHas('user', fn ($q) => $q->where('email', 'guruNgaji@iqra.com'))->first();

        $configs = [
            ['teacher' => $lisma,     'class_id' => 1, 'name' => 'Tabungan Kelas A Sem 2 2025/2026'],
            ['teacher' => $sofia,     'class_id' => 2, 'name' => 'Tabungan Kelas B Sem 2 2025/2026'],
            ['teacher' => $guruNgaji, 'class_id' => 3, 'name' => 'Tabungan Kelas C Sem 2 2025/2026'],
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

        $this->command->info('Tabungan: 3 ledger (Kelas A, B, C) + passbook + transaksi per siswa');
    }

    private function seedTransactions(
        SavingLedger $ledger,
        StudentPassbook $passbook,
        Student $student,
        int $idx,
    ): int {
        $depositSets = [
            [10000, 5000, 15000, 20000, 10000, 5000, 15000],
            [5000, 10000, 10000, 15000, 5000, 20000, 10000],
            [20000, 15000, 10000, 5000, 15000, 10000, 20000],
            [15000, 20000, 5000, 10000, 20000, 15000, 5000],
            [10000, 10000, 20000, 15000, 5000, 10000, 15000],
        ];

        $amounts = $depositSets[$idx % count($depositSets)];
        $dates   = ['2026-01-12', '2026-02-02', '2026-02-16', '2026-03-02', '2026-03-16', '2026-04-06', '2026-05-04'];
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

        if ($idx % 3 === 0) {
            $withdraw = min(15000, $balance);
            $balance -= $withdraw;
            SavingTransaction::create([
                'student_id'         => $student->student_id,
                'ledger_id'          => $ledger->ledger_id,
                'passbook_id'        => $passbook->passbook_id,
                'transaction_date'   => '2026-05-19',
                'transaction_type'   => 'withdrawal',
                'amount'             => $withdraw,
                'description'        => 'Penarikan',
                'transaction_number' => $this->generateTxnNumber('2026-05-19'),
            ]);
        }

        $passbook->update([
            'current_balance' => $balance,
            'last_update'     => '2026-05-19',
        ]);

        return $balance;
    }

    private function generateTxnNumber(string $date): string
    {
        $d = Carbon::parse($date)->format('Ymd');
        return 'TRX-' . $d . '-' . str_pad($this->txnSeq++, 6, '0', STR_PAD_LEFT);
    }

    // ─── KEHADIRAN SISWA DEMO ────────────────────────────────────────────

    private function seedDemoStudentAttendance(): void
    {
        $demoStudents = Student::whereIn('NIS', ['2338'])->get();
        if ($demoStudents->isEmpty()) return;

        $startDate = Carbon::create(2026, 1, 5);
        $endDate   = Carbon::create(2026, 6, 20);

        $schoolDays = collect(CarbonPeriod::create($startDate, $endDate))
            ->filter(fn (Carbon $date) => $date->isWeekday())
            ->values();

        $holidays = collect([
            '2026-01-01', '2026-01-29', '2026-02-12',
            '2026-03-20', '2026-03-29', '2026-03-30', '2026-03-31',
            '2026-04-01', '2026-04-02', '2026-04-03',
            '2026-05-01', '2026-05-14', '2026-05-25', '2026-06-01',
        ]);

        $schoolDays = $schoolDays->reject(fn (Carbon $d) => $holidays->contains($d->format('Y-m-d')));

        foreach ($demoStudents as $student) {
            if (StudentAttendance::where('student_id', $student->student_id)->exists()) continue;

            $class   = ClassRoom::find($student->class_id);
            $teacher = $class?->homeroom_teacher_id ? Teacher::find($class->homeroom_teacher_id) : null;
            if (!$teacher) continue;

            foreach ($schoolDays as $date) {
                $rand   = rand(1, 100);
                $status = match (true) {
                    $rand <= 85 => 'hadir',
                    $rand <= 91 => 'sakit',
                    $rand <= 96 => 'izin',
                    default     => 'tanpa keterangan',
                };

                StudentAttendance::create([
                    'student_id' => $student->student_id,
                    'teacher_id' => $teacher->teacher_id,
                    'status'     => $status,
                    'created_at' => $date->copy()->setTime(7, 30, rand(0, 59)),
                ]);
            }
        }

        $this->command->info('Kehadiran: data absensi siswa demo');
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

    // ─── RAPORT: SUBMITTED (MENUNGGU PERSETUJUAN ADMIN) ────────────────

    private function seedSubmittedReportCard(): void
    {
        $student = Student::where('NIS', '2338')->first();
        if (!$student) return;

        if (StudentReportCard::where('student_id', $student->student_id)->exists()) return;

        $period = AcademicPeriod::where('tahun_ajaran', '2025/2026')
            ->where('semester', 1)->first();
        if (!$period) return;

        $class     = ClassRoom::find($student->class_id);
        $teacherId = $class?->homeroom_teacher_id;

        $rc = StudentReportCard::create([
            'student_id'          => $student->student_id,
            'period_id'           => $period->period_id,
            'class_id'            => $student->class_id,
            'homeroom_teacher_id' => $teacherId,
            'status'              => 'submitted',
        ]);

        $nama = 'Khalisa';
        $ortu = 'Ayah dan Bunda';

        NarrativeAssessment::create([
            'report_card_id' => $rc->report_card_id,
            'kategori'       => 'intrakurikuler',
            'judul'          => 'Nilai Agama dan Budi Pekerti',
            'isi_naratif'    => "Selama semester ini, Ananda {$nama} semakin menunjukkan pemahaman terhadap nilai-nilai agama dan budi pekerti. {$nama} sudah terbiasa berdoa sebelum dan sesudah kegiatan, serta mengucapkan salam setiap kali bertemu guru dan teman. {$nama} juga menjaga kebersihan dirinya dengan baik. Kami berharap {$nama} dapat lebih konsisten dalam menerapkan kebiasaan baik ini secara mandiri. Kami mengharapkan dukungan dari {$ortu} untuk terus membiasakan {$nama} menjalankan nilai-nilai agama di rumah.",
        ]);

        NarrativeAssessment::create([
            'report_card_id' => $rc->report_card_id,
            'kategori'       => 'kokurikuler',
            'judul'          => 'Jati Diri',
            'isi_naratif'    => "{$nama} menunjukkan kemajuan yang baik dalam mengenali dan mengelola emosinya. {$nama} juga sudah bisa menyatakan perasaan senang, sedih, atau marah dengan kata-kata sederhana. Selain itu, {$nama} semakin berani memulai percakapan dengan teman-teman barunya. Kami berharap {$ortu} di rumah dapat mendukung {$nama} dengan memberikan waktu untuk berbicara tentang perasaannya setiap hari.",
        ]);

        NarrativeAssessment::create([
            'report_card_id' => $rc->report_card_id,
            'kategori'       => 'kokurikuler',
            'judul'          => 'Dasar-dasar Literasi, Matematika, Sains, Teknologi, Rekayasa, dan Seni',
            'isi_naratif'    => "{$nama} menunjukkan minat yang besar pada kegiatan bercerita dan menggambar serta mampu menyebutkan huruf-huruf dalam namanya dan mengenali angka hingga 10. Dalam kegiatan eksplorasi sains, {$nama} sangat antusias saat mencoba permainan air dan pasir. Kami menyarankan {$ortu} mendampingi {$nama} dalam kegiatan seperti membaca buku cerita bergambar atau menghitung benda-benda sederhana di rumah.",
        ]);

        $leafIds = $this->getLeafCategoryIds();
        $pattern = ['SM','MM','MM','SM','SM','MM','MM','SM','SM','SM','SM','SM','SM','MM','MM','SM','MM','SM','SM','MM','MM','MM','SM','MM','SM','MM','SM','MM','SM','MM','MM','MM','SM','MM','SM','SM','MM','SM','MM','MM','SM','MM','SM','SM','MM','MM','MM'];
        foreach ($pattern as $i => $status) {
            if (!isset($leafIds[$i])) continue;
            StudentChecklistAssessment::create([
                'report_card_id' => $rc->report_card_id,
                'category_id'    => $leafIds[$i],
                'status'         => $status,
            ]);
        }

        HealthCondition::create([
            'report_card_id' => $rc->report_card_id,
            'pendengaran'    => 'Baik',
            'penglihatan'    => 'Baik',
        ]);

        PhysicalMeasurement::create([
            'report_card_id' => $rc->report_card_id,
            'tinggi_badan'   => 108.5,
            'berat_badan'    => 18.2,
            'lingkar_kepala' => 50.5,
            'tanggal_ukur'   => '2025-10-15',
        ]);

        $this->command->info("Raport: 1 raport submitted (Khalisa Zahra - NIS 2338) siap di-approve admin");
    }

    private function getLeafCategoryIds(): array
    {
        $leaves  = [];
        $parents = DevelopmentCategory::whereNull('parent_id')
            ->orderBy('urutan')->get();

        foreach ($parents as $parent) {
            $children = DevelopmentCategory::where('parent_id', $parent->category_id)
                ->orderBy('urutan')->get();

            if ($children->isEmpty()) {
                $leaves[] = $parent->category_id;
            } else {
                foreach ($children as $child) {
                    $leaves[] = $child->category_id;
                }
            }
        }

        return $leaves;
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
                ['Orang Tua Demo', 'demo.ortu1@iqra.com',     'Cicilan PENDING + raport SUBMITTED'],
            ],
        );

        $this->command->newLine();
        $this->command->info('=== INSIGHT DEMO ===');
        $this->command->info('• Collection Rate SPP: ~74% (8 siswa lunas sempurna, 4 penunggak)');
        $this->command->info('• Revenue comparison: Sem 1 (Rp300rb/bln) vs Sem 2 (Rp350rb/bln)');
        $this->command->info('• Aging tunggakan: tersebar di 3 bucket (2-3 bln, 4-6 bln, >6 bln)');
        $this->command->info('• Churn rate: 2 siswa keluar dalam semester ini');
        $this->command->info('• PPDB conversion: 4/8 diterima (50%)');
        $this->command->info('• Tabungan: 3 kelas dengan variasi setoran');
    }

    // ─── DATA PENDAFTARAN ────────────────────────────────────────────────

    private function getRegistrationData(): array
    {
        $currentPeriodStart = Carbon::create(2026, 1, 5);

        return [
            [
                'name'           => 'Sari Dewi',
                'email'          => 'demo.ortu1@iqra.com',
                'no_hp'          => '081360765971',
                'nis'            => '2338',
                'create_student' => true,
                'registration'   => [
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
                    'status'            => 'accepted',
                    'source'            => 'app',
                    'created_at'        => $currentPeriodStart->copy()->addDays(5),
                ],
            ],
            [
                'name'           => 'Rina Marlina',
                'email'          => 'rina.marlina@iqra.com',
                'no_hp'          => '081300001111',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Azka Raditya',
                    'tempat_lahir'      => 'Medan',
                    'tanggal_lahir'     => '2021-06-20',
                    'jenis_kelamin'     => 'L',
                    'alamat'            => 'Jl. Iskandar Muda No. 25, Medan',
                    'anak_ke'           => 2,
                    'ukuran_baju'       => 'M',
                    'nama_ayah'         => 'Hendra Saputra',
                    'tempat_lahir_ayah' => 'Medan',
                    'tanggal_lahir_ayah' => '1988-09-15',
                    'alamat_ayah'       => 'Jl. Iskandar Muda No. 25, Medan',
                    'pendidikan_ayah'   => 'S1',
                    'pekerjaan_ayah'    => 'PNS',
                    'no_telp_ayah'      => '081300001112',
                    'nama_ibu'          => 'Rina Marlina',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1991-03-22',
                    'alamat_ibu'        => 'Jl. Iskandar Muda No. 25, Medan',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'Guru',
                    'no_telp_ibu'       => '081300001113',
                    'status'            => 'accepted',
                    'source'            => 'app',
                    'created_at'        => $currentPeriodStart->copy()->addDays(12),
                ],
            ],
            [
                'name'           => 'Fitri Handayani',
                'email'          => 'fitri.handayani@iqra.com',
                'no_hp'          => '081300002221',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Nayla Putri Handayani',
                    'tempat_lahir'      => 'Binjai',
                    'tanggal_lahir'     => '2021-01-08',
                    'jenis_kelamin'     => 'P',
                    'alamat'            => 'Jl. Jend. Sudirman No. 88, Binjai',
                    'anak_ke'           => 1,
                    'ukuran_baju'       => 'S',
                    'nama_ayah'         => 'Rizky Handayani',
                    'tempat_lahir_ayah' => 'Binjai',
                    'tanggal_lahir_ayah' => '1989-12-01',
                    'alamat_ayah'       => 'Jl. Jend. Sudirman No. 88, Binjai',
                    'pendidikan_ayah'   => 'D3',
                    'pekerjaan_ayah'    => 'Karyawan Swasta',
                    'no_telp_ayah'      => '081300002222',
                    'nama_ibu'          => 'Fitri Handayani',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1993-07-14',
                    'alamat_ibu'        => 'Jl. Jend. Sudirman No. 88, Binjai',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'Apoteker',
                    'no_telp_ibu'       => '081300002223',
                    'status'            => 'accepted',
                    'source'            => 'admin',
                    'created_at'        => $currentPeriodStart->copy()->addDays(20),
                ],
            ],
            [
                'name'           => 'Maya Sari',
                'email'          => 'maya.sari@iqra.com',
                'no_hp'          => '081300003331',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Farel Ahmad Sari',
                    'tempat_lahir'      => 'Medan',
                    'tanggal_lahir'     => '2021-09-30',
                    'jenis_kelamin'     => 'L',
                    'alamat'            => 'Jl. Krakatau No. 45, Medan',
                    'anak_ke'           => 3,
                    'ukuran_baju'       => 'M',
                    'nama_ayah'         => 'Dimas Sari',
                    'tempat_lahir_ayah' => 'Medan',
                    'tanggal_lahir_ayah' => '1987-04-11',
                    'alamat_ayah'       => 'Jl. Krakatau No. 45, Medan',
                    'pendidikan_ayah'   => 'SMA',
                    'pekerjaan_ayah'    => 'Wiraswasta',
                    'no_telp_ayah'      => '081300003332',
                    'nama_ibu'          => 'Maya Sari',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1990-11-05',
                    'alamat_ibu'        => 'Jl. Krakatau No. 45, Medan',
                    'pendidikan_ibu'    => 'SMA',
                    'pekerjaan_ibu'     => 'Ibu Rumah Tangga',
                    'no_telp_ibu'       => '081300003333',
                    'status'            => 'accepted',
                    'source'            => 'app',
                    'created_at'        => $currentPeriodStart->copy()->addDays(35),
                ],
            ],
            [
                'name'           => 'Lestari Wulandari',
                'email'          => 'lestari.wulandari@iqra.com',
                'no_hp'          => '081300004441',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Aditya Putra Wulandari',
                    'tempat_lahir'      => 'Deli Serdang',
                    'tanggal_lahir'     => '2021-04-17',
                    'jenis_kelamin'     => 'L',
                    'alamat'            => 'Jl. Veteran No. 12, Lubuk Pakam',
                    'anak_ke'           => 1,
                    'ukuran_baju'       => 'S',
                    'nama_ayah'         => 'Arif Wulandari',
                    'tempat_lahir_ayah' => 'Deli Serdang',
                    'tanggal_lahir_ayah' => '1991-02-28',
                    'alamat_ayah'       => 'Jl. Veteran No. 12, Lubuk Pakam',
                    'pendidikan_ayah'   => 'S1',
                    'pekerjaan_ayah'    => 'Dokter',
                    'no_telp_ayah'      => '081300004442',
                    'nama_ibu'          => 'Lestari Wulandari',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1993-06-19',
                    'alamat_ibu'        => 'Jl. Veteran No. 12, Lubuk Pakam',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'Bidan',
                    'no_telp_ibu'       => '081300004443',
                    'status'            => 'pending',
                    'source'            => 'app',
                    'created_at'        => $currentPeriodStart->copy()->addDays(60),
                ],
            ],
            [
                'name'           => 'Sri Wahyuni',
                'email'          => 'sri.wahyuni@iqra.com',
                'no_hp'          => '081300005551',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Keisha Amira Wahyuni',
                    'tempat_lahir'      => 'Medan',
                    'tanggal_lahir'     => '2021-08-22',
                    'jenis_kelamin'     => 'P',
                    'alamat'            => 'Jl. Asia No. 77, Medan',
                    'anak_ke'           => 2,
                    'ukuran_baju'       => 'M',
                    'nama_ayah'         => 'Bambang Wahyuni',
                    'tempat_lahir_ayah' => 'Medan',
                    'tanggal_lahir_ayah' => '1986-10-03',
                    'alamat_ayah'       => 'Jl. Asia No. 77, Medan',
                    'pendidikan_ayah'   => 'S2',
                    'pekerjaan_ayah'    => 'Dosen',
                    'no_telp_ayah'      => '081300005552',
                    'nama_ibu'          => 'Sri Wahyuni',
                    'tempat_lahir_ibu'  => 'Medan',
                    'tanggal_lahir_ibu' => '1989-01-30',
                    'alamat_ibu'        => 'Jl. Asia No. 77, Medan',
                    'pendidikan_ibu'    => 'S1',
                    'pekerjaan_ibu'     => 'PNS',
                    'no_telp_ibu'       => '081300005553',
                    'status'            => 'pending',
                    'source'            => 'app',
                    'created_at'        => $currentPeriodStart->copy()->addDays(75),
                ],
            ],
            [
                'name'           => 'Ratna Sari Dewi',
                'email'          => 'ratna.dewi@iqra.com',
                'no_hp'          => '081300006661',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Gibran Alfarizi',
                    'tempat_lahir'      => 'Medan',
                    'tanggal_lahir'     => '2021-12-10',
                    'jenis_kelamin'     => 'L',
                    'alamat'            => 'Jl. Setia Budi No. 33, Medan',
                    'anak_ke'           => 1,
                    'ukuran_baju'       => 'S',
                    'nama_ayah'         => 'Fajar Alfarizi',
                    'tempat_lahir_ayah' => 'Medan',
                    'tanggal_lahir_ayah' => '1992-07-25',
                    'alamat_ayah'       => 'Jl. Setia Budi No. 33, Medan',
                    'pendidikan_ayah'   => 'SMA',
                    'pekerjaan_ayah'    => 'Pedagang',
                    'no_telp_ayah'      => '081300006662',
                    'nama_ibu'          => 'Ratna Sari Dewi',
                    'tempat_lahir_ibu'  => 'Tebing Tinggi',
                    'tanggal_lahir_ibu' => '1994-05-18',
                    'alamat_ibu'        => 'Jl. Setia Budi No. 33, Medan',
                    'pendidikan_ibu'    => 'SMA',
                    'pekerjaan_ibu'     => 'Ibu Rumah Tangga',
                    'no_telp_ibu'       => '081300006663',
                    'status'            => 'rejected',
                    'source'            => 'app',
                    'rejection_reason'  => 'Usia anak belum mencukupi persyaratan minimum (4 tahun) pada awal tahun ajaran.',
                    'created_at'        => $currentPeriodStart->copy()->addDays(15),
                ],
            ],
            [
                'name'           => 'Anisa Putri',
                'email'          => 'anisa.putri@iqra.com',
                'no_hp'          => '081300007771',
                'create_student' => false,
                'registration'   => [
                    'jenis_pendaftaran' => 'TK',
                    'nama_siswa'        => 'Rania Azzahra Putri',
                    'tempat_lahir'      => 'Langkat',
                    'tanggal_lahir'     => '2021-11-28',
                    'jenis_kelamin'     => 'P',
                    'alamat'            => 'Jl. Pemuda No. 55, Stabat',
                    'anak_ke'           => 1,
                    'ukuran_baju'       => 'S',
                    'nama_ayah'         => 'Irwan Putri',
                    'tempat_lahir_ayah' => 'Langkat',
                    'tanggal_lahir_ayah' => '1990-08-14',
                    'alamat_ayah'       => 'Jl. Pemuda No. 55, Stabat',
                    'pendidikan_ayah'   => 'D3',
                    'pekerjaan_ayah'    => 'Teknisi',
                    'no_telp_ayah'      => '081300007772',
                    'nama_ibu'          => 'Anisa Putri',
                    'tempat_lahir_ibu'  => 'Langkat',
                    'tanggal_lahir_ibu' => '1993-02-07',
                    'alamat_ibu'        => 'Jl. Pemuda No. 55, Stabat',
                    'pendidikan_ibu'    => 'SMA',
                    'pekerjaan_ibu'     => 'Ibu Rumah Tangga',
                    'no_telp_ibu'       => '081300007773',
                    'status'            => 'rejected',
                    'source'            => 'app',
                    'rejection_reason'  => 'Dokumen persyaratan tidak lengkap (akte kelahiran belum dilampirkan).',
                    'created_at'        => $currentPeriodStart->copy()->addDays(40),
                ],
            ],
        ];
    }
}
