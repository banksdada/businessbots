<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 20); // instagram, linkedin, gbp, youtube
            $table->text('caption');
            $table->string('hashtags')->nullable();
            $table->timestamp('scheduled_time');
            $table->timestamp('posted_at')->nullable();
            $table->string('post_id')->nullable(); // external platform post ID
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('reach')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->timestamps();

            $table->index(['business_id', 'posted_at']);
            $table->index(['scheduled_time', 'posted_at']); // used by the hourly PostSchedulerJob
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_posts');
    }
};
