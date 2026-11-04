<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_all_protected_routes(): void
    {
        $this->get('/pengajuan')->assertRedirect('/login');
        $this->post('/pengajuan')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
        $this->patch('/dashboard/pengajuan/1')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_user_can_access_user_routes_and_is_redirected_from_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // User routes: diperbolehkan
        $this->actingAs($user)->get('/pengajuan')->assertStatus(200);
        $this->actingAs($user)->post('/pengajuan')->assertRedirect(route('pengajuan.index'));
        $this->actingAs($user)->get('/profile')->assertStatus(200);

        // Admin routes: ditolak/dialihkan ke halaman user, bukan 403 atau 500
        $getDashboard = $this->actingAs($user)->get('/dashboard');
        $getDashboard->assertStatus(302);
        $getDashboard->assertRedirect(route('pengajuan.index'));

        $patchDashboard = $this->actingAs($user)->patch('/dashboard/pengajuan/1');
        $patchDashboard->assertStatus(302);
        $patchDashboard->assertRedirect(route('pengajuan.index'));
    }

    public function test_admin_can_access_admin_routes_and_is_redirected_from_user_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Admin routes: diperbolehkan
        $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $this->actingAs($admin)->patch('/dashboard/pengajuan/1')->assertRedirect(route('dashboard'));
        $this->actingAs($admin)->get('/profile')->assertStatus(200);

        // User routes: dialihkan ke halaman admin, bukan 403 atau 500
        $getPengajuan = $this->actingAs($admin)->get('/pengajuan');
        $getPengajuan->assertStatus(302);
        $getPengajuan->assertRedirect(route('dashboard'));

        $postPengajuan = $this->actingAs($admin)->post('/pengajuan');
        $postPengajuan->assertStatus(302);
        $postPengajuan->assertRedirect(route('dashboard'));
    }
}

