<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Models\AssessmentIndicator;
use App\Models\AssessmentCategory;
use App\Http\Requests\Master\StoreAssessmentIndicatorRequest;
use App\Http\Requests\Master\UpdateAssessmentIndicatorRequest;
use App\Services\AssessmentIndicatorService;
use Illuminate\Http\Request;

class AssessmentIndicatorController extends Controller
{
    protected $indicatorService;

    public function __construct(AssessmentIndicatorService $indicatorService)
    {
        $this->indicatorService = $indicatorService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);
        $sortColumn = $request->input('sort_column', 'display_order');
        $sortDirection = $request->input('sort_direction', 'asc');

        $indicators = $this->indicatorService->getPaginated($search, $categoryId, $status, $perPage, $sortColumn, $sortDirection);
        $categories = AssessmentCategory::orderBy('display_order')->get();

        return view('master.assessment_indicators.index', compact('indicators', 'categories', 'search', 'categoryId', 'status', 'perPage', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        $categories = AssessmentCategory::where('is_active', true)->orderBy('display_order')->get();
        return view('master.assessment_indicators.create', compact('categories'));
    }

    public function store(StoreAssessmentIndicatorRequest $request)
    {
        $this->indicatorService->create($request->validated());
        return redirect()->route('master.assessment-indicators.index')->with('success', 'Indikator Penilaian berhasil ditambahkan.');
    }

    public function show(AssessmentIndicator $assessment_indicator)
    {
        return view('master.assessment_indicators.show', compact('assessment_indicator'));
    }

    public function edit(AssessmentIndicator $assessment_indicator)
    {
        $categories = AssessmentCategory::where('is_active', true)->orderBy('display_order')->get();
        return view('master.assessment_indicators.edit', compact('assessment_indicator', 'categories'));
    }

    public function update(UpdateAssessmentIndicatorRequest $request, AssessmentIndicator $assessment_indicator)
    {
        $this->indicatorService->update($assessment_indicator, $request->validated());
        return redirect()->route('master.assessment-indicators.index')->with('success', 'Indikator Penilaian berhasil diperbarui.');
    }

    public function destroy(AssessmentIndicator $assessment_indicator)
    {
        $this->indicatorService->delete($assessment_indicator);
        return redirect()->route('master.assessment-indicators.index')->with('success', 'Indikator Penilaian berhasil dihapus.');
    }
}
