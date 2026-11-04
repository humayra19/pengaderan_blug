<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat akun');
        $response->assertSee('Minimal 8 karakter');
        $response->assertSee('Masuk');
        $response->assertSee('Daftar');
    }

    public function test_new_users_can_register_and_are_redirected_to_pengajuan(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Calon Anggota',
            'email' => 'calon@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pengajuan.index', absolute: false));

        $user = User::where('email', 'calon@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);

        Notification::assertNothingSent();
    }

    public function test_role_cannot_be_manipulated_during_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'role' => 'admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $user = User::where('email', 'hacker@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);
    }

    public function test_registration_validation_fails_with_invalid_data(): void
    {
        // 1. Existing user
        User::factory()->create(['email' => 'exist@example.com']);

        $response = $this->post('/register', [
            'name' => '',
            'email' => 'exist@example.com',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'name' => 'Nama lengkap wajib diisi.',
            'email' => 'Email ini sudah terdaftar.',
            'password',
        ]);
    }

    public function test_authenticated_user_cannot_visit_registration_screen(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect(route('pengajuan.index', absolute: false));
    }
}
