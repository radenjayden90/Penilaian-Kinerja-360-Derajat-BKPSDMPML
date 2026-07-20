<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Models\AssessmentCategory;
use App\Http\Requests\Master\StoreAssessmentCategoryRequest;
use App\Http\Requests\Master\UpdateAssessmentCategoryRequest;
use App\Services\AssessmentCategoryService;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
    protected $assessmentCategoryService;

    public function __construct(AssessmentCategoryService $assessmentCategoryService)
    {
        $this->assessmentCategoryService = $assessmentCategoryService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);
        $sortColumn = $request->input('sort_column', 'display_order');
        $sortDirection = $request->input('sort_direction', 'asc');

        $categories = $this->assessmentCategoryService->getPaginated($search, $status, $perPage, $sortColumn, $sortDirection);

        return view('master.assessment_categories.index', compact('categories', 'search', 'status', 'perPage', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        return view('master.assessment_categories.create');
    }

    public function store(StoreAssessmentCategoryRequest $request)
    {
        $this->assessmentCategoryService->create($request->validated());
        return redirect()->route('master.assessment-categories.index')->with('success', 'Aspek Penilaian berhasil ditambahkan.');
    }

    public function show(AssessmentCategory $assessment_category)
    {
        return view('master.assessment_categories.show', compact('assessment_category'));
    }

    public function edit(AssessmentCategory $assessment_category)
    {
        return view('master.assessment_categories.edit', compact('assessment_category'));
    }

    public function update(UpdateAssessmentCategoryRequest $request, AssessmentCategory $assessment_category)
    {
        $this->assessmentCategoryService->update($assessment_category, $request->validated());
        return redirect()->route('master.assessment-categories.index')->with('success', 'Aspek Penilaian berhasil diperbarui.');
    }

    public function destroy(AssessmentCategory $assessment_category)
    {
        $this->assessmentCategoryService->delete($assessment_category);
        return redirect()->route('master.assessment-categories.index')->with('success', 'Aspek Penilaian berhasil dihapus.');
    }
}
