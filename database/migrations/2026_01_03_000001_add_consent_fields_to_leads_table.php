<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Every inbound WhatsApp message is itself consent to reply within Meta's
            // 24h customer service window — but we still track it explicitly, and give
            // an unambiguous, permanent way to opt out (STOP), which we must always honor.
            $table->boolean('opted_out')->default(false)->after('status');
            $table->timestamp('opted_out_at')->nullable()->after('opted_out');
            $table->timestamp('consent_notice_sent_at')->nullable()->after('opted_out_at');

            $table->index(['business_id', 'phone', 'opted_out']);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['opted_out', 'opted_out_at', 'consent_notice_sent_at']);
        });
    }
};
