<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('platform', 20); // whatsapp, instagram, linkedin, gbp
            $table->text('access_token')->nullable(); // encrypted via the model cast — never store plaintext
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('external_account_id')->nullable();
            $table->string('external_account_name')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->timestamps();

            $table->unique(['business_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_settings');
    }
};
