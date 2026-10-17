<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function payload(string $role): array
    {
        return [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => $role,
        ];
    }

    public function test_register_as_customer_goes_to_customer_dashboard(): void
    {
        $this->post('/register', $this->payload('customer'))
            ->assertRedirect(route('customer.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'budi@example.com', 'role' => 'customer']);
    }

    public function test_cannot_register_as_admin(): void
    {
        $this->post('/register', $this->payload('admin'))->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'budi@example.com']);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->inactive()->create(['email' => 'off@example.com']);

        $this->post('/login', ['email' => 'off@example.com', 'password' => 'password']);

        $this->assertGuest();
    }

    public function test_customer_cannot_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_merchant_can_open_merchant_dashboard(): void
{
    $this->actingAs(User::factory()->merchant()->create())
        ->get('/merchant/dashboard')
        ->assertOk();
}
}