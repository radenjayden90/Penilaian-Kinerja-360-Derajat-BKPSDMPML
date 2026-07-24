<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\Period;
use App\Models\AssessmentResult;
use App\Services\Export\AssessmentResultExporter;
use App\Services\Export\AssessmentHistoryExporter;
use App\Services\Export\ReportExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessment_result_exporter_generates_spreadsheet()
    {
        $period = Period::factory()->create(['name' => 'Periode 2026', 'year' => 2026, 'month' => 7]);
        $employee = Employee::factory()->create(['name' => 'Hendra Setiawan', 'nip' => '198502142010011004']);
        $result = AssessmentResult::factory()->create([
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'final_score' => 85.50,
            'category' => \App\Enums\ResultCategory::GOOD,
        ]);

        $exporter = new AssessmentResultExporter($result);
        $spreadsheet = $exporter->export();

        $this->assertEquals(2, $spreadsheet->getSheetCount());
        $this->assertEquals('Ringkasan Penilaian', $spreadsheet->getSheet(0)->getTitle());
        $this->assertEquals('Detail Penilaian', $spreadsheet->getSheet(1)->getTitle());
    }

    public function test_assessment_history_exporter_generates_spreadsheet()
    {
        $period1 = Period::factory()->create(['name' => 'Periode 2025', 'year' => 2025, 'month' => 12]);
        $period2 = Period::factory()->create(['name' => 'Periode 2026', 'year' => 2026, 'month' => 6]);
        $employee = Employee::factory()->create(['name' => 'Hendra Setiawan', 'nip' => '198502142010011004']);
        
        $res1 = AssessmentResult::factory()->create([
            'employee_id' => $employee->id,
            'period_id' => $period1->id,
            'final_score' => 80.00,
            'category' => \App\Enums\ResultCategory::GOOD,
        ]);
        $res2 = AssessmentResult::factory()->create([
            'employee_id' => $employee->id,
            'period_id' => $period2->id,
            'final_score' => 88.00,
            'category' => \App\Enums\ResultCategory::GOOD,
        ]);

        $exporter = new AssessmentHistoryExporter($employee, collect([$res2, $res1]));
        $spreadsheet = $exporter->export();

        $this->assertEquals(4, $spreadsheet->getSheetCount());
        $this->assertEquals('Ringkasan Penilaian', $spreadsheet->getSheet(0)->getTitle());
    }

    public function test_report_exporter_generates_spreadsheet()
    {
        $period = Period::factory()->create(['name' => 'Periode 2026', 'year' => 2026, 'month' => 7]);
        $employee = Employee::factory()->create(['name' => 'Hendra Setiawan', 'nip' => '198502142010011004']);
        $results = AssessmentResult::factory()->count(1)->create([
            'employee_id' => $employee->id,
            'period_id' => $period->id,
            'final_score' => 92.50,
            'category' => \App\Enums\ResultCategory::VERY_GOOD,
        ]);

        $exporter = new ReportExporter($results, $period, null);
        $spreadsheet = $exporter->export();

        $this->assertEquals(4, $spreadsheet->getSheetCount());
        $this->assertEquals('Ringkasan Penilaian', $spreadsheet->getSheet(0)->getTitle());
    }

    public function test_export_excel_routes()
    {
        $employee = Employee::factory()->create([
            'email' => 'hendra@pemalang.go.id',
            'nip' => '198502142010011004',
        ]);
        $period = Period::factory()->create(['month' => 7]);
        $result = AssessmentResult::factory()->create([
            'employee_id' => $employee->id,
            'period_id' => $period->id,
        ]);

        $response = $this->actingAs($employee)->get(route('assessment.exportExcel', $result->id));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responseReport = $this->actingAs($employee)->get(route('report.exportCsv'));
        $responseReport->assertStatus(200);
        $responseReport->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
