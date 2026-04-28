<?php

namespace App\Services\ReportCard;

use App\Models\StudentReportCard;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ReportCardService
{
    // Ambil semua raport dengan filter opsional.
    // Filter yang tersedia: period_id, class_id, status, homeroom_teacher_id.
    // Eager load student, period, classRoom untuk menghindari N+1 query.
    public function getAll(array $filters = []): Collection
    {
        $query = StudentReportCard::with(['student', 'period', 'classRoom']);

        if (!empty($filters['period_id'])) {
            $query->where('period_id', $filters['period_id']);
        }
        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['homeroom_teacher_id'])) {
            $query->where('homeroom_teacher_id', $filters['homeroom_teacher_id']);
        }

        return $query->get();
    }

    // Ambil semua raport milik satu siswa.
    // Jika $periodId diisi, hanya kembalikan raport untuk periode tersebut.
    public function getByStudent(int $studentId, ?int $periodId = null): Collection
    {
        $query = StudentReportCard::where('student_id', $studentId);

        if ($periodId !== null) {
            $query->where('period_id', $periodId);
        }

        return $query->get();
    }

    // Ambil satu raport lengkap beserta SEMUA relasi sekaligus (eager loading).
    // Dipakai saat membuka halaman detail raport agar tidak terjadi N+1 query.
    // homeroomTeacher.user → ambil data teacher sekaligus user-nya (nama, email, dll).
    // narrativeAssessments.photos → ambil narasi sekaligus foto-fotonya.
    // checklistAssessments.category → ambil checklist sekaligus nama kategorinya.
    public function getById(int $id): StudentReportCard
    {
        return StudentReportCard::with([
            'student',
            'period',
            'classRoom',
            'homeroomTeacher.user',
            'approvedBy',
            'narrativeAssessments.photos',
            'checklistAssessments.category',
            'physicalMeasurement',
            'healthCondition',
        ])->findOrFail($id);
    }

    // Buat raport baru dengan status awal 'draft'.
    // Cek duplikat terlebih dahulu: satu siswa hanya boleh punya satu raport per periode.
    // Jika sudah ada, lempar InvalidArgumentException agar controller bisa menanganinya.
    public function create(array $data): StudentReportCard
    {
        $exists = StudentReportCard::where('student_id', $data['student_id'])
            ->where('period_id', $data['period_id'])
            ->exists();

        if ($exists) {
            throw new InvalidArgumentException('Raport untuk siswa dan periode ini sudah ada.');
        }

        return StudentReportCard::create([
            'student_id'          => $data['student_id'],
            'period_id'           => $data['period_id'],
            'class_id'            => $data['class_id'],
            'homeroom_teacher_id' => $data['homeroom_teacher_id'],
            'status'              => 'draft',
        ]);
    }

    // Ubah status raport dari draft → submitted.
    // Guru menekan tombol "Selesai" setelah mengisi semua bagian raport.
    // Hanya boleh dilakukan jika status saat ini adalah 'draft'.
    public function submit(int $id): bool
    {
        $raport = StudentReportCard::findOrFail($id);

        if ($raport->status !== 'draft') {
            throw new InvalidArgumentException('Hanya raport berstatus draft yang dapat disubmit.');
        }

        return (bool) $raport->update(['status' => 'submitted']);
    }

    // Ubah status raport dari submitted → approved.
    // Dilakukan oleh kepala sekolah atau admin setelah mereview isi raport.
    // Mencatat siapa yang approve (approved_by) dan kapan (approved_at).
    // Hanya boleh dilakukan jika status saat ini adalah 'submitted'.
    public function approve(int $id, int $approvedBy): bool
    {
        $raport = StudentReportCard::findOrFail($id);

        if ($raport->status !== 'submitted') {
            throw new InvalidArgumentException('Hanya raport berstatus submitted yang dapat diapprove.');
        }

        return (bool) $raport->update([
            'status'      => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    // Hapus raport beserta semua isinya (cascade di database).
    // Hanya raport berstatus 'draft' yang boleh dihapus.
    // Raport yang sudah submitted atau approved tidak boleh dihapus
    // untuk menjaga integritas data dan jejak audit.
    public function delete(int $id): bool
    {
        $raport = StudentReportCard::findOrFail($id);

        if ($raport->status !== 'draft') {
            throw new InvalidArgumentException('Hanya raport berstatus draft yang dapat dihapus.');
        }

        return (bool) $raport->delete();
    }
}
