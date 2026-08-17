<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            // Instagram's Graph API rejects feed posts with no image_url — caption
            // alone isn't a valid post. Nothing populates this column yet (neither
            // content_generator.py nor any image-generation job exists) — see
            // SETUP-NOTES.md "Instagram posting is blocked" for what's missing.
            $table->string('media_url')->nullable()->after('hashtags');
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->dropColumn('media_url');
        });
    }
};
