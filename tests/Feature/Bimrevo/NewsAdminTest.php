<?php

namespace Tests\Feature\Bimrevo;

use App\Filament\Resources\Bimrevo\NewsPosts\NewsPostResource;
use App\Filament\Resources\Bimrevo\NewsPosts\Pages\CreateNewsPost;
use App\Filament\Resources\Bimrevo\NewsPosts\Pages\EditNewsPost;
use App\Filament\Resources\Bimrevo\NewsPosts\Pages\ListNewsPosts;
use App\Filament\Resources\NewsPosts\NewsPostResource as GeorevoNewsPostResource;
use App\Models\Bimrevo\NewsPost;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewsAdminTest extends TestCase
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

    private function makePost(array $attrs = []): NewsPost
    {
        return NewsPost::create(array_merge([
            'slug' => 'article',
            'title' => 'Article',
            'content' => '<p>Contenu</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ], $attrs));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/bimrevo/news-posts')->assertRedirect('/admin/login');
        $this->get('/admin/bimrevo/news-posts/create')->assertRedirect('/admin/login');
    }

    public function test_list_page_shows_posts(): void
    {
        $this->signIn();
        $a = $this->makePost(['slug' => 'a', 'title' => 'Alpha']);
        $b = $this->makePost(['slug' => 'b', 'title' => 'Beta']);

        $this->get(NewsPostResource::getUrl('index'))->assertOk();

        Livewire::test(ListNewsPosts::class)
            ->assertCanSeeTableRecords([$a, $b])
            ->assertSee('Alpha');
    }

    public function test_create_a_post(): void
    {
        $this->signIn();

        Livewire::test(CreateNewsPost::class)
            ->fillForm([
                'title' => 'Détection de collisions',
                'slug' => 'detection-collisions',
                'content' => '<p>Texte</p>',
                'author' => 'Équipe BIMREVO',
                'published_at' => now()->subHour()->toDateTimeString(),
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('bimrevo_news_posts', ['slug' => 'detection-collisions', 'title' => 'Détection de collisions']);
    }

    public function test_title_and_content_are_required_and_slug_is_unique(): void
    {
        $this->signIn();
        $this->makePost(['slug' => 'taken']);

        Livewire::test(CreateNewsPost::class)
            ->fillForm(['title' => null, 'content' => null, 'slug' => 'taken'])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'content', 'slug' => 'unique']);
    }

    public function test_edit_and_delete_a_post(): void
    {
        $this->signIn();
        $post = $this->makePost();

        Livewire::test(EditNewsPost::class, ['record' => $post->getKey()])
            ->fillForm(['title' => 'Nouveau titre', 'is_published' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Nouveau titre', $post->fresh()->title);
        $this->assertFalse($post->fresh()->is_published);

        Livewire::test(EditNewsPost::class, ['record' => $post->getKey()])->callAction('delete');
        $this->assertModelMissing($post);
    }

    public function test_urls_do_not_collide_with_georevo(): void
    {
        $this->signIn();

        $this->assertSame(url('/admin/bimrevo/news-posts'), NewsPostResource::getUrl('index'));
        $this->assertSame(url('/admin/news-posts'), GeorevoNewsPostResource::getUrl('index'));
    }
}
