<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("assessment_results", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("employee_id")->constrained("employees")->cascadeOnDelete();
            $table->foreignUuid("period_id")->constrained("periods")->cascadeOnDelete();
            $table->decimal("superior_score", 5, 2)->nullable();
            $table->decimal("peer_score", 5, 2)->nullable();
            $table->decimal("subordinate_score", 5, 2)->nullable();
            $table->decimal("final_score", 5, 2)->nullable();
            $table->string("category")->nullable();
            $table->timestamp("calculated_at")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("assessment_results");
    }
};
