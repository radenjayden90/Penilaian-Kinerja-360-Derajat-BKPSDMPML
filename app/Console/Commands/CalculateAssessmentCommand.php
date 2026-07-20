<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Period;
use App\Jobs\CalculateAssessmentJob;

class CalculateAssessmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate final assessment score for all employees in the active period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Looking for OPEN period...");
        $period = Period::where('status', 'OPEN')->first();

        if (!$period) {
            $this->error("No OPEN period found.");
            return Command::FAILURE;
        }

        $this->info("OPEN period found: {$period->name}. Dispatching job...");

        // Dispatching the calculation job synchronously or to queue
        CalculateAssessmentJob::dispatchSync($period);

        $this->info("Mass calculation job dispatched successfully.");
        return Command::SUCCESS;
    }
}
