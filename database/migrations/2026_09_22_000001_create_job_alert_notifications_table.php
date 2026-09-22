<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_alert_notifications', function (Blueprint $table) {
            $table->id();

            // Flux's own job id (e.g. "remoteok-a1b2c3d4e5f6") — not a foreign
            // key, since the jobs themselves live in Flux's own jobs.json, not
            // this database. This table only remembers what's already been
            // emailed so job_alert_notifier.py never sends the same match twice.
            $table->string('flux_job_id')->unique();

            $table->string('title');
            $table->string('company');
            $table->unsignedTinyInteger('score');
            $table->timestamp('notified_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_alert_notifications');
    }
};
