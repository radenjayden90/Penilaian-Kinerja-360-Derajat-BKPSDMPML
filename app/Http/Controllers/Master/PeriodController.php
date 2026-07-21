<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Http\Requests\Master\StorePeriodRequest;
use App\Http\Requests\Master\UpdatePeriodRequest;
use App\Services\PeriodService;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);
        $sortColumn = $request->input('sort_column', 'year');
        $sortDirection = $request->input('sort_direction', 'desc');

        $periods = $this->periodService->getPaginated($search, $month, $year, $status, $perPage, $sortColumn, $sortDirection);

        return view('master.periods.index', compact('periods', 'search', 'month', 'year', 'status', 'perPage', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        return view('master.periods.create');
    }

    public function store(StorePeriodRequest $request)
    {
        $this->periodService->create($request->validated());
        return redirect()->route('master.periods.index')->with('success', 'Data Periode berhasil ditambahkan.');
    }

    public function show(Period $period)
    {
        return view('master.periods.show', compact('period'));
    }

    public function edit(Period $period)
    {
        return view('master.periods.edit', compact('period'));
    }

    public function update(UpdatePeriodRequest $request, Period $period)
    {
        $this->periodService->update($period, $request->validated());
        return redirect()->route('master.periods.index')->with('success', 'Data Periode berhasil diperbarui.');
    }

    public function destroy(Period $period)
    {
        $this->periodService->delete($period);
        return redirect()->route('master.periods.index')->with('success', 'Data Periode berhasil dihapus.');
    }
}
