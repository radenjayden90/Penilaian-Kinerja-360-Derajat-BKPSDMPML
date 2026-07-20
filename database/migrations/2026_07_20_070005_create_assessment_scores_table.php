<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("assessment_scores", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("assessment_id")->constrained("assessments")->cascadeOnDelete();
            $table->foreignUuid("indicator_id")->constrained("assessment_indicators")->cascadeOnDelete();
            $table->integer("score");
            $table->text("comment")->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("assessment_scores");
    }
};
