<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentReportCard;
use App\Models\Teacher;
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

    // Daftar raport kelas yang dipegang guru ini sebagai wali kelas.
    public function index(Request $request): View
    {
        $teacher  = Teacher::where('user_id', auth()->id())->firstOrFail();
        $filters  = array_merge(
            $request->only(['period_id', 'status']),
            ['homeroom_teacher_id' => $teacher->teacher_id]
        );
        $raports   = $this->reportCardService->getAll($filters);
        $periods   = $this->periodService->getAll();
        $classroom = ClassRoom::where('homeroom_teacher_id', $teacher->teacher_id)->first();

        return view('guru.raport.index', compact('raports', 'periods', 'classroom', 'filters'));
    }

    // Form buat raport baru untuk siswa di kelas sendiri.
    public function create(): View
    {
        $teacher   = Teacher::where('user_id', auth()->id())->firstOrFail();
        $classroom = ClassRoom::where('homeroom_teacher_id', $teacher->teacher_id)->firstOrFail();
        $periods   = $this->periodService->getAll();
        $active    = $this->periodService->getActive();
        $students  = Student::where('class_id', $classroom->class_id)->orderBy('nama_siswa')->get();

        return view('guru.raport.create', compact('periods', 'students', 'classroom', 'active', 'teacher'));
    }

    // Simpan raport baru oleh guru — hanya untuk siswa di kelasnya.
    public function store(Request $request): RedirectResponse
    {
        $teacher  = Teacher::where('user_id', auth()->id())->firstOrFail();
        $data     = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'period_id'  => 'required|exists:academic_periods,period_id',
        ]);
        $student  = Student::findOrFail($data['student_id']);

        // Pastikan siswa memang di kelas yang dipegang guru ini
        $classroom = ClassRoom::where('homeroom_teacher_id', $teacher->teacher_id)
            ->where('class_id', $student->class_id)
            ->firstOrFail();

        try {
            $raport = $this->reportCardService->create([
                'student_id'          => $student->student_id,
                'period_id'           => $data['period_id'],
                'class_id'            => $classroom->class_id,
                'homeroom_teacher_id' => $teacher->teacher_id,
            ]);

            return redirect()->route('guru.raport.edit', $raport->report_card_id)
                ->with('success', 'Raport berhasil dibuat. Silakan isi penilaian.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Halaman edit raport. Hanya wali kelas yang bersangkutan yang boleh akses.
    public function edit(int $id): View
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $raport  = $this->reportCardService->getById($id);
        abort_if($raport->homeroom_teacher_id !== $teacher->teacher_id, 403);

        $narratives = $this->narrativeService->getByReportCard($id)->keyBy('judul');
        $checklists = $this->checklistService->getByReportCard($id);
        $categories = $this->checklistService->getAllCategories();
        $physical   = $this->physicalService->getByReportCard($id);
        $health     = $this->healthService->getByReportCard($id);

        return view('guru.raport.edit', compact(
            'raport', 'narratives', 'checklists', 'categories', 'physical', 'health'
        ));
    }

    // Simpan narasi penilaian.
    public function updateNarrative(Request $request, int $id): RedirectResponse
    {
        $this->authorizeEdit($id);
        $data = $request->validate([
            'narratives'               => 'required|array',
            'narratives.*.judul'       => 'required|string|max:255',
            'narratives.*.kategori'    => 'required|in:intrakurikuler,kokurikuler',
            'narratives.*.isi_naratif' => 'nullable|string',
        ]);

        $this->narrativeService->upsert($id, $data['narratives']);

        return redirect()->route('guru.raport.edit', $id)
            ->with('success', 'Narasi berhasil disimpan.');
    }

    // Simpan checklist perkembangan.
    public function updateChecklist(Request $request, int $id): RedirectResponse
    {
        $this->authorizeEdit($id);
        $data = $request->validate([
            'checklists'               => 'nullable|array',
            'checklists.*.category_id' => 'required|exists:development_categories,category_id',
            'checklists.*.status'      => 'required|in:BM,MM,SM',
            'checklists.*.catatan'     => 'nullable|string',
        ]);

        $this->checklistService->upsert($id, $data['checklists'] ?? []);

        return redirect()->route('guru.raport.edit', $id)
            ->with('success', 'Checklist perkembangan berhasil disimpan.');
    }

    // Simpan data fisik dan kondisi kesehatan.
    public function updatePhysical(Request $request, int $id): RedirectResponse
    {
        $this->authorizeEdit($id);
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

        return redirect()->route('guru.raport.edit', $id)
            ->with('success', 'Data fisik dan kesehatan berhasil disimpan.');
    }

    // Submit raport draft → submitted untuk persetujuan admin.
    public function submit(int $id): RedirectResponse
    {
        $this->authorizeEdit($id);

        try {
            $this->reportCardService->submit($id);

            return redirect()->route('guru.raport.index')
                ->with('success', 'Raport berhasil disubmit untuk persetujuan.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Cek kepemilikan raport: hanya wali kelas yang bersangkutan yang boleh edit.
    private function authorizeEdit(int $id): void
    {
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();
        $raport  = StudentReportCard::findOrFail($id);
        abort_if($raport->homeroom_teacher_id !== $teacher->teacher_id, 403);
    }
}
