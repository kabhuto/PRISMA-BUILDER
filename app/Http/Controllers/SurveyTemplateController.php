<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyTemplate;
use App\Support\AppSettings;
use App\Support\SurveyTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Templates (superadmin): make one from a form, rename it, refresh its content, hide, or delete it;
 * the built-in ones can be hidden.
 */
class SurveyTemplateController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(self::rules(true), self::messages());
        $survey = Survey::query()->findOrFail($data['survey_id']);

        SurveyTemplate::query()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'blocks' => $survey->blocks,
            'sort' => (int) SurveyTemplate::query()->max('sort') + 1,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()->to(route('settings.index').'#template')->with('status', "Template \"{$data['name']}\" dibuat dari \"{$survey->title}\".");
    }

    public function update(Request $request, SurveyTemplate $template): RedirectResponse
    {
        $data = $request->validate(self::rules(false), self::messages());
        $template->fill([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'hidden' => $request->boolean('hidden'),
            'sort' => $data['sort'] ?? $template->sort,
        ]);
        if (filled($data['survey_id'] ?? null)) {
            $template->blocks = Survey::query()->findOrFail($data['survey_id'])->blocks;
        }
        $template->save();

        return redirect()->to(route('settings.index').'#template')->with('status', "Template \"{$template->name}\" diperbarui.");
    }

    public function destroy(SurveyTemplate $template): RedirectResponse
    {
        $template->delete();

        return redirect()->to(route('settings.index').'#template')->with('status', "Template \"{$template->name}\" dihapus.");
    }

    /**
     * Hides a built-in template or shows it again. "Kosong" always stays.
     */
    public function builtIn(Request $request, string $key): RedirectResponse
    {
        abort_unless(array_key_exists($key, SurveyTemplates::builtIn()) && $key !== SurveyTemplates::DEFAULT, 404);

        $hidden = SurveyTemplates::hiddenBuiltIns();
        $hidden = $request->boolean('hidden') ? array_values(array_unique([...$hidden, $key])) : array_values(array_diff($hidden, [$key]));
        AppSettings::put('hidden_templates', $hidden);

        return redirect()->to(route('settings.index').'#template')->with('status', 'Template "'.SurveyTemplates::builtIn()[$key]['label'].'" '.($request->boolean('hidden') ? 'disembunyikan.' : 'ditampilkan.'));
    }

    /**
     * @return array<string, mixed>
     */
    private static function rules(bool $creating): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'survey_id' => [$creating ? 'required' : 'nullable', 'integer', Rule::exists('surveys', 'id')],
            'sort' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function messages(): array
    {
        return [
            'name.required' => 'Isi nama template.',
            'survey_id.required' => 'Pilih kuesioner yang dijadikan template.',
            'survey_id.exists' => 'Kuesioner tidak ditemukan.',
        ];
    }
}
