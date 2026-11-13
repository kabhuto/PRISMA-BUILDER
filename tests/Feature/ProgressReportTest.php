<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\ProgressReport;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressReportTest extends TestCase
{
    use RefreshDatabase;

    private function survey(): Survey
    {
        return Survey::query()->create([
            'title' => 'Survei Lapangan', 'form_code' => 'UJI', 'activity' => 'Pendataan Uji', 'settings' => Survey::DEFAULT_SETTINGS,
            'blocks' => [['id' => 'b-1', 'title' => 'BLOK', 'layout' => 'full', 'condition' => null, 'questions' => [
                ['id' => 'q-nama', 'type' => 'short', 'label' => 'Nama', 'required' => false, 'code_length' => 0, 'options' => []],
            ]]],
            'published_at' => now(),
        ]);
    }

    public function test_an_admin_sets_the_deadline_and_report_days_which_reach_the_calendar(): void
    {
        $survey = $this->survey();
        $this->actingAs(User::factory()->employee()->create());

        $this->get(route('report.create', $survey))->assertNotFound();

        $start = now()->subDays(3)->toDateString();
        $deadline = now()->addDays(2)->toDateString();
        $this->put(route('progress.schedule', $survey), ['collect_start' => $start, 'collect_deadline' => $deadline, 'report_every' => 'daily', 'report_time' => '16:00'])
            ->assertRedirect(route('progress.index', $survey));

        $survey->refresh();
        $this->assertSame('Setiap hari pukul 16:00', $survey->reportScheduleLabel());
        $this->assertCount(6, $survey->reportDueDates(now()->subMonth(), now()->addMonth()));

        $this->get(route('dashboard', ['bulan' => now()->addDays(2)->format('Y-m')]))->assertSee('Tenggat pendataan: Survei Lapangan');
        $this->get(route('surveys.index'))->assertSee('Belum ada laporan progres')->assertSee('Tenggat pendataan 2 hari lagi');

        $this->put(route('progress.schedule', $survey), ['report_every' => 'weekly'])->assertSessionHasErrors('collect_deadline');
    }

    public function test_petugas_report_through_the_link_and_collect_through_the_api(): void
    {
        $survey = $this->survey();
        $survey->update(['collect_deadline' => now()->addWeek(), 'report_every' => 'weekly', 'report_weekday' => 5]);

        $this->get(route('report.create', $survey))->assertOk()->assertSee('LAPORAN PROGRES PETUGAS')->assertSee('Setiap Jumat');
        $this->post(route('report.store', $survey), ['reporter' => 'Budi', 'done' => 12, 'target' => 40, 'status' => 'kendala'])
            ->assertSessionHasErrors(['obstacle' => 'Ceritakan kendalanya.']);
        $this->post(route('report.store', $survey), ['reporter' => 'Budi', 'area' => 'Desa Tengket', 'done' => 12, 'target' => 40, 'status' => 'kendala', 'obstacle' => 'Responden sering tidak di rumah'])
            ->assertRedirect(route('report.create', $survey));

        $token = ApiToken::issue('Collect', true)[1];
        $this->withToken($token)->postJson(route('api.reports.store', $survey), ['reporter' => 'Sari', 'done' => 30, 'target' => 30, 'status' => 'lancar'])
            ->assertCreated()->assertJsonPath('data.percent', 100)->assertJsonPath('data.source', 'api');
        $this->withToken($token)->getJson(route('api.reports.index', $survey))->assertOk()
            ->assertJsonCount(2, 'data')->assertJsonPath('schedule.report_every', 'weekly');

        $this->actingAs(User::factory()->employee()->create());
        $this->get(route('progress.index', $survey))->assertOk()
            ->assertSee('Responden sering tidak di rumah')->assertSee('Desa Tengket')->assertSee('2 petugas melapor')->assertSee('30%');

        $report = ProgressReport::query()->where('reporter', 'Budi')->sole();
        $this->delete(route('progress.destroy', [$survey, $report]))->assertRedirect();
        $this->assertDatabaseCount('progress_reports', 1);
    }
}
