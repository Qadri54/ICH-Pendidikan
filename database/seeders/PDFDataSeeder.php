<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\ClassRoom;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\StudentChecklistAssessment;
use App\Models\StudentReportCard;
use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PDFDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Kepala Sekolah / Yayasan / Admin
        $kepsekUser = User::create([
            'email' => 'kepsek@ich.com',
            'password' => Hash::make('password'),
            'name' => 'Adli Qarin, S.S. M.Ikom',
            'no_hp' => '081361924467',
            'status' => 'active',
        ]);
        Role::create(['user_id' => $kepsekUser->user_id, 'role_name' => 'Admin']);
        Admin::create([
            'user_id' => $kepsekUser->user_id,
            'NIP' => '199906062024011001',
        ]);

        // 2. Akun Guru
        $guruSofiaUser = User::create([
            'email' => 'sofia@ich.com',
            'password' => Hash::make('password'),
            'name' => 'Sofia Aurora Susanto, S.Pd',
            'no_hp' => '085830631730',
            'status' => 'active',
        ]);
        Role::create(['user_id' => $guruSofiaUser->user_id, 'role_name' => 'Guru']);
        $guruSofia = Teacher::create([
            'user_id' => $guruSofiaUser->user_id,
            'NIP' => '200203172024012002',
            'tipe' => 'Guru TK',
            'hire_date' => '2024-01-01',
        ]);

        $guruLismaUser = User::create([
            'email' => 'lisma@ich.com',
            'password' => Hash::make('password'),
            'name' => 'Lisma Farida Pane, S.Pd.I',
            'no_hp' => '083851726758',
            'status' => 'active',
        ]);
        Role::create(['user_id' => $guruLismaUser->user_id, 'role_name' => 'Guru']);
        $guruLisma = Teacher::create([
            'user_id' => $guruLismaUser->user_id,
            'NIP' => '198603012024012003',
            'tipe' => 'Guru TK',
            'hire_date' => '2024-01-01',
        ]);

        // Create Classrooms
        $kelasB = ClassRoom::create(['nama_kelas' => 'TK B', 'nama_ruangan' => 'Ruang B1', 'homeroom_teacher_id' => $guruSofia->teacher_id]);
        $kelasA = ClassRoom::create(['nama_kelas' => 'TK A', 'nama_ruangan' => 'Ruang A1', 'homeroom_teacher_id' => $guruLisma->teacher_id]);

        $period = AcademicPeriod::firstOrCreate(
            ['tahun_ajaran' => '2025/2026', 'semester' => '1'],
            ['tanggal_mulai' => '2025-07-01', 'tanggal_selesai' => '2025-12-31', 'is_active' => true]
        );

        // 3. Akun Orang Tua & Siswa
        $this->createStudent(
            'Robby Endoh Pratama, S.H', 'robby@ich.com', 'Iis Dahlia', '081234567890',
            'Yasmin Zahira', '2334', '2020-03-10', 'P', 'Medan', 'Jl. Datuk Kabu Pasar III Gg. Ridho No.17',
            $kelasB->class_id, true
        );

        $this->createStudent(
            'Jumadi', 'jumadi@ich.com', 'Sutrina', '081234567891',
            'Rafif Afkari', '2317', '2020-05-04', 'L', 'Bandar Kallifah', 'Jl. Batang Kuis Dusun VIII Rambungan I',
            $kelasA->class_id, false
        );

        $this->createStudent(
            'Muhammad Juanda Harahap', 'juanda@ich.com', 'Lisma Farida Pane', '081234567892',
            'Zunaira Safiya Afra Harahap', '2337', '2020-11-16', 'P', 'Medan', 'Jl. Jermal VII Gg. Dahlia',
            $kelasA->class_id, false
        );

        $this->createStudent(
            'Adji Kurniawan', 'adji@ich.com', 'Herliyana Siregar', '081234567893',
            'Arumi Zea Razeta', '2324', '2021-03-22', 'P', 'Medan', 'Jl. Datuk Kabu No.12 Medan',
            $kelasA->class_id, false
        );

        $this->createStudent(
            'Firman Wahid', 'firman@ich.com', 'Ria Triwardani', '081234567894',
            'Muhammad Zafran Khan', '2385', '2020-08-18', 'L', 'Medan', 'Jl. Datuk Kabu Pasar III Gg. Ridho',
            $kelasB->class_id, false
        );

        $this->createStudent(
            'Aswan Lubis', 'aswan@ich.com', 'Yenisunita Nasution', '081234567895',
            'Ahmad Sahydan Lubis', '2321', '2020-03-01', 'L', 'Medan', 'Jl. Bangun Sari IV Gg. Satria V',
            $kelasB->class_id, false
        );
    }

    private function createStudent($ayah, $email, $ibu, $nohp, $namaAnak, $nis, $tglLahir, $jk, $tempatLahir, $alamat, $classId, $isYasmin)
    {
        $ortu = User::create([
            'email' => $email,
            'password' => Hash::make('password'),
            'name' => $ayah,
            'no_hp' => $nohp,
            'status' => 'active',
        ]);
        Role::create(['user_id' => $ortu->user_id, 'role_name' => 'Orang Tua']);

        $student = Student::create([
            'user_id' => $ortu->user_id,
            'class_id' => $classId,
            'nama_siswa' => $namaAnak,
            'NIS' => $nis,
            'jenis_kelamin' => $jk,
            'tanggal_lahir' => $tglLahir,
            'tempat_lahir' => $tempatLahir,
            'nama_ayah' => $ayah,
            'nama_ibu' => $ibu,
            'status' => 'aktif',
        ]);

        if ($isYasmin) {
            $this->seedPenilaianYasmin($student);
        }
    }

    private function seedPenilaianYasmin($student)
    {
        $period = AcademicPeriod::first();
        
        $reportCard = StudentReportCard::create([
            'student_id' => $student->student_id,
            'period_id' => $period->period_id,
            'class_id' => $student->class_id,
            'homeroom_teacher_id' => $student->classRoom->homeroom_teacher_id,
            'status' => 'approved',
        ]);

        // Ceklis Perkembangan Agama
        $dataCeklis = [
            'Mengenal Allah Melalui Penciptanya' => 'SM',
            'Percaya Kepada Allah' => 'MM',
            'Percaya Kepada Malaikat' => 'MM',
            'Percaya Kepada Rasul' => 'MM',
            'Percaya Kepada Kitab' => 'MM',
            'Dua Kalimat Syahadat' => 'SM',
            'Shalat' => 'SM',
            'Doa Ibu Bapak' => 'MM',
            'Doa Mau Tidur' => 'MM',
            'Doa Mau Makan' => 'SM',
            'Doa Selesai Makan' => 'SM',
            'Kalimat Syahadat' => 'MM',
            'Kalimat Basmalah' => 'SM',
            'Surah Al-Fatihah' => 'SM',
            'Surah An-Nas' => 'SM',
            'Mengenal Aturan Wudhu: Berniat' => 'MM',
            'Mengenal Gerakan Shalat' => 'MM',
        ];

        foreach ($dataCeklis as $indicator => $grade) {
            $cat = \App\Models\DevelopmentCategory::firstOrCreate([
                'nama' => $indicator,
                'parent_id' => null,
            ]);

            StudentChecklistAssessment::create([
                'report_card_id' => $reportCard->report_card_id,
                'category_id' => $cat->category_id,
                'status' => $grade,
            ]);
        }
    }
}
