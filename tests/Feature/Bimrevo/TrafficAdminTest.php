<?php

namespace Tests\Feature\Bimrevo;

use App\Filament\Pages\Bimrevo\Traffic as TrafficPage;
use App\Filament\Widgets\Bimrevo\TopPages;
use App\Filament\Widgets\Bimrevo\TopReferrers;
use App\Filament\Widgets\Bimrevo\TrafficStats;
use App\Models\Bimrevo\PageView;
use App\Models\PageView as GeorevoPageView;
use App\Models\User;
use App\Support\Bimrevo\Traffic;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrafficAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The User model has no canAccessPanel(); Filament only allows that in the local environment.
        config(['app.env' => 'local']);
    }

    private function signIn(): void
    {
        $this->actingAs(User::factory()->create());
        Filament::setCurrentPanel('admin');
    }

    private function addView(string $path, string $visitor, $at = null, ?string $referrer = null, string $device = 'desktop'): PageView
    {
        return PageView::create([
            'visitor_hash' => str_pad($visitor, 32, '0'),
            'path' => $path,
            'referrer_host' => $referrer,
            'device' => $device,
            'viewed_at' => $at ?? now(),
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/bimrevo/traffic')->assertRedirect('/admin/login');
    }

    public function test_traffic_page_loads_and_is_in_the_bimrevo_menu(): void
    {
        $this->signIn();

        $this->get(TrafficPage::getUrl())->assertOk()->assertSee('BIMRevo traffic');

        $group = collect(Filament::getNavigation())->first(fn ($g) => $g->getLabel() === 'BIMRevo');
        $this->assertContains('Traffic', $group->getItems()->map->getLabel()->all());
        $this->assertSame(url('/admin/bimrevo/traffic'), TrafficPage::getUrl());
    }

    public function test_numbers_count_views_and_daily_visitors(): void
    {
        $this->addView('/', 'a');
        $this->addView('/blog/', 'a');
        $this->addView('/', 'b');
        $this->addView('/', 'a', now()->subDays(3));
        $this->addView('/', 'a', now()->subDays(40));

        $this->assertSame(3, Traffic::views(now()->startOfDay()));
        $this->assertSame(2, Traffic::visitors(now()->startOfDay()));
        $this->assertSame(4, Traffic::views(now()->subDays(6)->startOfDay()));
        $this->assertSame(3, Traffic::visitors(now()->subDays(6)->startOfDay()));
        $this->assertSame(5, Traffic::views(now()->subDays(60)->startOfDay()));
        $this->assertSame(['desktop' => 5], Traffic::breakdown('device', 90));
    }

    public function test_daily_series_has_one_entry_per_day(): void
    {
        $this->addView('/', 'a');

        $series = Traffic::daily(7);

        $this->assertCount(7, $series['labels']);
        $this->assertSame(1, $series['views'][6]);
        $this->assertSame(array_fill(0, 6, 0), array_slice($series['views'], 0, 6));
    }

    public function test_widgets_render_on_the_page(): void
    {
        $this->signIn();
        $this->addView('/blog/', 'a', null, 'google.com');

        Livewire::test(TrafficStats::class)->assertSee('Views today');
        Livewire::test(TopPages::class)->assertSee('/blog/');
        Livewire::test(TopReferrers::class)->assertSee('google.com');
    }

    public function test_georevo_dashboard_does_not_show_bimrevo_widgets(): void
    {
        $this->signIn();
        GeorevoPageView::create(['visitor_hash' => str_repeat('c', 32), 'path' => '/geo', 'locale' => 'fr', 'device' => 'desktop', 'viewed_at' => now()]);
        $this->addView('/bimrevo-only', 'a');

        $widgets = collect(Filament::getWidgets());

        $this->assertNotContains(TrafficStats::class, $widgets);
        $this->assertNotContains(TopPages::class, $widgets);
        $this->assertContains(\App\Filament\Widgets\TrafficStats::class, $widgets);

        Livewire::test(\App\Filament\Widgets\TopPages::class)->assertSee('/geo')->assertDontSee('/bimrevo-only');
    }
}
