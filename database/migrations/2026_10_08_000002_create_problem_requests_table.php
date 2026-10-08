<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('automation_job_id')->nullable()->constrained()->nullOnDelete();

            // The client's answers
            $table->string('title');
            $table->text('description');
            $table->text('already_tried')->nullable();
            $table->text('current_tools')->nullable();
            $table->unsignedInteger('staff_count')->nullable();
            $table->string('urgency');      // low | medium | high
            $table->string('help_wanted');  // advice | build

            // processing → pending_review → approved, or processing → failed
            $table->string('status')->default('processing');
            $table->longText('draft_report')->nullable();
            $table->longText('final_report')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_requests');
    }
};
