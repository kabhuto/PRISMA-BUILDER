<?php

namespace Tests\Feature;

use App\Models\ApiToken;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What PRISMA SM controls while a field activity runs: the instrument held still, answers only
 * through assignment links, links closing with the period, and the reviewer's notes on the form.
 */
class FieldCollectionControlsTest extends TestCase
{
    use RefreshDatabase;

    private function survey(): Survey
    {
        return Survey::query()->create([
            'title' => 'Survei Uji', 'activity' => 'Uji', 'published_at' => now(), 'settings' => Survey::DEFAULT_SETTINGS,
            'blocks' => [['id' => 'b-1', 'title' => 'A', 'layout' => 'full', 'condition' => null, 'questions' => [[
                'id' => 'q1', 'type' => 'short', 'label' => 'Nama', 'description' => '', 'required' => false, 'code_length' => 0, 'options' => [],
                'hidden' => false, 'disabled' => false, 'validation' => ['rules' => [], 'message' => '', 'checks' => []],
            ]]]],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function api(): array
    {
        [, $plain] = ApiToken::issue('uji', true);

        return ['Authorization' => 'Bearer '.$plain];
    }

    public function test_a_locked_instrument_cannot_change_until_it_is_released(): void
    {
        $survey = $this->survey();
        $headers = $this->api();

        $this->putJson("/api/v1/surveys/{$survey->id}/pendataan", ['locked' => true, 'note' => 'Kegiatan Susenas berjalan'], $headers)
            ->assertOk()->assertJsonPath('data.locked', true);
        $this->putJson("/api/v1/surveys/{$survey->id}", ['title' => 'Berubah'], $headers)->assertStatus(423);

        $admin = User::factory()->create();
        $this->actingAs($admin)->put(route('surveys.update', $survey), [
            'title' => 'Berubah', 'activity' => 'Uji',
            'settings' => ['paper_size' => 'F4', 'orientation' => 'landscape', 'period_type' => 'semester', 'period_value' => 2, 'theme' => 'blue', 'numbering' => 'block', 'confidential' => '0'],
            'header' => json_encode(['items' => []]),
            'blocks' => json_encode($survey->blocks),
        ])->assertSessionHas('error');
        $this->assertSame('Survei Uji', $survey->fresh()->title);

        $this->actingAs($admin)->post(route('surveys.unlock', $survey))->assertSessionHas('status');
        $this->assertFalse($survey->fresh()->isLocked());
    }

    public function test_assignment_only_closes_the_public_link(): void
    {
        $survey = $this->survey();
        $headers = $this->api();
        $this->putJson("/api/v1/surveys/{$survey->id}/pendataan", ['assignment_only' => true], $headers)->assertJsonPath('data.assignment_only', true);
        $link = $this->postJson("/api/v1/surveys/{$survey->id}/assignments", ['label' => 'Ruta 1'], $headers)->json('data.0.url');

        $this->get(route('form.create', $survey))->assertOk()->assertSee('hanya dapat diisi lewat tautan penugasan');
        $this->post(route('form.store', $survey), ['answers' => ['q1' => 'x']])->assertForbidden();
        $this->assertSame(0, $survey->responses()->count());
        $this->get($link)->assertOk()->assertSee('Ruta 1');
    }

    public function test_links_close_and_reopen_together_and_show_the_reviewer_notes(): void
    {
        $survey = $this->survey();
        $headers = $this->api();
        $created = $this->postJson("/api/v1/surveys/{$survey->id}/assignments", ['assignments' => [
            ['label' => 'Ruta 1', 'notes' => ['q1' => 'Nama belum sesuai KTP.', 'q-tidak-ada' => 'abaikan']],
            ['label' => 'Ruta 2'],
        ]], $headers)->assertCreated()->json('data');

        $this->get($created[0]['url'])->assertOk()->assertSee('Nama belum sesuai KTP.')->assertDontSee('abaikan');

        $ids = array_column($created, 'id');
        $this->putJson("/api/v1/surveys/{$survey->id}/assignments/kedaluwarsa", ['ids' => $ids, 'expires_at' => now()->subDay()->toIso8601String()], $headers)->assertJsonPath('data.updated', 2);
        $this->get($created[1]['url'])->assertSee('kedaluwarsa');

        $this->putJson("/api/v1/surveys/{$survey->id}/assignments/kedaluwarsa", ['ids' => $ids, 'expires_at' => null], $headers)->assertOk();
        $this->get($created[1]['url'])->assertDontSee('kedaluwarsa');
    }
}
