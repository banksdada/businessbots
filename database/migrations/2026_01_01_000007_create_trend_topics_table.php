<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trend_topics', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('source', 20); // google_trends, instagram, youtube
            $table->date('detected_date');
            $table->unsignedInteger('volume')->default(0);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamps();

            $table->index('detected_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trend_topics');
    }
};
