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

    public function test_farmer_visiting_login_is_redirected_to_the_farmer_dashboard(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->get('/login')
            ->assertRedirect(route('farmer.dashboard'));
    }

    public function test_admin_visiting_login_is_redirected_to_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_customer_visiting_login_is_redirected_to_the_home_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get('/login')
            ->assertRedirect(route('home'));
    }

    public function test_farmer_visiting_dashboard_is_redirected_to_the_farmer_dashboard(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->get('/dashboard')
            ->assertRedirect(route('farmer.dashboard'));
    }

    public function test_admin_visiting_dashboard_is_redirected_to_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_customer_visiting_dashboard_is_redirected_to_the_home_page(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertRedirect(route('home'));
    }
}
