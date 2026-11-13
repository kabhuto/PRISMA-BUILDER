<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyTemplate;
use App\Support\AppSettings;
use App\Support\SurveyTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * "Kelola" (superadmin): the order of the menu and which menus, tools, question types, templates,
 * and forms admins (pegawai) may use.
 */
class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'menu' => collect(AppSettings::menuOrder())->map(fn (string $key): array => ['key' => $key, ...AppSettings::MENU[$key]]),
            'hiddenMenus' => AppSettings::hiddenMenus(),
            'tools' => AppSettings::TOOLS,
            'hiddenTools' => AppSettings::hiddenTools(),
            'questionTypes' => Survey::QUESTION_TYPES,
            'hiddenTypes' => AppSettings::hiddenQuestionTypes(),
            'builtIn' => SurveyTemplates::builtIn(),
            'hiddenTemplates' => SurveyTemplates::hiddenBuiltIns(),
            'customTemplates' => SurveyTemplate::query()->with('creator:id,name')->orderBy('sort')->orderBy('id')->get(),
            'surveys' => Survey::query()->with('creator:id,name')->withCount('responses')->latest('updated_at')->get(),
        ]);
    }

    /**
     * The menu: its order (one key per row, top to bottom) and the menus admins see (ticked).
     */
    public function menu(Request $request): RedirectResponse
    {
        $keys = array_keys(AppSettings::MENU);
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => [Rule::in($keys)],
            'visible' => ['array'],
            'visible.*' => [Rule::in($keys)],
        ]);
        AppSettings::put('menu_order', array_values(array_unique($data['order'])));
        AppSettings::put('hidden_menus', array_values(array_filter(array_diff($keys, $data['visible'] ?? []), fn (string $key): bool => ! AppSettings::MENU[$key]['fixed'])));

        return redirect()->to(route('settings.index').'#menu')->with('status', 'Urutan dan tampilan menu disimpan.');
    }

    /**
     * Tools and question types: what is ticked stays on for admins.
     */
    public function tools(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tools' => ['array'],
            'tools.*' => [Rule::in(array_keys(AppSettings::TOOLS))],
            'types' => ['array'],
            'types.*' => [Rule::in(array_keys(Survey::QUESTION_TYPES))],
        ]);
        AppSettings::put('hidden_tools', array_values(array_diff(array_keys(AppSettings::TOOLS), $data['tools'] ?? [])));
        AppSettings::put('hidden_question_types', array_values(array_diff(array_keys(Survey::QUESTION_TYPES), $data['types'] ?? [])));

        return redirect()->to(route('settings.index').'#alat')->with('status', 'Alat dan jenis isian yang boleh dipakai disimpan.');
    }

    /**
     * Shows or hides a form for admins: a hidden form is out of their lists and its links close.
     */
    public function survey(Request $request, Survey $survey): RedirectResponse
    {
        $hide = $request->boolean('hidden');
        $survey->update(['hidden_at' => $hide ? now() : null]);

        return redirect()->to(route('settings.index').'#kuesioner')->with('status', "\"{$survey->title}\" ".($hide ? 'disembunyikan.' : 'ditampilkan lagi.'));
    }
}
