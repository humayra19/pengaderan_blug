<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_page_can_be_rendered_without_login(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertStatus(200);
        $response->assertSee('BLUG');
        $response->assertSee('mark-yellow', false);
        $response->assertSee('Apa itu BLUG?');
        $response->assertSee('Yang kami lakukan');
        $response->assertSee('Cara bergabung');
        $response->assertSee('01');
        $response->assertSee('02');
        $response->assertSee('03');
        $response->assertSee('Buat akun');
        $response->assertSee('Isi pengajuan');
        $response->assertSee('Tunggu keputusan');
        $response->assertSee(route('register'));
        $response->assertSee('Daftar Sekarang');
    }

    public function test_daftar_sekarang_links_to_pengajuan_for_authenticated_regular_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('beranda'));

        $response->assertStatus(200);
        $response->assertSee(route('pengajuan.index'));
        $response->assertDontSee(route('register'));
    }

    public function test_daftar_sekarang_links_to_dashboard_for_authenticated_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('beranda'));

        $response->assertStatus(200);
        $response->assertSee(route('dashboard'));
        $response->assertDontSee(route('register'));
    }

    public function test_pengajuan_and_dashboard_placeholder_routes_are_accessible_by_respective_roles(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $responsePengajuan = $this->actingAs($user)->get(route('pengajuan.index'));
        $responsePengajuan->assertStatus(200);
        $responsePengajuan->assertSee('Pengajuan');

        $admin = User::factory()->create(['role' => 'admin']);
        $responseDashboard = $this->actingAs($admin)->get(route('dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('Dashboard');
    }
}
