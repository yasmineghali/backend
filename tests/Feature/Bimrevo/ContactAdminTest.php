<?php

namespace Tests\Feature\Bimrevo;

use App\Filament\Resources\Bimrevo\Contacts\ContactResource;
use App\Filament\Resources\Bimrevo\Contacts\Pages\ListContacts;
use App\Filament\Resources\Bimrevo\Contacts\Pages\ViewContact;
use App\Filament\Resources\Contacts\ContactResource as GeorevoContactResource;
use App\Filament\Resources\NewsPosts\NewsPostResource;
use App\Models\Bimrevo\Contact;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The User model has no canAccessPanel(); Filament only allows that in the local environment.
        config(['app.env' => 'local']);
    }

    private function signIn(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Filament::setCurrentPanel('admin');

        return $user;
    }

    private function contact(array $attrs = []): Contact
    {
        return Contact::create(array_merge([
            'name' => 'Jean Dupont',
            'email' => 'jean@bureau.ch',
            'message' => 'Hello',
        ], $attrs));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/bimrevo/contacts')->assertRedirect('/admin/login');
        $this->get('/admin/bimrevo/contacts/1')->assertRedirect('/admin/login');
    }

    public function test_list_shows_submissions(): void
    {
        $this->signIn();
        $a = $this->contact(['name' => 'Alice']);
        $b = $this->contact(['name' => 'Bob']);

        Livewire::test(ListContacts::class)
            ->assertCanSeeTableRecords([$a, $b])
            ->assertSee('Alice');
    }

    public function test_unread_badge_counts_only_unread(): void
    {
        $this->signIn();
        $this->contact();
        $this->contact();
        $this->contact(['read_at' => now()]);

        $this->assertSame('2', ContactResource::getNavigationBadge());
    }

    public function test_badge_is_hidden_when_nothing_is_unread(): void
    {
        $this->signIn();
        $this->contact(['read_at' => now()]);

        $this->assertNull(ContactResource::getNavigationBadge());
    }

    public function test_opening_a_message_marks_it_read(): void
    {
        $this->signIn();
        $contact = $this->contact();

        $this->get(ContactResource::getUrl('view', ['record' => $contact]))->assertOk()->assertSee('Hello');

        $this->assertNotNull($contact->fresh()->read_at);
    }

    public function test_mark_unread_from_view_page(): void
    {
        $this->signIn();
        $contact = $this->contact(['read_at' => now()]);

        Livewire::test(ViewContact::class, ['record' => $contact->getKey()])
            ->callAction('markUnread');

        $this->assertNull($contact->fresh()->read_at);
    }

    public function test_row_and_bulk_read_unread_actions(): void
    {
        $this->signIn();
        $a = $this->contact();
        $b = $this->contact();

        $list = Livewire::test(ListContacts::class)
            ->callTableAction('toggleRead', $a);
        $this->assertNotNull($a->fresh()->read_at);

        $list->callTableAction('toggleRead', $a);
        $this->assertNull($a->fresh()->read_at);

        $list->callTableBulkAction('markRead', [$a, $b]);
        $this->assertNotNull($a->fresh()->read_at);
        $this->assertNotNull($b->fresh()->read_at);

        $list->callTableBulkAction('markUnread', [$a, $b]);
        $this->assertSame(2, Contact::unread()->count());
    }

    public function test_unread_filter(): void
    {
        $this->signIn();
        $unread = $this->contact(['name' => 'Unread one']);
        $read = $this->contact(['name' => 'Read one', 'read_at' => now()]);

        Livewire::test(ListContacts::class)
            ->filterTable('read', false)
            ->assertCanSeeTableRecords([$unread])
            ->assertCanNotSeeTableRecords([$read]);
    }

    public function test_sidebar_groups_and_unchanged_georevo_urls(): void
    {
        $this->signIn();

        $groups = collect(Filament::getNavigation())->keyBy(fn ($g) => $g->getLabel());

        $labels = fn (string $group) => $groups[$group]->getItems()->map->getLabel()->values()->all();
        $this->assertSame(['Contacts', 'News'], collect($labels('GeoRevo'))->sort()->values()->all());
        $this->assertSame(['Contacts'], $labels('BIMRevo'));

        $this->assertSame(url('/admin/contacts'), GeorevoContactResource::getUrl('index'));
        $this->assertSame(url('/admin/news-posts'), NewsPostResource::getUrl('index'));
        $this->assertSame(url('/admin/bimrevo/contacts'), ContactResource::getUrl('index'));
    }
}
