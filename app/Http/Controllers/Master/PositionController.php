<?php
namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department;
use App\Http\Requests\Master\StorePositionRequest;
use App\Http\Requests\Master\UpdatePositionRequest;
use App\Services\PositionService;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    protected $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $departmentId = $request->input('department_id');
        $perPage = $request->input('per_page', 10);
        $sortColumn = $request->input('sort_column', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');

        $positions = $this->positionService->getPaginated($search, $departmentId, $status, $perPage, $sortColumn, $sortDirection);
        $departments = Department::orderBy('name')->get();

        return view('master.positions.index', compact('positions', 'departments', 'search', 'departmentId', 'status', 'perPage', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('master.positions.create', compact('departments'));
    }

    public function store(StorePositionRequest $request)
    {
        $this->positionService->create($request->validated());
        return redirect()->route('master.positions.index')->with('success', 'Data Jabatan berhasil ditambahkan.');
    }

    public function show(Position $position)
    {
        return view('master.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('master.positions.edit', compact('position', 'departments'));
    }

    public function update(UpdatePositionRequest $request, Position $position)
    {
        $this->positionService->update($position, $request->validated());
        return redirect()->route('master.positions.index')->with('success', 'Data Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        $this->positionService->delete($position);
        return redirect()->route('master.positions.index')->with('success', 'Data Jabatan berhasil dihapus.');
    }
}
