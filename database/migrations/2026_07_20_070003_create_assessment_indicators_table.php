<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("assessment_indicators", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("category_id")->constrained("assessment_categories")->cascadeOnDelete();
            $table->string("indicator");
            $table->text("description")->nullable();
            $table->integer("display_order")->default(0);
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists("assessment_indicators");
    }
};
