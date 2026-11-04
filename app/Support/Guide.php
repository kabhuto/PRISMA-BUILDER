<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * The in-app guide. Its text lives in config/panduan.php: one section per page or feature,
 * each naming the routes it explains so every page can link straight to its part.
 */
class Guide
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function sections(): array
    {
        return array_values(config('panduan.halaman', []));
    }

    /**
     * The id of the section that explains the page with this route name, if any does.
     */
    public static function sectionFor(?string $route): ?string
    {
        if ($route === null) {
            return null;
        }

        foreach (self::sections() as $section) {
            if (Str::is($section['rute'] ?? [], $route)) {
                return $section['id'];
            }
        }

        return null;
    }

    /**
     * Where the help button of the page being viewed points: its own section, or the start of the guide.
     */
    public static function linkFor(?string $route): string
    {
        return route('panduan').'#'.(self::sectionFor($route) ?? 'alur');
    }
}
