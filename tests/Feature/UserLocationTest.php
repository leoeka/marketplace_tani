<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLocationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $over = []): array
    {
        return array_merge(['latitude' => -8.65, 'longitude' => 115.21, 'accuracy' => 25.4], $over);
    }

    public function test_guest_cannot_store_location(): void
    {
        $this->postJson(route('location.store'), $this->payload())->assertUnauthorized();
        $this->postJson(route('location.consent'), ['choice' => 'granted'])->assertUnauthorized();
    }

    public function test_location_is_rejected_without_consent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('location.store'), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('user_locations', 0);
    }

    public function test_denied_consent_stores_nothing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('location.consent'), ['choice' => 'denied'])->assertOk();
        $this->actingAs($user)->postJson(route('location.store'), $this->payload())->assertForbidden();

        $this->assertSame('denied', $user->fresh()->location_consent);
        $this->assertDatabaseCount('user_locations', 0);
    }

    public function test_granted_consent_stores_location_once_per_hour(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->postJson(route('location.consent'), ['choice' => 'granted'])->assertOk();

        $this->actingAs($user)->postJson(route('location.store'), $this->payload())->assertOk();
        $this->actingAs($user)->postJson(route('location.store'), $this->payload())->assertOk();

        $this->assertDatabaseCount('user_locations', 1);
        $this->assertDatabaseHas('user_locations', ['user_id' => $user->id, 'accuracy_m' => 25]);
    }

    public function test_invalid_coordinates_are_rejected(): void
    {
        $user = User::factory()->create(['location_consent' => 'granted']);

        $this->actingAs($user)->postJson(route('location.store'), $this->payload(['latitude' => 120]))
            ->assertUnprocessable();
        $this->actingAs($user)->postJson(route('location.store'), $this->payload(['longitude' => 'abc']))
            ->assertUnprocessable();
        $this->assertDatabaseCount('user_locations', 0);
    }

    public function test_user_can_withdraw_consent_and_delete_data(): void
    {
        $user = User::factory()->create(['location_consent' => 'granted']);
        $this->actingAs($user)->postJson(route('location.store'), $this->payload())->assertOk();

        $this->actingAs($user)->delete(route('location.destroy'))->assertRedirect();

        $this->assertDatabaseCount('user_locations', 0);
        $this->assertSame('denied', $user->fresh()->location_consent);
    }

    public function test_admin_sees_locations_and_others_are_forbidden(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = User::factory()->create(['role' => 'farmer', 'location_consent' => 'granted']);
        \App\Models\UserLocation::create([
            'user_id' => $farmer->id,
            'latitude' => -8.65,
            'longitude' => 115.21,
            'captured_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('admin.locations.index'))
            ->assertOk()->assertSee('-8.65000, 115.21000');

        $this->actingAs($farmer)->get(route('admin.locations.index'))->assertForbidden();
    }
}