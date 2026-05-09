<?php

namespace App\Services\ReportCard;

use App\Models\NarrativeAssessment;
use App\Models\NarrativePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class NarrativeAssessmentService
{
    // Ambil semua narasi milik satu raport beserta foto-fotonya sekaligus.
    // Diurutkan by kategori sehingga 'intrakurikuler' selalu tampil sebelum 'kokurikuler'.
    public function getByReportCard(int $reportCardId): Collection
    {
        return NarrativeAssessment::with('photos')
            ->where('report_card_id', $reportCardId)
            ->orderBy('kategori')
            ->get();
    }

    // Simpan atau update narasi dalam satu raport.
    // Menggunakan updateOrCreate sehingga guru bisa menekan Save berkali-kali
    // tanpa menimbulkan data duplikat.
    // Kunci pencarian: kombinasi report_card_id + judul (keduanya unik per raport).
    // $narratives contoh isi:
    // [
    //   ['kategori' => 'intrakurikuler', 'judul' => 'Nilai Agama dan Budi Pekerti', 'isi_naratif' => '...'],
    //   ['kategori' => 'kokurikuler',    'judul' => 'Jati Diri',                    'isi_naratif' => '...'],
    // ]
    public function upsert(int $reportCardId, array $narratives): void
    {
        foreach ($narratives as $item) {
            NarrativeAssessment::updateOrCreate(
                ['report_card_id' => $reportCardId, 'judul' => $item['judul']],
                ['kategori' => $item['kategori'], 'isi_naratif' => $item['isi_naratif'] ?? '']
            );
        }
    }

    // Upload foto dokumentasi dan lampirkan ke satu narasi.
    // File disimpan ke disk 'public' di folder raport/photos/.
    // Path yang tersimpan di database: raport/photos/namafile.jpg
    // File bisa diakses publik via URL: /storage/raport/photos/namafile.jpg
    // $urutan menentukan urutan tampil foto jika ada lebih dari satu.
    public function addPhoto(int $narrativeId, UploadedFile $file, ?string $caption, int $urutan): NarrativePhoto
    {
        $path = $file->store('raport/photos', 'public');

        return NarrativePhoto::create([
            'narrative_id' => $narrativeId,
            'photo_path'   => $path,
            'caption'      => $caption,
            'urutan'       => $urutan,
        ]);
    }

    // Hapus satu foto: file fisik dari storage dihapus terlebih dahulu,
    // baru kemudian record-nya dihapus dari database.
    // Urutan ini penting: jika record dihapus duluan, path file tidak bisa diketahui lagi.
    public function deletePhoto(int $photoId): bool
    {
        $photo = NarrativePhoto::findOrFail($photoId);
        Storage::disk('public')->delete($photo->photo_path);

        return (bool) $photo->delete();
    }
}
