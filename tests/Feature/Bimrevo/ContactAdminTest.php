<?php

namespace Tests\Feature\Bimrevo;

use App\Filament\Resources\Bimrevo\Contacts\ContactResource;
use App\Filament\Resources\Bimrevo\Contacts\Pages\EditContact;
use App\Filament\Resources\Bimrevo\Contacts\Pages\ListContacts;
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
            'status' => Contact::STATUS_NEW,
        ], $attrs));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/bimrevo/contacts')->assertRedirect('/admin/login');
        $this->get('/admin/bimrevo/contacts/1')->assertRedirect('/admin/login');
        $this->get('/admin/bimrevo/contacts/1/edit')->assertRedirect('/admin/login');
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

    public function test_new_badge_counts_only_new_contacts(): void
    {
        $this->signIn();
        $this->contact();
        $this->contact();
        $this->contact(['status' => Contact::STATUS_IN_PROGRESS]);
        $this->contact(['status' => Contact::STATUS_HANDLED]);

        $this->assertSame('2', ContactResource::getNavigationBadge());
    }

    public function test_badge_is_hidden_when_nothing_is_new(): void
    {
        $this->signIn();
        $this->contact(['status' => Contact::STATUS_HANDLED]);

        $this->assertNull(ContactResource::getNavigationBadge());
    }

    public function test_view_page_shows_message_and_does_not_change_status(): void
    {
        $this->signIn();
        $contact = $this->contact();

        $this->get(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertOk()
            ->assertSee('Hello');

        $this->assertSame(Contact::STATUS_NEW, $contact->fresh()->status);
    }

    public function test_mark_handled_row_and_bulk_actions(): void
    {
        $this->signIn();
        $a = $this->contact();
        $b = $this->contact();
        $c = $this->contact();

        $list = Livewire::test(ListContacts::class)
            ->callTableAction('markHandled', $a);
        $this->assertSame(Contact::STATUS_HANDLED, $a->fresh()->status);
        $this->assertNotNull($a->fresh()->handled_at);

        $list->callTableBulkAction('markHandled', [$b, $c]);
        $this->assertSame(Contact::STATUS_HANDLED, $b->fresh()->status);
        $this->assertSame(Contact::STATUS_HANDLED, $c->fresh()->status);
    }

    public function test_edit_status_and_internal_notes(): void
    {
        $this->signIn();
        $contact = $this->contact();

        Livewire::test(EditContact::class, ['record' => $contact->getKey()])
            ->fillForm(['status' => Contact::STATUS_IN_PROGRESS, 'admin_notes' => 'Called back on Monday'])
            ->call('save')
            ->assertHasNoFormErrors();

        $contact->refresh();
        $this->assertSame(Contact::STATUS_IN_PROGRESS, $contact->status);
        $this->assertSame('Called back on Monday', $contact->admin_notes);
    }

    public function test_delete_from_edit_page_and_bulk(): void
    {
        $this->signIn();
        $a = $this->contact();
        $b = $this->contact();
        $c = $this->contact();

        Livewire::test(EditContact::class, ['record' => $a->getKey()])->callAction('delete');
        $this->assertModelMissing($a);

        Livewire::test(ListContacts::class)->callTableBulkAction('delete', [$b, $c]);
        $this->assertSame(0, Contact::count());
    }

    public function test_status_filter(): void
    {
        $this->signIn();
        $new = $this->contact(['name' => 'New one']);
        $handled = $this->contact(['name' => 'Handled one', 'status' => Contact::STATUS_HANDLED]);

        Livewire::test(ListContacts::class)
            ->filterTable('status', Contact::STATUS_NEW)
            ->assertCanSeeTableRecords([$new])
            ->assertCanNotSeeTableRecords([$handled]);
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
