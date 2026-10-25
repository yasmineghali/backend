<?php

namespace Tests\Feature\Bimrevo;

use App\Models\Bimrevo\PageView;
use App\Models\PageView as GeorevoPageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class TrackApiTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/bimrevo/track';

    private const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0 Safari/537.36';

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('bimrevo-track');
    }

    private function track(array $data, array $headers = []): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders($headers + ['User-Agent' => self::BROWSER])->postJson(self::URL, $data);
    }

    public function test_a_page_view_is_stored_without_personal_data(): void
    {
        $this->track(['path' => '/blog/', 'referrer' => 'https://www.google.com/search?q=bim'])->assertNoContent();

        $view = PageView::firstOrFail();
        $this->assertSame('/blog/', $view->path);
        $this->assertSame('google.com', $view->referrer_host);
        $this->assertSame('desktop', $view->device);
        $this->assertSame(32, strlen($view->visitor_hash));
        $this->assertStringNotContainsString('127.0.0.1', json_encode($view->getAttributes()));
    }

    public function test_internal_referrers_are_dropped(): void
    {
        $this->track(['path' => '/', 'referrer' => 'www.bimrevo.ch'])->assertNoContent();

        $this->assertNull(PageView::firstOrFail()->referrer_host);
    }

    public function test_query_strings_are_removed_except_the_blog_slug(): void
    {
        $this->track(['path' => '/contact.html?email=a@b.ch&utm=x'])->assertNoContent();
        $this->track(['path' => '/blog/article.html?slug=coordination-bim&email=a@b.ch'])->assertNoContent();

        $this->assertSame(
            ['/blog/article.html?slug=coordination-bim', '/contact.html'],
            PageView::orderBy('path')->pluck('path')->all(),
        );
    }

    public function test_invalid_paths_are_ignored(): void
    {
        $this->track(['path' => 'https://evil.example/x'])->assertNoContent();
        $this->track(['path' => 'no-slash'])->assertNoContent();
        $this->track(['path' => '//double'])->assertNoContent();

        $this->assertSame(0, PageView::count());
    }

    public function test_bots_and_do_not_track_are_ignored(): void
    {
        $this->track(['path' => '/'], ['User-Agent' => 'Googlebot/2.1'])->assertNoContent();
        $this->track(['path' => '/'], ['DNT' => '1'])->assertNoContent();
        $this->track(['path' => '/'], ['Sec-GPC' => '1'])->assertNoContent();

        $this->assertSame(0, PageView::count());
    }

    public function test_tracking_can_be_disabled(): void
    {
        config(['bimrevo.analytics.enabled' => false]);

        $this->track(['path' => '/'])->assertNoContent();

        $this->assertSame(0, PageView::count());
    }

    public function test_path_is_required(): void
    {
        $this->track([])->assertStatus(422)->assertJsonValidationErrors(['path']);
    }

    public function test_same_visitor_gets_the_same_hash_within_a_day(): void
    {
        $this->track(['path' => '/a'])->assertNoContent();
        $this->track(['path' => '/b'])->assertNoContent();

        $this->assertSame(1, PageView::distinct()->count('visitor_hash'));
    }

    public function test_bimrevo_and_georevo_traffic_are_separate(): void
    {
        $this->track(['path' => '/'])->assertNoContent();

        $this->assertSame(1, PageView::count());
        $this->assertSame(0, GeorevoPageView::count());
    }

    public function test_tracking_is_rate_limited(): void
    {
        for ($i = 0; $i < 120; $i++) {
            $this->track(['path' => '/'])->assertNoContent();
        }

        $this->track(['path' => '/'])->assertStatus(429);
    }

    public function test_old_views_are_pruned(): void
    {
        PageView::create(['visitor_hash' => str_repeat('a', 32), 'path' => '/old', 'device' => 'desktop', 'viewed_at' => now()->subDays(400)]);
        PageView::create(['visitor_hash' => str_repeat('b', 32), 'path' => '/new', 'device' => 'desktop', 'viewed_at' => now()->subDays(5)]);

        $this->artisan('bimrevo:analytics-prune')->assertSuccessful();

        $this->assertSame(['/new'], PageView::pluck('path')->all());
    }
}
