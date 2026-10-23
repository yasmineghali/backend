<?php

namespace Tests\Feature\Bimrevo;

use App\Models\Bimrevo\Contact as BimrevoContact;
use App\Models\Contact as GeorevoContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/api/bimrevo/contacts';

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('bimrevo-contact');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jean Dupont',
            'email' => 'jean@bureau.ch',
            'company' => 'Bureau XYZ',
            'phone' => '+41 79 000 00 00',
            'plan' => 'Team',
            'message' => 'Bonjour',
        ], $overrides);
    }

    public function test_valid_submission_is_stored(): void
    {
        $this->postJson(self::URL, $this->payload())
            ->assertCreated()
            ->assertExactJson(['message' => 'Received.']);

        $this->assertDatabaseHas('bimrevo_contacts', [
            'email' => 'jean@bureau.ch',
            'plan' => 'Team',
            'status' => 'new',
            'handled_at' => null,
        ]);
    }

    public function test_message_is_required(): void
    {
        $this->postJson(self::URL, $this->payload(['message' => '']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        $this->postJson(self::URL, array_diff_key($this->payload(), ['message' => 1]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        $this->assertSame(0, BimrevoContact::count());
    }

    public function test_plan_is_optional(): void
    {
        $this->postJson(self::URL, $this->payload(['plan' => null]))->assertCreated();
        $this->assertSame(1, BimrevoContact::count());
    }

    public function test_invalid_submissions_are_rejected_with_json_errors(): void
    {
        $this->postJson(self::URL, [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);

        $this->postJson(self::URL, $this->payload(['email' => 'not-an-email']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->postJson(self::URL, $this->payload(['plan' => 'Gold']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['plan']);

        $this->postJson(self::URL, $this->payload(['message' => str_repeat('a', 5001)]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        $this->assertSame(0, BimrevoContact::count());
    }

    public function test_honeypot_pretends_success_but_stores_nothing(): void
    {
        $this->postJson(self::URL, $this->payload(['botcheck' => '1']))->assertCreated();
        $this->assertSame(0, BimrevoContact::count());
    }

    public function test_submissions_stay_separate_from_georevo(): void
    {
        $this->postJson(self::URL, $this->payload())->assertCreated();

        $this->assertSame(1, BimrevoContact::count());
        $this->assertSame(0, GeorevoContact::count());
    }

    public function test_endpoint_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson(self::URL, $this->payload())->assertCreated();
        }

        $this->postJson(self::URL, $this->payload())->assertStatus(429);
    }

    public function test_cross_origin_preflight_is_allowed(): void
    {
        $this->call('OPTIONS', self::URL, server: [
            'HTTP_ORIGIN' => 'https://bimrevo.ch',
            'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'POST',
        ])->assertSuccessful()->assertHeader('Access-Control-Allow-Origin');
    }

    public function test_no_public_admin_endpoints_exist(): void
    {
        $this->getJson(self::URL)->assertStatus(405);
        $this->getJson(self::URL.'/1')->assertNotFound();
        $this->patchJson(self::URL.'/1', ['read_at' => now()])->assertNotFound();
    }
}
