<?php

namespace App\Support;

use App\Models\AppSetting;
use App\Models\Survey;
use Illuminate\Support\Str;

/**
 * What a superadmin sets for the whole app: the order of the menu and which menus, tools, and
 * question types an admin (pegawai) may use. A superadmin always sees everything.
 */
class AppSettings
{
    /**
     * The menu, in its default order: route, the routes it covers, label, short label for the
     * phone tab bar, icon, and whether it can be switched off.
     *
     * @var array<string, array{route: string, routes: list<string>, label: string, short: ?string, icon: string, fixed: bool}>
     */
    public const MENU = [
        'beranda' => ['route' => 'surveys.index', 'routes' => ['surveys.*'], 'label' => 'Beranda', 'short' => null, 'icon' => 'M3 11l9-7 9 7M5 10v10h5v-6h4v6h5V10', 'fixed' => true],
        'referensi' => ['route' => 'references.index', 'routes' => ['references.*'], 'label' => 'Data referensi', 'short' => 'Referensi', 'icon' => 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3Zm0 0v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3', 'fixed' => false],
        'riwayat' => ['route' => 'history.index', 'routes' => ['history.*'], 'label' => 'Riwayat', 'short' => null, 'icon' => 'M3 12a9 9 0 1 0 3-6.7L3 8M3 3v5h5M12 7v5l3 2', 'fixed' => false],
        'panduan' => ['route' => 'panduan', 'routes' => ['panduan'], 'label' => 'Panduan', 'short' => null, 'icon' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01', 'fixed' => false],
        'dashboard' => ['route' => 'dashboard', 'routes' => ['dashboard', 'agenda.*'], 'label' => 'Dashboard', 'short' => null, 'icon' => 'M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z', 'fixed' => false],
    ];

    /**
     * Tools an admin (pegawai) may be kept from, with the pages each one covers.
     *
     * @var array<string, array{label: string, description: string, routes: list<string>}>
     */
    public const TOOLS = [
        'validasi' => ['label' => 'Validasi data', 'description' => 'Memeriksa file Excel/CSV terhadap aturan kuesioner.', 'routes' => ['validation.*']],
        'impor' => ['label' => 'Impor JSON / FormGear', 'description' => 'Membuat kuesioner dari file ekspor.', 'routes' => ['surveys.import']],
        'ekspor' => ['label' => 'Ekspor JSON / FormGear', 'description' => 'Mengunduh struktur dan respons kuesioner.', 'routes' => ['surveys.export-json', 'surveys.export-formgear']],
        'penugasan' => ['label' => 'Penugasan', 'description' => 'Tautan per unit/responden dengan prefill dan batas waktu.', 'routes' => ['assignments.*']],
        'laporan' => ['label' => 'Tenggat & laporan progres', 'description' => 'Jadwal pendataan dan laporan kendala dari petugas.', 'routes' => ['progress.*']],
        'google-drive' => ['label' => 'Google Drive', 'description' => 'Mengirim respons ke Google Drive.', 'routes' => ['google-drive.*']],
        'cetak' => ['label' => 'Cetak', 'description' => 'Mencetak kuesioner, listing, dan respons.', 'routes' => ['surveys.print', 'surveys.responses.listing', 'surveys.responses.print']],
    ];

    /**
     * Pictures a superadmin may swap: the mark in the top bar, the PRISMA mark of the home page, and
     * the icon in the browser tab. Each falls back to the built-in drawing when none is uploaded.
     *
     * @var array<string, array{label: string, description: string}>
     */
    public const LOGOS = [
        'navbar' => ['label' => 'Logo navbar', 'description' => 'Pengganti tulisan "bps" di kiri atas setiap halaman (juga halaman masuk dan formulir responden).'],
        'prisma' => ['label' => 'Logo PRISMA', 'description' => 'Pengganti logo "P" di hero Beranda dan di samping judul PRISMA.'],
        'favicon' => ['label' => 'Ikon tab (judul)', 'description' => 'Ikon kecil di tab browser, di samping judul halaman. Paling bagus persegi, misalnya 64×64 piksel.'],
    ];

    /**
     * The address of an uploaded logo, or null for the built-in one.
     */
    public static function logo(string $which): ?string
    {
        $path = self::get('logo_'.$which);

        return is_string($path) && $path !== '' ? asset('storage/'.$path) : null;
    }

    /**
     * Settings read during this request, so the menu and the checks ask the database once.
     */
    private static function cache(): \ArrayObject
    {
        if (! app()->bound('app-settings.cache')) {
            app()->instance('app-settings.cache', new \ArrayObject);
        }

        return app('app-settings.cache');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $cache = self::cache();
        if (! $cache->offsetExists($key)) {
            $cache[$key] = AppSetting::query()->find($key)?->value;
        }

        return $cache[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        if ($value === null) {
            AppSetting::query()->whereKey($key)->delete();
            self::cache()[$key] = null;

            return;
        }
        AppSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        self::cache()[$key] = $value;
    }

    /**
     * Menu keys in the order the superadmin set; new menus join at the end.
     *
     * @return list<string>
     */
    public static function menuOrder(): array
    {
        $saved = array_values(array_intersect((array) self::get('menu_order', []), array_keys(self::MENU)));

        return array_values(array_unique([...$saved, ...array_keys(self::MENU)]));
    }

    /**
     * @return list<string>
     */
    public static function hiddenMenus(): array
    {
        return array_values(array_filter((array) self::get('hidden_menus', []), fn ($key): bool => isset(self::MENU[$key]) && ! self::MENU[$key]['fixed']));
    }

    /**
     * @return list<string>
     */
    public static function hiddenTools(): array
    {
        return array_values(array_intersect((array) self::get('hidden_tools', []), array_keys(self::TOOLS)));
    }

    /**
     * @return list<string>
     */
    public static function hiddenQuestionTypes(): array
    {
        return array_values(array_intersect((array) self::get('hidden_question_types', []), array_keys(Survey::QUESTION_TYPES)));
    }

    /**
     * The menu the person signed in sees, in order. Each entry says whether it is switched off for
     * admins (a superadmin still sees it, marked).
     *
     * @return list<array{key: string, route: string, routes: list<string>, label: string, short: ?string, icon: string, fixed: bool, off: bool}>
     */
    public static function menu(): array
    {
        $hidden = self::hiddenMenus();
        $superadmin = Access::isSuperadmin();
        $items = [];
        foreach (self::menuOrder() as $key) {
            $off = in_array($key, $hidden, true);
            if ($off && ! $superadmin) {
                continue;
            }
            $items[] = ['key' => $key, ...self::MENU[$key], 'off' => $off];
        }

        return $items;
    }

    public static function menuOn(string $key): bool
    {
        return Access::isSuperadmin() || ! in_array($key, self::hiddenMenus(), true);
    }

    public static function toolOn(string $tool): bool
    {
        return Access::isSuperadmin() || ! in_array($tool, self::hiddenTools(), true);
    }

    /**
     * Question types the builder offers to the person signed in.
     *
     * @return array<string, string>
     */
    public static function questionTypes(): array
    {
        return Access::isSuperadmin() ? Survey::QUESTION_TYPES : array_diff_key(Survey::QUESTION_TYPES, array_flip(self::hiddenQuestionTypes()));
    }

    /**
     * The label of the menu or tool that keeps admins from a page, or null when it is open.
     */
    public static function switchedOffFor(string $route): ?string
    {
        foreach (self::hiddenMenus() as $key) {
            if (Str::is(self::MENU[$key]['routes'], $route)) {
                return self::MENU[$key]['label'];
            }
        }
        foreach (self::hiddenTools() as $key) {
            if (Str::is(self::TOOLS[$key]['routes'], $route)) {
                return self::TOOLS[$key]['label'];
            }
        }

        return null;
    }
}
