<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'customer',
            'customer_type' => 'individual',
        ]);
    }

    public function test_new_users_can_register_as_farmers(): void
    {
        $response = $this->post('/register', [
            'name' => 'Farmer User',
            'email' => 'farmer@example.com',
            'role' => 'farmer',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('farmer.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'farmer@example.com',
            'role' => 'farmer',
        ]);
    }

    public function test_registration_rejects_admin_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }
}
