<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("assessments", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("period_id")->constrained("periods")->cascadeOnDelete();
            $table->foreignUuid("assessor_id")->constrained("employees")->cascadeOnDelete();
            $table->foreignUuid("employee_id")->constrained("employees")->cascadeOnDelete();
            $table->string("assessment_type");
            $table->string("status")->default("DRAFT");
            $table->timestamp("submitted_at")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("assessments");
    }
};
