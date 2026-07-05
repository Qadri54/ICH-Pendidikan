<?php

namespace App\Services\ReportCard;

use App\Models\ReportCardSnapshot;
use App\Models\StudentAttendance;
use App\Models\StudentReportCard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportCardSnapshotService
{
    public function createSnapshot(int $reportCardId): ReportCardSnapshot
    {
        $raport = StudentReportCard::with([
            'student',
            'period',
            'classRoom',
            'homeroomTeacher.user',
            'narrativeAssessments.photos',
            'checklistAssessments.category.parent',
            'physicalMeasurement',
            'healthCondition',
        ])->findOrFail($reportCardId);

        $attendance = $this->getAttendance($raport);
        $kepalaSekolah = $this->getKepalaSekolah();

        $data = [
            'student' => [
                'nama_siswa'    => $raport->student->nama_siswa,
                'NIS'           => $raport->student->NIS,
                'tempat_lahir'  => $raport->student->tempat_lahir,
                'tanggal_lahir' => $raport->student->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $raport->student->jenis_kelamin,
                'nama_ayah'     => $raport->student->nama_ayah,
                'nama_ibu'      => $raport->student->nama_ibu,
            ],
            'period' => [
                'tahun_ajaran' => $raport->period->tahun_ajaran,
                'semester'     => $raport->period->semester,
            ],
            'class' => [
                'nama_kelas' => $raport->classRoom?->nama_kelas,
            ],
            'homeroom_teacher_name' => $raport->homeroomTeacher?->user?->name,
            'kepala_sekolah'        => $kepalaSekolah,
            'approved_at'           => $raport->approved_at?->toISOString(),
            'narratives' => $raport->narrativeAssessments->map(fn ($n) => [
                'kategori'   => $n->kategori,
                'judul'      => $n->judul,
                'isi_naratif' => $n->isi_naratif,
                'photos'     => $n->photos->map(fn ($p) => [
                    'photo_path' => $p->photo_path,
                    'caption'    => $p->caption,
                    'urutan'     => $p->urutan,
                ])->toArray(),
            ])->toArray(),
            'checklists' => $raport->checklistAssessments->map(fn ($cl) => [
                'category_nama' => $cl->category?->nama,
                'parent_nama'   => $cl->category?->parent?->nama,
                'status'        => $cl->status,
                'catatan'       => $cl->catatan,
                'urutan'        => $cl->category?->urutan ?? 999,
            ])->toArray(),
            'physical_measurement' => $raport->physicalMeasurement ? [
                'tinggi_badan'   => $raport->physicalMeasurement->tinggi_badan,
                'berat_badan'    => $raport->physicalMeasurement->berat_badan,
                'lingkar_kepala' => $raport->physicalMeasurement->lingkar_kepala,
                'tanggal_ukur'   => $raport->physicalMeasurement->tanggal_ukur?->format('Y-m-d'),
            ] : null,
            'health_condition' => $raport->healthCondition ? [
                'pendengaran'      => $raport->healthCondition->pendengaran,
                'penglihatan'      => $raport->healthCondition->penglihatan,
                'catatan_tambahan' => $raport->healthCondition->catatan_tambahan,
            ] : null,
            'attendance' => $attendance,
        ];

        return ReportCardSnapshot::updateOrCreate(
            ['report_card_id' => $reportCardId],
            ['snapshot_data' => $data],
        );
    }

    public function hydrateForPdf(array $data): array
    {
        $student = (object) $data['student'];
        $student->tanggal_lahir = isset($data['student']['tanggal_lahir'])
            ? Carbon::parse($data['student']['tanggal_lahir'])
            : null;

        $period = (object) $data['period'];

        $classRoom = (object) ($data['class'] ?? ['nama_kelas' => '-']);

        $homeroomTeacher = (object) [
            'user' => (object) ['name' => $data['homeroom_teacher_name'] ?? '-'],
        ];

        $physicalMeasurement = isset($data['physical_measurement'])
            ? (object) $data['physical_measurement']
            : null;

        $healthCondition = isset($data['health_condition'])
            ? (object) $data['health_condition']
            : null;

        $narrativeAssessments = collect($data['narratives'] ?? [])->map(function ($n) {
            $obj = (object) $n;
            $obj->photos = collect($n['photos'] ?? [])->map(fn ($p) => (object) $p);
            return $obj;
        });

        $checklistAssessments = collect($data['checklists'] ?? [])->map(function ($cl) {
            return (object) [
                'status'   => $cl['status'],
                'catatan'  => $cl['catatan'] ?? null,
                'category' => (object) [
                    'nama'   => $cl['category_nama'],
                    'urutan' => $cl['urutan'] ?? 999,
                    'parent' => isset($cl['parent_nama'])
                        ? (object) ['nama' => $cl['parent_nama']]
                        : null,
                ],
            ];
        });

        $raport = (object) [
            'student'               => $student,
            'period'                => $period,
            'classRoom'             => $classRoom,
            'homeroomTeacher'       => $homeroomTeacher,
            'physicalMeasurement'   => $physicalMeasurement,
            'healthCondition'       => $healthCondition,
            'narrativeAssessments'  => $narrativeAssessments,
            'checklistAssessments'  => $checklistAssessments,
            'approved_at'           => isset($data['approved_at'])
                ? Carbon::parse($data['approved_at'])
                : null,
        ];

        return [
            'raport'         => $raport,
            'attendance'     => $data['attendance'] ?? ['sakit' => 0, 'izin' => 0, 'tanpa_keterangan' => 0],
            'kepalaSekolah'  => $data['kepala_sekolah'] ?? 'Kepala Sekolah',
        ];
    }

    private function getAttendance(StudentReportCard $raport): array
    {
        $records = StudentAttendance::where('student_id', $raport->student_id)
            ->whereBetween('created_at', [
                $raport->period->tanggal_mulai,
                $raport->period->tanggal_selesai,
            ])
            ->get();

        return [
            'sakit'            => $records->where('status', 'sakit')->count(),
            'izin'             => $records->where('status', 'izin')->count(),
            'tanpa_keterangan' => $records->where('status', 'tanpa keterangan')->count(),
        ];
    }

    private function getKepalaSekolah(): string
    {
        $kepsek = User::whereHas('role', fn ($q) => $q->where('role_name', 'Kepala Sekolah'))
            ->first();

        return $kepsek?->name ?? 'Kepala Sekolah';
    }
}
