<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use App\Models\DevelopmentCategory;
use App\Models\HealthCondition;
use App\Models\NarrativeAssessment;
use App\Models\Student;
use App\Models\StudentChecklistAssessment;
use App\Models\StudentReportCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportCardSeeder extends Seeder
{
    private const TEMPLATE_AGAMA = 'Selama semester ini, Ananda {nama} semakin menunjukkan pemahaman terhadap nilai-nilai agama dan budi pekerti. {nama} sudah terbiasa berdoa sebelum dan sesudah kegiatan, serta mengucapkan salam setiap kali bertemu guru dan teman. {nama} juga menjaga kebersihan dirinya dengan baik, seperti mencuci tangan sebelum makan. Dalam berinteraksi, {nama} mulai menghargai teman dengan berbagi mainan saat bermain bersama. Kami berharap {nama} dapat lebih konsisten dalam menerapkan kebiasaan baik ini secara mandiri tanpa arahan, seperti mengingatkan teman untuk menjaga kebersihan bersama. Kami mengharapkan dukungan dari {ortu} untuk terus membiasakan {nama} menjalankan nilai-nilai agama di rumah, seperti mengingatkan untuk berdoa sebelum tidur, atau sikap menghormati orang tua agar dapat membantu {nama} memperkuat nilai-nilai budi pekerti.';

    private const TEMPLATE_JATI_DIRI = '{nama} menunjukkan kemajuan yang baik dalam mengenali dan mengelola emosinya. {nama} juga sudah bisa menyatakan perasaan senang, sedih, atau marah dengan kata-kata sederhana. Selain itu, {nama} semakin berani memulai percakapan dengan teman-teman barunya. Ia juga terlihat lebih sabar menunggu giliran saat bermain, meskipun sesekali masih perlu diingatkan. Di semester depan, kami akan lebih banyak kegiatan yang membantu {nama} mengekspresikan emosi dengan cara positif, seperti melalui seni dan bercerita. Selain itu, kami juga akan melatih {nama} untuk lebih mandiri dalam menyelesaikan masalah kecil saat bermain bersama teman. Kami berharap {ortu} di rumah dapat mendukung {nama} dengan memberikan waktu untuk berbicara tentang perasaannya setiap hari, misalnya menceritakan pengalaman di sekolah hari ini. Kegiatan sederhana seperti bermain peran di rumah juga dapat membantu {nama} belajar menyelesaikan konflik dengan baik.';

    private const TEMPLATE_STEAM = '{nama} menunjukkan minat yang besar pada kegiatan bercerita dan menggambar serta mampu menyebutkan huruf-huruf dalam namanya dan mengenali angka hingga 10. Dalam kegiatan eksplorasi sains, {nama} sangat antusias saat mencoba permainan air dan pasir. Kreativitasnya juga berkembang pesat, terlihat dari gambar yang penuh warna dan pola yang mulai teratur. Pada pembelajaran selanjutnya, {nama} akan berlatih mengenali hubungan sederhana antara angka dan benda untuk mendukung pemahaman dasar matematika. Sehingga kami menyarankan orang tua mendampingi {nama} dalam kegiatan seperti membaca buku cerita bergambar atau menghitung benda-benda sederhana di rumah. {ortu} juga bisa meluangkan waktu untuk mengajak {nama} mencoba hal baru, seperti melukis atau merakit mainan.';

    // 47 leaf items per pattern: 1(cat1) + 6(Iman) + 5(Islam) + 14(Doa) + 8(Kalimat) + 6(Surah) + 5(Wudhu) + 1(cat8) + 1(cat9)
    private const PATTERNS = [
        'A' => ['SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','MM','MM','MM','MM','SM','SM','MM','MM','MM','SM','MM','MM','MM','SM','MM','MM','SM','SM','MM','SM','MM','SM','SM','MM','MM','MM','MM','SM','MM','SM','SM','MM','MM','MM'],
        'B' => ['SM','MM','MM','MM','MM','MM','MM','SM','SM','SM','SM','SM','SM','MM','MM','SM','MM','SM','SM','MM','MM','MM','SM','MM','SM','MM','SM','MM','SM','MM','MM','MM','SM','MM','SM','SM','MM','SM','MM','MM','SM','MM','SM','SM','MM','MM','MM'],
        'C' => ['SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','SM','MM','MM','MM','MM','MM','MM','MM','BM','BM','MM','MM','MM','MM','MM','BM','MM','MM','BM','BM','MM','MM','BM','SM','SM','BM','MM','BM','BM','BM','BM','BM','BM','BM','BM','BM'],
        'D' => ['MM','MM','MM','MM','MM','MM','MM','SM','SM','SM','SM','SM','MM','MM','MM','MM','MM','SM','SM','BM','BM','BM','MM','MM','MM','MM','MM','MM','SM','MM','MM','MM','SM','BM','SM','SM','MM','SM','BM','BM','BM','BM','BM','BM','BM','BM','BM'],
        'E' => ['MM','MM','MM','MM','MM','MM','MM','SM','SM','SM','SM','SM','MM','MM','MM','MM','MM','SM','SM','BM','BM','BM','MM','MM','MM','MM','MM','MM','SM','MM','MM','MM','SM','BM','SM','SM','MM','MM','BM','BM','MM','MM','MM','MM','MM','MM','MM'],
        'F' => ['SM','MM','MM','MM','MM','MM','MM','MM','MM','MM','MM','MM','SM','SM','SM','MM','MM','MM','MM','BM','BM','BM','BM','SM','SM','SM','SM','SM','SM','SM','BM','BM','SM','BM','SM','SM','BM','SM','BM','BM','MM','MM','MM','MM','MM','MM','MM'],
    ];

    public function run(): void
    {
        $period = AcademicPeriod::where('tahun_ajaran', '2025/2026')
            ->where('semester', 1)->first();

        $sofiaTeacherId = User::where('email', 'guru@iqra.com')
            ->first()->teacher->teacher_id;
        $lismaTeacherId = User::where('email', 'lisma.pane@iqra.com')
            ->first()->teacher->teacher_id;

        $adminUserId = User::where('email', 'admin@iqra.com')->value('user_id');

        $leafCategoryIds = $this->getLeafCategoryIds();

        foreach ($this->getStudentData($sofiaTeacherId, $lismaTeacherId) as $data) {
            $student = Student::where('NIS', $data['nis'])->first();
            if (!$student) {
                continue;
            }

            $reportCard = StudentReportCard::create([
                'student_id'          => $student->student_id,
                'period_id'           => $period->period_id,
                'class_id'            => $student->class_id,
                'homeroom_teacher_id' => $data['teacher_id'],
                'status'              => 'approved',
                'approved_by'         => $adminUserId,
                'approved_at'         => '2025-12-20',
            ]);

            $this->seedNarratives($reportCard, $data['nama'], $data['ortu']);
            $this->seedChecklist($reportCard, $leafCategoryIds, $data['pattern']);
            $this->seedHealth($reportCard);
        }
    }

    private function getLeafCategoryIds(): array
    {
        $leaves = [];
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

    private function seedNarratives(StudentReportCard $reportCard, string $nama, string $ortu): void
    {
        $replace = fn (string $template) => str_replace(
            ['{nama}', '{ortu}'], [$nama, $ortu], $template
        );

        $narratives = [
            ['kategori' => 'intrakurikuler', 'judul' => 'Nilai Agama dan Budi Pekerti', 'template' => self::TEMPLATE_AGAMA],
            ['kategori' => 'kokurikuler', 'judul' => 'Jati Diri', 'template' => self::TEMPLATE_JATI_DIRI],
            ['kategori' => 'kokurikuler', 'judul' => 'Dasar-dasar Literasi, Matematika, Sains, Teknologi, Rekayasa, dan Seni', 'template' => self::TEMPLATE_STEAM],
        ];

        foreach ($narratives as $n) {
            NarrativeAssessment::create([
                'report_card_id' => $reportCard->report_card_id,
                'kategori'       => $n['kategori'],
                'judul'          => $n['judul'],
                'isi_naratif'    => $replace($n['template']),
            ]);
        }
    }

    private function seedChecklist(StudentReportCard $reportCard, array $leafCategoryIds, string $patternKey): void
    {
        $statuses = self::PATTERNS[$patternKey];

        foreach ($statuses as $i => $status) {
            StudentChecklistAssessment::create([
                'report_card_id' => $reportCard->report_card_id,
                'category_id'    => $leafCategoryIds[$i],
                'status'         => $status,
            ]);
        }
    }

    private function seedHealth(StudentReportCard $reportCard): void
    {
        HealthCondition::create([
            'report_card_id' => $reportCard->report_card_id,
            'pendengaran'    => 'Baik',
            'penglihatan'    => 'Baik',
        ]);
    }

    private function getStudentData(int $sofiaId, int $lismaId): array
    {
        return [
            ['nis' => '2317', 'teacher_id' => $sofiaId,  'nama' => 'Rafif',   'ortu' => 'Ayah dan Bunda', 'pattern' => 'D'],
            ['nis' => '2318', 'teacher_id' => $sofiaId,  'nama' => 'Ryuga',   'ortu' => 'Abah dan Umi',   'pattern' => 'A'],
            ['nis' => '2321', 'teacher_id' => $lismaId,  'nama' => 'Jidan',   'ortu' => 'Ayah dan Mama',  'pattern' => 'A'],
            ['nis' => '2323', 'teacher_id' => $lismaId,  'nama' => 'Arumi',   'ortu' => 'Ayah dan Bunda', 'pattern' => 'E'],
            ['nis' => '2324', 'teacher_id' => $lismaId,  'nama' => 'Arumi',   'ortu' => 'Papa dan Mama',  'pattern' => 'B'],
            ['nis' => '2325', 'teacher_id' => $sofiaId,  'nama' => 'Fathan',  'ortu' => 'Ayah dan Mama',  'pattern' => 'A'],
            ['nis' => '2326', 'teacher_id' => $lismaId,  'nama' => 'Putri',   'ortu' => 'Ayah dan Mama',  'pattern' => 'B'],
            ['nis' => '2328', 'teacher_id' => $lismaId,  'nama' => 'Zafran',  'ortu' => 'Ayah dan Mama',  'pattern' => 'A'],
            ['nis' => '2329', 'teacher_id' => $sofiaId,  'nama' => 'Nabila',  'ortu' => 'Ayah dan Mama',  'pattern' => 'F'],
            ['nis' => '2330', 'teacher_id' => $lismaId,  'nama' => 'Nabila',  'ortu' => 'Ayah dan Bunda', 'pattern' => 'B'],
            ['nis' => '2331', 'teacher_id' => $sofiaId,  'nama' => 'Nayra',   'ortu' => 'Ayah dan Ibu',   'pattern' => 'C'],
            ['nis' => '2332', 'teacher_id' => $sofiaId,  'nama' => 'Aya',     'ortu' => 'Ayah dan Bunda', 'pattern' => 'A'],
            ['nis' => '2333', 'teacher_id' => $lismaId,  'nama' => 'Caca',    'ortu' => 'Ayah dan Bunda', 'pattern' => 'F'],
            ['nis' => '2334', 'teacher_id' => $sofiaId,  'nama' => 'Yasmin',  'ortu' => 'Ayah dan Bunda', 'pattern' => 'D'],
            ['nis' => '2335', 'teacher_id' => $sofiaId,  'nama' => 'Zahra',   'ortu' => 'Ayah dan Mama',  'pattern' => 'B'],
            ['nis' => '2336', 'teacher_id' => $sofiaId,  'nama' => 'Alwi',    'ortu' => 'Ayah dan Mama',  'pattern' => 'C'],
            ['nis' => '2337', 'teacher_id' => $sofiaId,  'nama' => 'Rara',    'ortu' => 'Ayah dan Umi',   'pattern' => 'C'],
        ];
    }
}
