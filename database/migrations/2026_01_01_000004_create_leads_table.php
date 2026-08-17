<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20);
            $table->string('name')->nullable();
            $table->text('message');
            $table->string('intent', 20)->default('other'); // inquiry, schedule, complaint, other
            $table->boolean('ai_reply_sent')->default(false);
            $table->text('ai_reply_text')->nullable();
            $table->unsignedInteger('reply_time_seconds')->nullable();
            $table->string('status', 20)->default('new'); // new, followup, closed
            $table->timestamp('scheduled_visit_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'created_at']);
            $table->index(['business_id', 'intent']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
