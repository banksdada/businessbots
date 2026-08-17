<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('intent_type', 20); // inquiry, schedule, complaint
            $table->text('template_text');
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->timestamps();

            $table->index(['business_id', 'intent_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_templates');
    }
};
