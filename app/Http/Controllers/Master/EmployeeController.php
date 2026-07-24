<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Http\Requests\Master\StoreEmployeeRequest;
use App\Http\Requests\Master\UpdateEmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        return view('master.employees.index');
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $supervisors = Employee::where('is_active', true)
            ->whereHas('role', function($q) {
                $q->where('name', 'HEAD');
            })
            ->with(['role', 'position'])->orderBy('name')->get();
        
        return view('master.employees.create', compact('departments', 'positions', 'roles', 'supervisors'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->employeeService->create($request->validated());
        return redirect()->route('master.employees.index')->with('success', 'Data Pegawai berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        return view('master.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $supervisors = Employee::where('is_active', true)
            ->where('id', '!=', $employee->id)
            ->whereHas('role', function($q) {
                $q->where('name', 'HEAD');
            })
            ->with(['role', 'position'])->orderBy('name')->get();
        
        return view('master.employees.edit', compact('employee', 'departments', 'positions', 'roles', 'supervisors'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->employeeService->update($employee, $request->validated());
        return redirect()->route('master.employees.index')->with('success', 'Data Pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $this->employeeService->delete($employee);
        return redirect()->route('master.employees.index')->with('success', 'Data Pegawai berhasil dihapus.');
    }
}
