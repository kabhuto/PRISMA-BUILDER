<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two kinds of admin (superadmin and admin/pegawai), who made each form, forms a superadmin hides,
     * the collection deadline and progress-report schedule, the reports themselves, templates kept in
     * the database, and the app settings a superadmin changes (menu, tools).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('username');
        });
        // Everyone who signed in until now had full access: they stay superadmins.
        DB::table('users')->update(['role' => 'superadmin']);

        Schema::table('surveys', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->timestamp('hidden_at')->nullable()->after('closed_at');
            $table->date('collect_start')->nullable()->after('hidden_at');
            $table->date('collect_deadline')->nullable()->after('collect_start');
            $table->string('report_every', 20)->nullable()->after('collect_deadline');
            $table->unsignedTinyInteger('report_weekday')->nullable()->after('report_every');
            $table->string('report_time', 5)->nullable()->after('report_weekday');
        });
        // Forms made before authors were kept belong to the first admin.
        if ($first = DB::table('users')->orderBy('id')->value('id')) {
            DB::table('surveys')->whereNull('user_id')->update(['user_id' => $first]);
        }

        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('reporter', 100);
            $table->string('area', 150)->nullable();
            $table->date('report_date');
            $table->unsignedInteger('done')->default(0);
            $table->unsignedInteger('target')->nullable();
            $table->string('status', 20)->default('lancar');
            $table->text('obstacle')->nullable();
            $table->text('plan')->nullable();
            $table->string('source', 20)->default('web');
            $table->timestamps();
            $table->index(['survey_id', 'report_date']);
        });

        Schema::create('survey_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->json('blocks');
            $table->boolean('hidden')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key', 60)->primary();
            $table->json('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('survey_templates');
        Schema::dropIfExists('progress_reports');
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['hidden_at', 'collect_start', 'collect_deadline', 'report_every', 'report_weekday', 'report_time']);
        });
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role'));
    }
};
