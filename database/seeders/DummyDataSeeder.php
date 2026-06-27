<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\RegistrationFee;
use App\Models\RegistrationTransaction;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\User;
use App\Services\User\UserService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function __construct(private UserService $userService) {}

    public function run(): void
    {
        $this->seedGuruNgaji();
        $this->seedSppData();
        $this->seedRegistrationFees();
        $this->seedStudentAttendance();
        $this->seedTeacherAttendance();
    }

    private function seedGuruNgaji(): void
    {
        $existing = User::where('email', 'guruNgaji@iqra.com')->first();
        if ($existing) return;

        $this->userService->createUser([
            'name'      => 'Ustadz Ahmad Fauzan',
            'email'     => 'guruNgaji@iqra.com',
            'no_hp'     => '08123456703',
            'password'  => 'password123',
            'status'    => 'active',
            'role_name' => 'Guru Ngaji',
            'NIP'       => 'NIP003',
            'tipe'      => 'Guru Ngaji',
            'hire_date' => '2023-06-01',
        ]);
    }

    private function seedSppData(): void
    {
        if (SppInvoice::count() > 0) return;

        $students = Student::whereNotNull('class_id')->get();
        $adminUser = User::whereHas('role', fn($q) => $q->where('role_name', 'Admin'))->first();

        $banks = ['BRI', 'BCA', 'Mandiri', 'BNI', 'Bank Sumut', 'BSI'];
        $sppAmount = 350000;

        foreach ($students as $student) {
            for ($month = 1; $month <= 6; $month++) {
                $tanggal = Carbon::create(2026, $month, 1);
                $jatuhTempo = Carbon::create(2026, $month, 10);

                $isPaid = $month <= 4;
                $isPending = $month === 5;

                $status = match (true) {
                    $isPaid    => 'paid',
                    $isPending => 'pending',
                    default    => 'unpaid',
                };

                $invoice = SppInvoice::create([
                    'student_id'   => $student->student_id,
                    'tanggal_tahun' => $tanggal,
                    'jumlah'       => $sppAmount,
                    'jatuh_tempo'  => $jatuhTempo,
                    'status'       => $status,
                    'created_at'   => $tanggal,
                    'updated_at'   => $isPaid ? $tanggal->copy()->addDays(rand(1, 8)) : $tanggal,
                ]);

                if ($isPaid || $isPending) {
                    $payDate = $tanggal->copy()->addDays(rand(1, 8));
                    SppPayment::create([
                        'student_id'               => $student->student_id,
                        'invoice_id'               => $invoice->invoice_id,
                        'approved_by'              => $isPaid ? $adminUser?->user_id : null,
                        'payment_date'             => $payDate,
                        'jumlah_bayar'             => $sppAmount,
                        'nama_bank'                => $banks[array_rand($banks)],
                        'gambar_bukti_pembayaran'  => 'bukti/dummy_spp_' . $student->student_id . '_' . $month . '.jpg',
                        'status'                   => $isPaid ? 'paid' : 'pending',
                        'created_at'               => $payDate,
                        'updated_at'               => $payDate,
                    ]);
                }
            }
        }
    }

    private function seedRegistrationFees(): void
    {
        if (RegistrationFee::count() > 0) return;

        $students = Student::whereNotNull('class_id')->get();
        $admin = Admin::first();
        $totalFee = 3000000;

        $banks = ['BRI', 'BCA', 'Mandiri', 'BNI', 'Bank Sumut', 'BSI'];

        foreach ($students as $index => $student) {
            $regFee = RegistrationFee::create([
                'student_id'   => $student->student_id,
                'total_jumlah' => $totalFee,
                'status'       => 'paid',
                'created_at'   => Carbon::create(2025, 11, rand(1, 28)),
                'updated_at'   => Carbon::create(2025, 12, rand(1, 28)),
            ]);

            if ($index % 3 === 0) {
                $payDate = Carbon::create(2025, 11, rand(10, 25));
                RegistrationTransaction::create([
                    'registration_fee_id'     => $regFee->registration_fee_id,
                    'approved_by'             => $admin?->admin_id,
                    'payment_date'            => $payDate,
                    'jumlah_bayar'            => $totalFee,
                    'nama_bank'               => $banks[array_rand($banks)],
                    'gambar_bukti_pembayaran' => 'bukti/dummy_reg_' . $student->student_id . '.jpg',
                    'payment_category'        => 'full',
                    'status'                  => 'approved',
                    'created_at'              => $payDate,
                    'updated_at'              => $payDate,
                ]);
            } else {
                $installments = [1500000, 1000000, 500000];
                $baseMonth = 11;
                foreach ($installments as $i => $amount) {
                    $payMonth = $baseMonth + $i;
                    $payYear = $payMonth > 12 ? 2026 : 2025;
                    $actualMonth = $payMonth > 12 ? $payMonth - 12 : $payMonth;
                    $payDate = Carbon::create($payYear, $actualMonth, rand(5, 20));

                    RegistrationTransaction::create([
                        'registration_fee_id'     => $regFee->registration_fee_id,
                        'approved_by'             => $admin?->admin_id,
                        'payment_date'            => $payDate,
                        'jumlah_bayar'            => $amount,
                        'nama_bank'               => $banks[array_rand($banks)],
                        'gambar_bukti_pembayaran' => 'bukti/dummy_reg_' . $student->student_id . '_' . ($i + 1) . '.jpg',
                        'payment_category'        => 'installment',
                        'status'                  => 'approved',
                        'created_at'              => $payDate,
                        'updated_at'              => $payDate,
                    ]);
                }
            }
        }
    }

    private function seedStudentAttendance(): void
    {
        if (StudentAttendance::count() > 0) return;

        $classes = ClassRoom::with('students')->get();
        $teachers = Teacher::where('tipe', 'Guru TK')->get();

        $startDate = Carbon::create(2026, 1, 5);
        $endDate = Carbon::create(2026, 6, 20);

        $schoolDays = collect(CarbonPeriod::create($startDate, $endDate))
            ->filter(fn(Carbon $date) => $date->isWeekday())
            ->values();

        $holidays = collect([
            '2026-01-01', '2026-01-29',
            '2026-02-12',
            '2026-03-20', '2026-03-29', '2026-03-30', '2026-03-31',
            '2026-04-01', '2026-04-02', '2026-04-03',
            '2026-05-01', '2026-05-14', '2026-05-25',
            '2026-06-01',
        ]);

        $schoolDays = $schoolDays->reject(fn(Carbon $d) => $holidays->contains($d->format('Y-m-d')));

        foreach ($classes as $class) {
            $teacher = $teachers->first(fn($t) =>
                $class->homeroom_teacher_id === $t->teacher_id
            ) ?? $teachers->first();

            if (!$teacher) continue;

            foreach ($class->students as $student) {
                foreach ($schoolDays as $date) {
                    $rand = rand(1, 100);
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
        }
    }

    private function seedTeacherAttendance(): void
    {
        if (AttendanceRecord::count() > 0) return;

        $teachers = Teacher::all();

        $schoolLat = 3.5952;
        $schoolLng = 98.6722;

        $startDate = Carbon::create(2026, 1, 5);
        $endDate = Carbon::create(2026, 6, 20);

        $schoolDays = collect(CarbonPeriod::create($startDate, $endDate))
            ->filter(fn(Carbon $date) => $date->isWeekday())
            ->values();

        $holidays = collect([
            '2026-01-01', '2026-01-29',
            '2026-02-12',
            '2026-03-20', '2026-03-29', '2026-03-30', '2026-03-31',
            '2026-04-01', '2026-04-02', '2026-04-03',
            '2026-05-01', '2026-05-14', '2026-05-25',
            '2026-06-01',
        ]);

        $schoolDays = $schoolDays->reject(fn(Carbon $d) => $holidays->contains($d->format('Y-m-d')));

        foreach ($teachers as $teacher) {
            foreach ($schoolDays as $date) {
                $rand = rand(1, 100);
                $status = match (true) {
                    $rand <= 88 => 'Hadir',
                    $rand <= 93 => 'Sakit',
                    $rand <= 97 => 'Izin',
                    default     => 'Tanpa Keterangan',
                };

                $isHadir = $status === 'Hadir';
                $latOffset = (rand(-50, 50) / 100000);
                $lngOffset = (rand(-50, 50) / 100000);
                $checkInLat = $isHadir ? round($schoolLat + $latOffset, 7) : null;
                $checkInLng = $isHadir ? round($schoolLng + $lngOffset, 7) : null;

                $withinGeofence = null;
                if ($isHadir) {
                    $withinGeofence = rand(1, 100) <= 95 ? 'ya' : 'tidak';
                }

                AttendanceRecord::create([
                    'teacher_id'         => $teacher->teacher_id,
                    'check_in_time'      => $isHadir ? $date->copy()->setTime(7, rand(0, 30), rand(0, 59)) : null,
                    'check_in_latitude'  => $checkInLat,
                    'check_in_longitude' => $checkInLng,
                    'check_in_accuracy'  => $isHadir ? rand(3, 25) . 'm' : null,
                    'selfie_path'        => null,
                    'is_within_geofence' => $withinGeofence,
                    'attendance_status'  => $status,
                    'created_at'         => $date->copy()->setTime(7, rand(0, 30), rand(0, 59)),
                    'updated_at'         => $date->copy()->setTime(7, rand(0, 30), rand(0, 59)),
                ]);
            }
        }
    }
}
