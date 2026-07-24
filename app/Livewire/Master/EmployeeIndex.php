<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Services\EmployeeService;

class EmployeeIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $department_id = '';
    public $position_id = '';
    public $status = '';
    public $role_id = '';
    
    public $perPage = 10;
    public $sortColumn = 'name';
    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartmentId()
    {
        $this->resetPage();
    }

    public function updatingPositionId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render(EmployeeService $employeeService)
    {
        $employees = $employeeService->getPaginated(
            $this->search,
            $this->department_id,
            $this->position_id,
            $this->role_id,
            $this->status !== '' ? $this->status : null,
            $this->perPage,
            $this->sortColumn,
            $this->sortDirection
        );

        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('livewire.master.employee-index', [
            'employees' => $employees,
            'departments' => $departments,
            'positions' => $positions,
            'roles' => $roles,
        ]);
    }
}
