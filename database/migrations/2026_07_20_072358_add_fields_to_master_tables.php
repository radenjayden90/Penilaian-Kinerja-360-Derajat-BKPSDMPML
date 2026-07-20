<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('description');
            $table->softDeletes();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('description');
            $table->softDeletes();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('gender');
        });

        Schema::table('periods', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('assessment_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('display_order');
            $table->softDeletes();
        });

        Schema::table('assessment_indicators', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropSoftDeletes();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropSoftDeletes();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('periods', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('assessment_categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropSoftDeletes();
        });

        Schema::table('assessment_indicators', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
