<?php

namespace App\Services\ReportCard;

use App\Models\DevelopmentCategory;
use App\Models\StudentChecklistAssessment;
use Illuminate\Support\Collection;

class ChecklistAssessmentService
{
    // Ambil seluruh struktur kategori perkembangan untuk ditampilkan di form penilaian.
    // with('children') → eager load sub-item sekaligus dalam 2 query total, bukan N+1.
    // whereNull('parent_id') → hanya ambil kategori utama (level atas).
    //   Sub-item ikut otomatis via relasi children yang sudah di-eager load.
    // where('is_active', true) → kategori yang dinonaktifkan tidak akan muncul.
    // orderBy('urutan') → tampil sesuai urutan yang sudah ditentukan, bukan acak.
    public function getAllCategories(): Collection
    {
        return DevelopmentCategory::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();
    }

    // Ambil semua hasil penilaian checklist untuk satu raport.
    // keyBy('category_id') → ubah collection menjadi array asosiatif dengan category_id sebagai key.
    // Tujuannya: di view, status setiap sub-item bisa langsung diakses via $checklists[$categoryId]
    // tanpa harus loop — lebih efisien saat merender form dengan banyak item.
    public function getByReportCard(int $reportCardId): Collection
    {
        return StudentChecklistAssessment::where('report_card_id', $reportCardId)
            ->get()
            ->keyBy('category_id');
    }

    // Simpan atau update hasil penilaian checklist dalam satu raport.
    // Guard isLeaf(): hanya sub-item (leaf node) yang boleh dinilai.
    //   Kategori utama seperti "Rukun Iman" dilewati karena bukan item penilaian.
    // updateOrCreate: guru bisa simpan berulang kali tanpa duplikasi data.
    // Kunci pencarian: kombinasi report_card_id + category_id (unik per raport).
    // $checklists contoh isi:
    // [
    //   ['category_id' => 2, 'status' => 'SM', 'catatan' => null],
    //   ['category_id' => 3, 'status' => 'MM', 'catatan' => 'Perlu latihan'],
    // ]
    public function upsert(int $reportCardId, array $checklists): void
    {
        foreach ($checklists as $item) {
            $category = DevelopmentCategory::find($item['category_id']);

            // Lewati jika category tidak ditemukan atau bukan leaf node
            if (!$category || !$category->isLeaf()) {
                continue;
            }

            StudentChecklistAssessment::updateOrCreate(
                ['report_card_id' => $reportCardId, 'category_id' => $item['category_id']],
                ['status' => $item['status'], 'catatan' => $item['catatan'] ?? null]
            );
        }
    }
}
