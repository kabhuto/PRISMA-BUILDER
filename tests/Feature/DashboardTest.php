<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    private function survey(string $title, ?string $published = null): Survey
    {
        return Survey::query()->create([
            'title' => $title, 'form_code' => 'UJI', 'activity' => 'Pendataan Uji', 'settings' => Survey::DEFAULT_SETTINGS,
            'blocks' => [['id' => 'b-1', 'title' => 'BLOK', 'layout' => 'full', 'condition' => null, 'questions' => [
                ['id' => 'q-nama', 'type' => 'short', 'label' => 'Nama', 'required' => false, 'code_length' => 0, 'options' => []],
            ]]],
            'published_at' => $published,
        ]);
    }

    public function test_closing_a_form_marks_it_finished_and_publishing_again_runs_it(): void
    {
        $survey = $this->survey('Survei Selesai', now()->toDateTimeString());
        $this->assertSame('running', $survey->status());

        $this->delete(route('published-surveys.destroy', $survey))->assertRedirect();
        $this->assertSame('done', $survey->fresh()->status());
        $this->assertSame('Selesai', $survey->fresh()->statusLabel());

        $this->post(route('published-surveys.store', $survey))->assertRedirect();
        $this->assertSame('running', $survey->fresh()->status());
        $this->assertNull($survey->fresh()->closed_at);

        // A form never published stays a draft.
        $this->assertSame('draft', $this->survey('Belum Terbit')->status());
    }

    public function test_the_dashboard_counts_forms_by_stage_and_shows_assignment_progress(): void
    {
        $running = $this->survey('Survei Berjalan', now()->toDateTimeString());
        $running->assignments()->create(['label' => 'Ruta 1']);
        $answered = $running->assignments()->create(['label' => 'Ruta 2']);
        $answered->update(['response_id' => $running->responses()->create(['answers' => ['q-nama' => 'Budi']])->id]);
        $this->survey('Survei Draf');

        $this->get(route('dashboard'))->assertOk()
            ->assertSeeInOrder(['Berjalan', '1', 'Selesai', '0', 'Draf', '1'])
            ->assertSee('1</b> dari 2 penugasan terisi', false)
            ->assertSee('Kalender tenggat &amp; agenda', false)
            ->assertSee('Kuesioner dibuat: Survei Berjalan');
    }

    public function test_agenda_is_added_to_the_calendar_shown_on_beranda_and_removed(): void
    {
        $date = now()->addDays(2)->toDateString();
        $this->post(route('agenda.store'), ['title' => 'Briefing petugas', 'kind' => 'briefing', 'date' => $date, 'time' => '09:00', 'place' => 'Aula BPS'])
            ->assertRedirect(route('dashboard', ['bulan' => substr($date, 0, 7)]).'#kalender');

        $agenda = Agenda::query()->sole();
        $this->get(route('dashboard', ['bulan' => substr($date, 0, 7)]))->assertOk()->assertSee('Briefing petugas')->assertSee('Aula BPS');
        $this->get(route('surveys.index'))->assertOk()->assertSee('Agenda terdekat')->assertSee('Briefing petugas');

        $this->post(route('agenda.store'), ['title' => '', 'kind' => 'pesta', 'date' => 'bukan'])->assertSessionHasErrors(['title', 'kind', 'date']);

        $this->delete(route('agenda.destroy', $agenda))->assertRedirect();
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_assignment_links_ending_soon_appear_as_deadlines_and_need_attention(): void
    {
        $survey = $this->survey('Survei Tenggat', now()->toDateTimeString());
        $survey->assignments()->create(['label' => 'Ruta 1', 'expires_at' => now()->addDay()]);

        $this->get(route('surveys.index'))->assertOk()
            ->assertSee('Perlu perhatian')
            ->assertSee('1 tautan penugasan berakhir')
            ->assertSee('Batas tautan penugasan: Survei Tenggat');
        $this->get(route('dashboard', ['bulan' => now()->addDay()->format('Y-m')]))->assertOk()->assertSee('Batas tautan penugasan: Survei Tenggat');
    }
}
