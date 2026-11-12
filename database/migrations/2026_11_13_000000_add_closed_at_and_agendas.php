<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * When a form was closed, so a finished form is told apart from one never published; and the
     * agenda of the dashboard calendar (briefings, meetings, deadlines set by hand).
     */
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('published_at');
        });

        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('kind', 20)->default('lainnya');
            $table->date('date');
            $table->time('time')->nullable();
            $table->string('place')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('survey_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('closed_at');
        });
    }
};
