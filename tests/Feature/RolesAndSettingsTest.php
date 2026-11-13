<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\ApiToken;
use App\Models\Survey;
use App\Models\SurveyTemplate;
use App\Models\User;
use App\Support\AppSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RolesAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.admin_open_on_lan' => false]);
    }

    private function survey(string $title, ?User $by = null, array $extra = []): Survey
    {
        return Survey::query()->create([
            'title' => $title, 'form_code' => 'UJI', 'activity' => 'Pendataan Uji', 'settings' => Survey::DEFAULT_SETTINGS,
            'blocks' => [['id' => 'b-1', 'title' => 'BLOK SATU', 'layout' => 'full', 'condition' => null, 'questions' => [
                ['id' => 'q-nama', 'type' => 'short', 'label' => 'Nama', 'required' => false, 'code_length' => 0, 'options' => []],
            ]]],
            'user_id' => $by?->id,
            ...$extra,
        ]);
    }

    public function test_only_a_superadmin_runs_the_app(): void
    {
        $this->actingAs(User::factory()->employee()->create());
        foreach (['api-access.index', 'admins.index', 'settings.index'] as $route) {
            $this->get(route($route))->assertForbidden();
        }
        $this->get(route('surveys.index'))->assertOk()->assertDontSee(route('settings.index'));
        $this->get(route('surveys.start'))->assertOk()->assertSee('Gunakan template')->assertDontSee('Kelola template');

        $this->actingAs(User::factory()->create());
        foreach (['api-access.index', 'admins.index', 'settings.index'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('surveys.index'))->assertSee(route('settings.index'));
    }

    public function test_the_phone_bar_follows_the_menu_order_and_hidden_menus_close_for_admins(): void
    {
        $superadmin = User::factory()->create();
        $this->actingAs($superadmin)->put(route('settings.menu'), [
            'order' => ['beranda', 'riwayat', 'referensi', 'panduan', 'dashboard'],
            'visible' => ['beranda', 'riwayat', 'referensi', 'panduan'],
        ])->assertRedirect();

        $this->assertSame(['dashboard'], AppSettings::hiddenMenus());
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('surveys.index'))->assertSeeInOrder(['>Beranda<', '>Riwayat<', 'nav-add-label', '>Data referensi<'], false);

        $this->actingAs(User::factory()->employee()->create());
        $this->get(route('dashboard'))->assertForbidden();
        $this->get(route('surveys.index'))->assertOk()->assertDontSee('href="'.route('dashboard').'"', false);
    }

    public function test_switched_off_tools_and_question_types_are_kept_from_admins(): void
    {
        $survey = $this->survey('Survei Alat');
        $this->actingAs(User::factory()->create())->put(route('settings.tools'), [
            'tools' => array_values(array_diff(array_keys(AppSettings::TOOLS), ['penugasan'])),
            'types' => array_values(array_diff(array_keys(Survey::QUESTION_TYPES), ['signature'])),
        ])->assertRedirect();

        $this->get(route('assignments.index', $survey))->assertOk();
        $this->get(route('surveys.edit', $survey))->assertSee('data-tool="signature"', false);

        $this->actingAs(User::factory()->employee()->create());
        $this->get(route('assignments.index', $survey))->assertForbidden();
        $this->get(route('surveys.edit', $survey))->assertOk()->assertDontSee('data-tool="signature"', false);
    }

    public function test_a_hidden_form_is_out_of_reach_for_admins_and_respondents(): void
    {
        $survey = $this->survey('Survei Rahasia', extra: ['published_at' => now()]);
        $this->actingAs(User::factory()->create())->put(route('settings.survey', $survey), ['hidden' => 1])->assertRedirect();
        $this->assertTrue($survey->fresh()->isHidden());
        $this->get(route('surveys.index'))->assertSee('Survei Rahasia')->assertSee('Disembunyikan');

        $this->actingAs(User::factory()->employee()->create());
        $this->get(route('surveys.index'))->assertDontSee('Survei Rahasia');
        $this->get(route('surveys.edit', $survey))->assertNotFound();

        auth()->logout();
        $this->get(route('form.create', $survey))->assertNotFound();
        $token = ApiToken::issue('Uji', false)[1];
        $this->withToken($token)->getJson(route('api.surveys.index'))->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_the_bell_rings_for_agenda_in_the_next_days_only(): void
    {
        $this->actingAs(User::factory()->employee()->create());
        Agenda::query()->create(['title' => 'Briefing petugas besok', 'kind' => 'briefing', 'date' => now()->addDay()->toDateString(), 'time' => '08:00']);
        Agenda::query()->create(['title' => 'Rapat bulan depan', 'kind' => 'rapat', 'date' => now()->addDays(20)->toDateString()]);

        $this->get(route('history.index'))->assertOk()
            ->assertSee('data-bell', false)
            ->assertSeeInOrder(['Notifikasi', 'Besok', 'Briefing petugas besok', 'Pukul 08:00'])
            ->assertDontSee('Rapat bulan depan')
            ->assertSee('Mode gelap');
    }

    public function test_beranda_shows_everyones_forms_and_riwayat_only_ones_own(): void
    {
        $me = User::factory()->employee()->create(['name' => 'Saya']);
        $other = User::factory()->employee()->create(['name' => 'Rekan Kerja']);
        $this->survey('Kuesioner Saya', $me);
        $this->survey('Kuesioner Rekan', $other);

        $this->actingAs($me);
        $this->get(route('surveys.index'))->assertOk()->assertSee('Kuesioner Saya')->assertSee('Kuesioner Rekan')->assertSee('oleh Rekan Kerja');
        $this->get(route('history.index'))->assertOk()->assertSee('Kuesioner Saya')->assertDontSee('Kuesioner Rekan')->assertSee('Kuesioner dibuat: Kuesioner Saya');

        $this->post(route('surveys.store'), [])->assertSessionHasErrors();
    }

    public function test_a_superadmin_swaps_the_logos_and_admins_cannot(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->employee()->create());
        $this->put(route('settings.logo', 'navbar'), ['logo' => UploadedFile::fake()->image('logo.png', 120, 40)])->assertForbidden();

        $this->actingAs(User::factory()->create());
        $this->put(route('settings.logo', 'navbar'), ['logo' => UploadedFile::fake()->image('logo.png', 120, 40)])->assertRedirect();
        $this->put(route('settings.logo', 'prisma'), ['logo' => UploadedFile::fake()->image('p.png', 64, 74)])->assertRedirect();
        $this->put(route('settings.logo', 'favicon'), ['logo' => UploadedFile::fake()->image('ikon.png', 64, 64)])->assertRedirect();
        $this->put(route('settings.logo', 'navbar'), ['logo' => UploadedFile::fake()->create('logo.svg', 4, 'image/svg+xml')])->assertSessionHasErrors('logo');
        $this->put(route('settings.logo', 'lain'), [])->assertNotFound();

        $navbar = AppSettings::get('logo_navbar');
        Storage::disk('public')->assertExists($navbar);
        $this->get(route('surveys.index'))->assertOk()
            ->assertSee('class="brand-logo" src="'.asset('storage/'.$navbar).'"', false)
            ->assertSee('home-mark is-uploaded', false)
            ->assertSee('<link rel="icon" href="'.asset('storage/'.AppSettings::get('logo_favicon')).'">', false);

        $this->put(route('settings.logo', 'navbar'), ['reset' => 1])->assertRedirect();
        Storage::disk('public')->assertMissing($navbar);
        $this->get(route('surveys.index'))->assertSee('<span class="brand-mark">', false);
    }

    public function test_a_superadmin_makes_templates_from_forms_and_hides_built_in_ones(): void
    {
        $source = $this->survey('Survei Kepuasan Kantor');
        $this->actingAs(User::factory()->create());

        $this->post(route('templates.store'), ['survey_id' => $source->id, 'name' => 'Kepuasan Kantor', 'description' => 'Template kantor'])->assertRedirect();
        $template = SurveyTemplate::query()->sole();
        $this->put(route('templates.built-in', 'listing'), ['hidden' => 1])->assertRedirect();

        $this->get(route('surveys.start'))->assertOk()->assertSee('Kepuasan Kantor')->assertSee('BLOK SATU')->assertDontSee('>Listing<', false);
        $this->get(route('surveys.create', ['template' => $template->key()]))->assertOk()->assertSee('BLOK SATU');

        $this->put(route('templates.update', $template), ['name' => 'Kepuasan', 'hidden' => 1])->assertRedirect();
        $this->get(route('surveys.start'))->assertDontSee('Template kantor');
        $this->delete(route('templates.destroy', $template))->assertRedirect();
        $this->assertDatabaseCount('survey_templates', 0);

        $this->actingAs(User::factory()->employee()->create());
        $this->post(route('templates.store'), ['survey_id' => $source->id, 'name' => 'Coba'])->assertForbidden();
    }
}
