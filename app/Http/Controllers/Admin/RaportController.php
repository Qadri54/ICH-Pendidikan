<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Services\ReportCard\AcademicPeriodService;
use App\Services\ReportCard\ChecklistAssessmentService;
use App\Services\ReportCard\HealthConditionService;
use App\Services\ReportCard\NarrativeAssessmentService;
use App\Services\ReportCard\PhysicalMeasurementService;
use App\Services\ReportCard\ReportCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class RaportController extends Controller
{
    public function __construct(
        private ReportCardService          $reportCardService,
        private AcademicPeriodService      $periodService,
        private ChecklistAssessmentService $checklistService,
        private NarrativeAssessmentService $narrativeService,
        private PhysicalMeasurementService $physicalService,
        private HealthConditionService     $healthService,
    ) {}

    // Daftar raport dengan filter periode, kelas, dan status.
    public function index(Request $request): View
    {
        $filters = $request->only(['period_id', 'class_id', 'status']);
        $raports = $this->reportCardService->getAll($filters);
        $periods = $this->periodService->getAll();
        $classes = ClassRoom::orderBy('nama_kelas')->get();

        return view('admin.raport.index', compact('raports', 'periods', 'classes', 'filters'));
    }

    // Form buat raport baru: pilih siswa + periode.
    public function create(): View
    {
        $periods  = $this->periodService->getAll();
        $students = Student::with('classRoom')->orderBy('nama_siswa')->get();
        $active   = $this->periodService->getActive();

        return view('admin.raport.create', compact('periods', 'students', 'active'));
    }

    // Simpan raport baru dengan status draft.
    // class_id dan homeroom_teacher_id diambil otomatis dari kelas siswa.
    public function store(Request $request): RedirectResponse
    {
        $data    = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'period_id'  => 'required|exists:academic_periods,period_id',
        ]);
        $student = Student::with('classRoom')->findOrFail($data['student_id']);

        // Raport wajib punya wali kelas — pastikan kelas siswa sudah dikonfigurasi
        if (! $student->class_id) {
            return back()->withErrors(['error' => 'Siswa belum memiliki kelas. Atur kelas siswa terlebih dahulu.'])->withInput();
        }
        if (! $student->classRoom?->homeroom_teacher_id) {
            return back()->withErrors(['error' => "Kelas {$student->classRoom->nama_kelas} belum memiliki wali kelas. Atur wali kelas di menu Kelas terlebih dahulu."])->withInput();
        }

        try {
            $raport = $this->reportCardService->create([
                'student_id'          => $student->student_id,
                'period_id'           => $data['period_id'],
                'class_id'            => $student->class_id,
                'homeroom_teacher_id' => $student->classRoom->homeroom_teacher_id,
            ]);

            return redirect()->route('admin.raport.edit', $raport->report_card_id)
                ->with('success', 'Raport berhasil dibuat. Silakan isi penilaian.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Halaman edit raport: tampilkan semua komponen penilaian dalam satu halaman.
    public function edit(int $id): View
    {
        $raport     = $this->reportCardService->getById($id);
        $narratives = $this->narrativeService->getByReportCard($id)->keyBy('judul');
        $checklists = $this->checklistService->getByReportCard($id);
        $categories = $this->checklistService->getAllCategories();
        $physical   = $this->physicalService->getByReportCard($id);
        $health     = $this->healthService->getByReportCard($id);

        return view('admin.raport.edit', compact(
            'raport', 'narratives', 'checklists', 'categories', 'physical', 'health'
        ));
    }

    // Simpan narasi penilaian (intrakurikuler + kokurikuler).
    public function updateNarrative(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'narratives'               => 'required|array',
            'narratives.*.judul'       => 'required|string|max:255',
            'narratives.*.kategori'    => 'required|in:intrakurikuler,kokurikuler',
            'narratives.*.isi_naratif' => 'nullable|string',
        ]);

        $this->narrativeService->upsert($id, $data['narratives']);

        return redirect()->route('admin.raport.edit', $id)
            ->with('success', 'Narasi berhasil disimpan.');
    }

    // Simpan penilaian checklist perkembangan.
    public function updateChecklist(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'checklists'               => 'nullable|array',
            'checklists.*.category_id' => 'required|exists:development_categories,category_id',
            'checklists.*.status'      => 'required|in:BM,MM,SM',
            'checklists.*.catatan'     => 'nullable|string',
        ]);

        $this->checklistService->upsert($id, $data['checklists'] ?? []);

        return redirect()->route('admin.raport.edit', $id)
            ->with('success', 'Checklist perkembangan berhasil disimpan.');
    }

    // Simpan pengukuran fisik dan kondisi kesehatan dalam satu form.
    public function updatePhysical(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'tinggi_badan'     => 'nullable|numeric|min:0|max:200',
            'berat_badan'      => 'nullable|numeric|min:0|max:100',
            'lingkar_kepala'   => 'nullable|numeric|min:0|max:80',
            'tanggal_ukur'     => 'nullable|date',
            'pendengaran'      => 'required|string|max:100',
            'penglihatan'      => 'required|string|max:100',
            'catatan_tambahan' => 'nullable|string',
        ]);

        $this->physicalService->upsert($id, [
            'tinggi_badan'   => $data['tinggi_badan'],
            'berat_badan'    => $data['berat_badan'],
            'lingkar_kepala' => $data['lingkar_kepala'],
            'tanggal_ukur'   => $data['tanggal_ukur'],
        ]);

        $this->healthService->upsert($id, [
            'pendengaran'      => $data['pendengaran'],
            'penglihatan'      => $data['penglihatan'],
            'catatan_tambahan' => $data['catatan_tambahan'],
        ]);

        return redirect()->route('admin.raport.edit', $id)
            ->with('success', 'Data fisik dan kesehatan berhasil disimpan.');
    }

    // Ubah status raport draft → submitted.
    public function submit(int $id): RedirectResponse
    {
        try {
            $this->reportCardService->submit($id);

            return redirect()->route('admin.raport.index')
                ->with('success', 'Raport berhasil disubmit.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Ubah status raport submitted → approved.
    public function approve(int $id): RedirectResponse
    {
        try {
            $this->reportCardService->approve($id, auth()->id());

            return redirect()->route('admin.raport.index')
                ->with('success', 'Raport berhasil disetujui.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Hapus raport (hanya boleh status draft).
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->reportCardService->delete($id);

            return redirect()->route('admin.raport.index')
                ->with('success', 'Raport berhasil dihapus.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
