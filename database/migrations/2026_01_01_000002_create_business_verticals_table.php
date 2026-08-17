<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_verticals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('vertical_type', 50); // care, cleaning, real_estate, fitness, trades, beauty, legal, automotive
            $table->json('industry_config')->nullable(); // tone, topics, hashtags, lead_questions — overrides the vertical_configs default
            $table->timestamps();

            $table->unique('business_id');
            $table->index('vertical_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_verticals');
    }
};
