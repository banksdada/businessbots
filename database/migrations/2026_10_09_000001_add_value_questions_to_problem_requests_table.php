<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('problem_requests', function (Blueprint $table) {
            // "What is it costing you?" answers, so the plan can show what a fix is worth.
            $table->json('impacts')->nullable()->after('description');
            $table->decimal('hours_per_week', 6, 1)->nullable()->after('impacts');
            $table->unsignedInteger('hourly_cost')->nullable()->after('hours_per_week');
            $table->text('desired_outcome')->nullable()->after('hourly_cost');
        });
    }

    public function down(): void
    {
        Schema::table('problem_requests', function (Blueprint $table) {
            $table->dropColumn(['impacts', 'hours_per_week', 'hourly_cost', 'desired_outcome']);
        });
    }
};
