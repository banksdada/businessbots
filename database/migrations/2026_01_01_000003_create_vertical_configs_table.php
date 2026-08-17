<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vertical_configs', function (Blueprint $table) {
            $table->id();
            $table->string('vertical_type', 50)->unique();
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->string('default_tone')->nullable();
            $table->json('default_topics')->nullable();
            $table->json('default_hashtags')->nullable();
            $table->json('lead_questions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vertical_configs');
    }
};
