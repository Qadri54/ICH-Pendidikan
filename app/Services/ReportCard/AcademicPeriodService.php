<?php

namespace App\Services\ReportCard;

use App\Models\AcademicPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AcademicPeriodService
{
    // Ambil semua periode akademik, diurutkan dari tahun ajaran terbaru,
    // lalu semester 1 sebelum semester 2.
    public function getAll(): Collection
    {
        return AcademicPeriod::orderByDesc('tahun_ajaran')->orderBy('semester')->get();
    }

    // Ambil periode yang sedang aktif (is_active = true).
    // Hanya boleh ada satu periode aktif pada satu waktu.
    // Mengembalikan null jika belum ada periode yang diaktifkan.
    public function getActive(): ?AcademicPeriod
    {
        return AcademicPeriod::where('is_active', true)->first();
    }

    // Ambil satu periode berdasarkan ID.
    // Otomatis lempar 404 jika period_id tidak ditemukan.
    public function getById(int $id): AcademicPeriod
    {
        return AcademicPeriod::findOrFail($id);
    }

    // Buat periode akademik baru.
    // $data harus berisi: tahun_ajaran, semester, tanggal_mulai, tanggal_selesai.
    public function create(array $data): AcademicPeriod
    {
        return AcademicPeriod::create($data);
    }

    // Update data periode yang sudah ada.
    // Mengembalikan true jika berhasil diupdate.
    public function update(int $id, array $data): bool
    {
        return (bool) AcademicPeriod::findOrFail($id)->update($data);
    }

    // Jadikan satu periode sebagai periode aktif.
    // Menggunakan DB::transaction: jika salah satu langkah gagal,
    // keduanya dibatalkan agar tidak ada kondisi "semua nonaktif" atau "dua aktif sekaligus".
    // Langkah 1: nonaktifkan semua periode yang saat ini aktif.
    // Langkah 2: aktifkan periode yang dipilih.
    public function setActive(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
            return (bool) AcademicPeriod::findOrFail($id)->update(['is_active' => true]);
        });
    }

    // Hapus periode akademik.
    // Akan gagal di database jika masih ada raport yang terhubung ke periode ini
    // karena foreign key di student_report_cards menggunakan onDelete('cascade') / restrict.
    public function delete(int $id): bool
    {
        return (bool) AcademicPeriod::findOrFail($id)->delete();
    }
}
