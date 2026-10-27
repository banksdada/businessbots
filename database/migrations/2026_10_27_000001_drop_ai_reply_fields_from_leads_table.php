<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = array_values(array_filter(
            ['ai_reply_sent', 'ai_reply_text', 'reply_time_seconds'],
            fn (string $column) => Schema::hasColumn('leads', $column),
        ));

        if ($columns === []) {
            return;
        }

        Schema::table('leads', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('ai_reply_sent')->default(false);
            $table->text('ai_reply_text')->nullable();
            $table->unsignedInteger('reply_time_seconds')->nullable();
        });
    }
};
