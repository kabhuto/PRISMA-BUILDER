<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Guide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuideTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pages that are files rather than something a person reads; Laravel's own storage routes are left out as well.
     */
    private const NOT_PAGES = ['accent.css'];

    public function test_the_guide_opens_with_the_prisma_chain_and_every_section(): void
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get(route('panduan'))->assertOk()->assertSee('Alur PRISMA')->assertSee('PRISMA Builder');
        foreach (Guide::sections() as $section) {
            $response->assertSee('id="'.$section['id'].'"', false)->assertSee($section['judul']);
        }
    }

    public function test_every_page_has_its_part_of_the_guide(): void
    {
        $names = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route): bool => in_array('GET', $route->methods(), true) && ! str_starts_with($route->uri(), 'api'))
            ->map(fn ($route): ?string => $route->getName())
            ->filter()
            ->reject(fn (string $name): bool => str_starts_with($name, 'storage.') || in_array($name, self::NOT_PAGES, true));

        foreach ($names as $name) {
            $this->assertNotNull(Guide::sectionFor($name), "Halaman {$name} belum dijelaskan di config/panduan.php.");
        }

        // A pattern that matches no route has outlived the page it explained.
        $all = collect(Route::getRoutes()->getRoutes())->map(fn ($route): ?string => $route->getName())->filter();
        foreach (Guide::sections() as $section) {
            foreach ($section['rute'] ?? [] as $pattern) {
                $this->assertTrue($all->contains(fn (string $name): bool => Str::is($pattern, $name)), "Pola rute {$pattern} di bagian {$section['id']} tidak cocok dengan halaman mana pun.");
            }
        }
    }

    public function test_the_help_button_opens_the_part_about_the_page_being_viewed(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get(route('references.index'))->assertOk()->assertSee(route('panduan').'#referensi', false);
        $this->get(route('panduan'))->assertSee(route('panduan').'#panduan-ini', false);

        // The form builder has its own top bar.
        $this->get(route('surveys.create'))->assertOk()->assertSee(route('panduan').'#menyusun', false);
    }
}
