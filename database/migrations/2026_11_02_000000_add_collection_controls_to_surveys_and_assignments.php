<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            // While a field activity runs: the instrument cannot change, and only assignment links are taken.
            $table->timestamp('locked_at')->nullable()->after('published_at');
            $table->string('locked_note', 255)->nullable()->after('locked_at');
            $table->boolean('assignment_only')->default(false)->after('locked_note');
        });

        Schema::table('assignments', function (Blueprint $table) {
            // What the reviewer asks to fix, per question id.
            $table->json('notes')->nullable()->after('locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', fn (Blueprint $table) => $table->dropColumn('notes'));
        Schema::table('surveys', fn (Blueprint $table) => $table->dropColumn(['locked_at', 'locked_note', 'assignment_only']));
    }
};
