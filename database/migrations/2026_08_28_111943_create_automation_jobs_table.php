<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_jobs', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('business_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('type');

            $table->string('status')
                ->default('pending');

            $table->json('payload');

            $table->json('result')
                ->nullable();

            $table->text('error_message')
                ->nullable();

            $table->unsignedInteger('attempts')
                ->default(0);

            $table->timestamp('queued_at')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamp('failed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'type',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_jobs');
    }
};