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
        Schema::table('assessment_results', function (Blueprint $table) {
            // Drop old columns to rename/recreate them
            $table->dropColumn(['superior_score', 'peer_score', 'subordinate_score']);
            
            // Add new averages
            $table->decimal('superior_average', 5, 2)->nullable()->after('period_id');
            $table->decimal('peer_average', 5, 2)->nullable()->after('superior_average');
            $table->decimal('subordinate_average', 5, 2)->nullable()->after('peer_average');
            
            // Add weights
            $table->decimal('superior_weight', 4, 2)->nullable()->after('subordinate_average'); // e.g., 0.50
            $table->decimal('peer_weight', 4, 2)->nullable()->after('superior_weight');
            $table->decimal('subordinate_weight', 4, 2)->nullable()->after('peer_weight');
            
            // Status and Pending Reason
            $table->string('status')->default('READY')->after('category');
            $table->text('pending_reason')->nullable()->after('status');

            // Add unique constraint
            $table->unique(['employee_id', 'period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_results', function (Blueprint $table) {
            $table->dropUnique(['employee_id', 'period_id']);
            
            $table->dropColumn([
                'superior_average', 'peer_average', 'subordinate_average',
                'superior_weight', 'peer_weight', 'subordinate_weight',
                'status', 'pending_reason'
            ]);
            
            $table->decimal('superior_score', 5, 2)->nullable();
            $table->decimal('peer_score', 5, 2)->nullable();
            $table->decimal('subordinate_score', 5, 2)->nullable();
        });
    }
};
