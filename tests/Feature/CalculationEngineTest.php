<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Employee;
use App\Models\Period;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\AssessmentResult;
use App\Models\AssessmentIndicator;
use App\Services\AssessmentCalculatorService;
use App\Enums\CalculationStatus;
use App\Enums\ResultCategory;
use Carbon\Carbon;

class CalculationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $period;
    protected $indicator;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new AssessmentCalculatorService();
        $this->period = Period::factory()->create([
            'name' => 'Periode Test',
            'month' => 7,
            'year' => 2026,
            'status' => 'OPEN', 
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth()
        ]);
        
        $category = \App\Models\AssessmentCategory::factory()->create(['name' => 'BerAKHLAK']);
        $this->indicator = AssessmentIndicator::factory()->create(['category_id' => $category->id, 'indicator' => 'Test']);
    }

    private function createAssessment($assessor, $target, $type, $scoreValue)
    {
        $assessment = Assessment::factory()->create([
            'assessor_id' => $assessor->id,
            'employee_id' => $target->id,
            'period_id' => $this->period->id,
            'assessment_type' => $type,
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
        ]);

        AssessmentScore::factory()->create([
            'assessment_id' => $assessment->id,
            'indicator_id' => $this->indicator->id,
            'score' => $scoreValue,
        ]);

        return $assessment;
    }

    public function test_staff_calculation_with_valid_assessments()
    {
        $superior = Employee::factory()->create();
        $staff = Employee::factory()->create(['supervisor_id' => $superior->id]);
        $peers = Employee::factory()->count(3)->create();

        // 1 Superior scores 9
        $this->createAssessment($superior, $staff, 'SUPERIOR', 9);

        // 3 Peers score 8, 8, 8
        foreach ($peers as $peer) {
            $this->createAssessment($peer, $staff, 'PEER', 8);
        }

        $this->service->calculateEmployee($staff, $this->period);

        $result = AssessmentResult::where('employee_id', $staff->id)->first();

        $this->assertNotNull($result);
        $this->assertEquals(CalculationStatus::COMPLETE, $result->status);
        $this->assertEquals(0.50, $result->superior_weight);
        $this->assertEquals(0.50, $result->peer_weight);
        $this->assertEquals(0.00, $result->subordinate_weight);
        
        // (9 * 0.5) + (8 * 0.5) = 4.5 + 4.0 = 8.5 (assuming indicator score is just * 10 or similar? Wait, the scale is 1-10. So final score is 8.5)
        // Wait, if it's 8.5, then it is Needs Improvement because it's <= 60?
        // Ah, if they score 1-10, the final score will be 1-10. But the category rule says 90-100 is Very Good.
        // Did I miss multiplying by 10? The business rule says: "Contoh: 9, 8, 10, 8, 9 -> Average Assessment: 8.8." But wait, "Kategori Nilai: 90-100 Sangat Baik".
        // If average is 8.8, final score will be max 10. How can it reach 90-100?
        // The business rule implies score is out of 100? "Skala 1-10" but total is 100?
        // Let me modify the test to simulate final score properly. I will multiply by 10 in the test or service.
        // Actually, if input is 1-10, to get 10-100, we should multiply the final score by 10.
        // Let's adjust the assertion for now based on exact math: 8.5
        $this->assertEquals(85, $result->final_score);
    }

    public function test_pending_when_less_than_3_peers()
    {
        $superior = Employee::factory()->create();
        $staff = Employee::factory()->create(['supervisor_id' => $superior->id]);
        $peers = Employee::factory()->count(2)->create(); // Only 2 peers

        $this->createAssessment($superior, $staff, 'SUPERIOR', 9);
        foreach ($peers as $peer) {
            $this->createAssessment($peer, $staff, 'PEER', 8);
        }

        $this->service->calculateEmployee($staff, $this->period);

        $result = AssessmentResult::where('employee_id', $staff->id)->first();

        $this->assertEquals(CalculationStatus::PENDING, $result->status);
        $this->assertStringContainsString("kurang dari 3", $result->pending_reason);
    }

    public function test_leader_calculation_with_valid_assessments()
    {
        $superior = Employee::factory()->create();
        $leader = Employee::factory()->create(['supervisor_id' => $superior->id]);
        $peers = Employee::factory()->count(3)->create();
        $subordinates = Employee::factory()->count(2)->create(['supervisor_id' => $leader->id]);

        $this->createAssessment($superior, $leader, 'SUPERIOR', 9);
        foreach ($peers as $peer) {
            $this->createAssessment($peer, $leader, 'PEER', 8);
        }
        foreach ($subordinates as $sub) {
            $this->createAssessment($sub, $leader, 'SUBORDINATE', 10);
        }

        $this->service->calculateEmployee($leader, $this->period);

        $result = AssessmentResult::where('employee_id', $leader->id)->first();

        $this->assertEquals(CalculationStatus::COMPLETE, $result->status);
        $this->assertEquals(0.50, $result->superior_weight);
        $this->assertEquals(0.30, $result->peer_weight);
        $this->assertEquals(0.20, $result->subordinate_weight);
        
        // (9*0.5) + (8*0.3) + (10*0.2) = 4.5 + 2.4 + 2.0 = 8.9
        $this->assertEquals(89, $result->final_score);
    }
}
