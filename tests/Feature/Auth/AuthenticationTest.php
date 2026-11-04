<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk');
        $response->assertSee('Daftar');
        $response->assertSee('tabs-blug');
        $response->assertDontSee('Forgot your password?');
        $response->assertDontSee('Lupa kata sandi');
    }

    public function test_users_can_authenticate_and_redirect_to_pengajuan(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pengajuan.index', absolute: false));
    }

    public function test_admins_can_authenticate_and_redirect_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ]);
    }

    public function test_authenticated_user_cannot_visit_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('pengajuan.index', absolute: false));
    }

    public function test_authenticated_admin_cannot_visit_login_screen(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_after_logout_user_is_redirected_to_beranda_and_cannot_access_pengajuan(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $logoutResponse = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $logoutResponse->assertRedirect('/');

        $pengajuanResponse = $this->get('/pengajuan');
        $pengajuanResponse->assertRedirect('/login');
    }

    public function test_navbar_has_logout_button_with_post_form_for_authenticated_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('action="'.route('logout').'"', false);
        $response->assertSee('method="POST"', false);
        $response->assertSee('type="submit"', false);
        $response->assertSee('Keluar');
        $response->assertDontSee('href="'.route('logout').'"', false);
    }
}
