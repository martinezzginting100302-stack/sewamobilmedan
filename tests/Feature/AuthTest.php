<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_page()
    {
        $this->get('/')
            ->assertRedirect('/login');

        $this->get('/cars')
            ->assertRedirect('/login');
    }

    public function test_login_page_can_be_rendered()
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('SewaMobilMedan');
    }

    public function test_register_page_can_be_rendered()
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Daftar');
    }

    public function test_user_can_register()
    {
        $this->post('/register', [
            'name' => 'Andi Prasetyo',
            'email' => 'andi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'andi@example.com',
            'name' => 'Andi Prasetyo',
        ]);

        $this->assertAuthenticated();
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])
            ->assertRedirect('/')
            ->assertSessionHas('success');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_logged_in_user_sees_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee($user->name);
    }
}