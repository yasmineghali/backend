<?php

namespace Tests\Feature\Bimrevo;

use App\Models\Bimrevo\NewsPost;
use App\Models\NewsPost as GeorevoNewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $attrs = []): NewsPost
    {
        static $n = 0;
        $n++;

        return NewsPost::create(array_merge([
            'slug' => "article-{$n}",
            'title' => "Article {$n}",
            'excerpt' => 'Resume',
            'content' => '<p>Contenu</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $attrs));
    }

    public function test_index_lists_only_published_posts_newest_first(): void
    {
        $old = $this->makePost(['title' => 'Old', 'published_at' => now()->subDays(5)]);
        $new = $this->makePost(['title' => 'New', 'published_at' => now()->subHour()]);
        $this->makePost(['title' => 'Draft', 'is_published' => false]);
        $this->makePost(['title' => 'Future', 'published_at' => now()->addDay()]);

        $response = $this->getJson('/api/bimrevo/news')->assertOk();

        $this->assertSame(['New', 'Old'], collect($response->json('data'))->pluck('title')->all());
        $response->assertJsonStructure(['data' => [['slug', 'title', 'excerpt', 'image_url', 'image_alt', 'author', 'published_at']]]);
        $response->assertJsonMissingPath('data.0.content');
    }

    public function test_show_returns_full_content_by_slug(): void
    {
        $post = $this->makePost(['slug' => 'coordination-bim', 'title' => 'Coordination BIM', 'author' => 'Équipe BIMREVO']);

        $this->getJson('/api/bimrevo/news/coordination-bim')
            ->assertOk()
            ->assertJsonPath('data.slug', 'coordination-bim')
            ->assertJsonPath('data.title', 'Coordination BIM')
            ->assertJsonPath('data.content', '<p>Contenu</p>')
            ->assertJsonPath('data.author', 'Équipe BIMREVO')
            ->assertJsonPath('data.image_alt', 'Coordination BIM');

        $this->assertNotNull($post->published_at);
    }

    public function test_show_hides_drafts_future_and_unknown_posts(): void
    {
        $this->makePost(['slug' => 'draft', 'is_published' => false]);
        $this->makePost(['slug' => 'future', 'published_at' => now()->addDay()]);

        $this->getJson('/api/bimrevo/news/draft')->assertNotFound();
        $this->getJson('/api/bimrevo/news/future')->assertNotFound();
        $this->getJson('/api/bimrevo/news/nope')->assertNotFound();
    }

    public function test_image_url_is_built_from_the_public_disk(): void
    {
        $this->makePost(['slug' => 'with-image', 'image' => 'bimrevo/news/cover.webp']);

        $this->getJson('/api/bimrevo/news/with-image')
            ->assertOk()
            ->assertJsonPath('data.image_url', fn ($url) => str_ends_with($url, '/storage/bimrevo/news/cover.webp'));
    }

    public function test_news_is_read_only_through_the_api(): void
    {
        $this->makePost(['slug' => 'a']);

        $this->postJson('/api/bimrevo/news', ['title' => 'x'])->assertStatus(405);
        $this->putJson('/api/bimrevo/news/a', ['title' => 'x'])->assertStatus(405);
        $this->deleteJson('/api/bimrevo/news/a')->assertStatus(405);
    }

    public function test_bimrevo_and_georevo_news_are_separate(): void
    {
        $this->makePost(['title' => 'Bimrevo only']);
        GeorevoNewsPost::create([
            'slug' => 'geo', 'title_fr' => 'Geo only', 'content_fr' => '<p>x</p>',
            'is_published' => true, 'published_at' => now()->subDay(),
        ]);

        $bim = collect($this->getJson('/api/bimrevo/news')->json('data'))->pluck('title')->all();
        $geo = collect($this->getJson('/api/news')->json('data'))->pluck('title')->all();

        $this->assertSame(['Bimrevo only'], $bim);
        $this->assertSame(['Geo only'], $geo);
    }
}
