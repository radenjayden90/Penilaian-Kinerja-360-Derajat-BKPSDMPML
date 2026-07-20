<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Requests\Master\StoreDepartmentRequest;
use App\Http\Requests\Master\UpdateDepartmentRequest;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);
        $sortColumn = $request->input('sort_column', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');

        $departments = $this->departmentService->getPaginated($search, $status, $perPage, $sortColumn, $sortDirection);

        return view('master.departments.index', compact('departments', 'search', 'status', 'perPage', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        return view('master.departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        $this->departmentService->create($request->validated());
        return redirect()->route('master.departments.index')->with('success', 'Data Bidang berhasil ditambahkan.');
    }

    public function show(Department $department)
    {
        return view('master.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('master.departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $this->departmentService->update($department, $request->validated());
        return redirect()->route('master.departments.index')->with('success', 'Data Bidang berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $this->departmentService->delete($department);
        return redirect()->route('master.departments.index')->with('success', 'Data Bidang berhasil dihapus.');
    }
}
