<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_login_and_logout(): void
    {
        $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('profiles', ['user_id' => User::whereEmail('new@example.com')->value('id')]);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/login', ['email' => 'new@example.com', 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_admin_is_redirected_to_dashboard_after_login(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'password', 'role' => 'admin']);

        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_regular_user_always_goes_home_even_after_requesting_an_admin_page(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com', 'password' => 'password', 'role' => 'user']);

        $this->get('/admin/dashboard')->assertRedirect('/login');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('home'));
    }
}
