<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyTemplate;
use App\Support\AppSettings;
use App\Support\SurveyTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'logos' => collect(AppSettings::LOGOS)->map(fn (array $logo, string $key): array => $logo + ['url' => AppSettings::logo($key)]),
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
     * Uploads a new picture for a logo, or goes back to the built-in one.
     */
    public function logo(Request $request, string $which): RedirectResponse
    {
        abort_unless(array_key_exists($which, AppSettings::LOGOS), 404);
        $old = AppSettings::get('logo_'.$which);
        $label = AppSettings::LOGOS[$which]['label'];

        if ($request->boolean('reset')) {
            AppSettings::put('logo_'.$which, null);
            $message = $label.' kembali ke bawaan.';
        } else {
            $request->validate(
                ['logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,ico', 'max:2048']],
                ['logo.required' => 'Pilih gambar dulu.', 'logo.mimes' => 'Gambar harus PNG, JPG, WEBP, atau ICO.', 'logo.max' => 'Ukuran gambar paling besar 2 MB.'],
            );
            AppSettings::put('logo_'.$which, $request->file('logo')->store('logos', 'public'));
            $message = $label.' diganti.';
        }
        if (is_string($old) && $old !== '') {
            Storage::disk('public')->delete($old);
        }

        return redirect()->to(route('settings.index').'#logo')->with('status', $message);
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
