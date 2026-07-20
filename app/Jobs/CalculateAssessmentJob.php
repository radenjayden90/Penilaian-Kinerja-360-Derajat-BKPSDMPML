<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Period;
use App\Models\Employee;
use App\Services\AssessmentCalculatorService;
use Illuminate\Support\Facades\Log;

class CalculateAssessmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $period;
    protected $employee;

    /**
     * Create a new job instance.
     * If employee is null, calculate for all.
     */
    public function __construct(Period $period, ?Employee $employee = null)
    {
        $this->period = $period;
        $this->employee = $employee;
    }

    /**
     * Execute the job.
     */
    public function handle(AssessmentCalculatorService $calculator): void
    {
        if ($this->employee) {
            Log::info("Job started for calculating single employee: {$this->employee->id}");
            $calculator->calculateEmployee($this->employee, $this->period);
        } else {
            Log::info("Job started for calculating all employees in period: {$this->period->id}");
            $calculator->calculateAll($this->period);
        }
    }
}
