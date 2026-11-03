<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_cannot_open_the_admin_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this
            ->actingAs($customer)
            ->get('/admin');

        $response->assertForbidden();
    }
}
