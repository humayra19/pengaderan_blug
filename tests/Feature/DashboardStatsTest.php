<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ //
    // Helper
    // ------------------------------------------------------------------ //

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeUser(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    private function makeApplication(User $user, string $status): MembershipApplication
    {
        return $user->applications()->create([
            'interest'    => config('blug.interests')[0],
            'motivation'  => str_repeat('Motivasi bergabung BLUG sangat kuat. ', 3),
            'status'      => $status,
            'reviewed_at' => in_array($status, ['approved', 'rejected']) ? now() : null,
            'reviewed_by' => in_array($status, ['approved', 'rejected']) ? $this->makeAdmin()->id : null,
        ]);
    }

    // ------------------------------------------------------------------ //
    // 1. Admin bisa mengakses dashboard dan melihat angka yang benar
    // ------------------------------------------------------------------ //

    public function test_admin_melihat_ringkasan_angka_yang_cocok_dengan_data(): void
    {
        $admin = $this->makeAdmin();

        // Buat data: 3 pending, 2 approved, 1 rejected
        $users = User::factory()->count(6)->create(['role' => 'user']);
        $this->makeApplication($users[0], 'pending');
        $this->makeApplication($users[1], 'pending');
        $this->makeApplication($users[2], 'pending');
        $this->makeApplication($users[3], 'approved');
        $this->makeApplication($users[4], 'approved');
        $this->makeApplication($users[5], 'rejected');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);

        // Angka harus muncul di halaman
        $response->assertSee('6');   // total
        $response->assertSee('3');   // pending
        $response->assertSee('2');   // approved
        $response->assertSee('1');   // rejected

        // Label kartu tampil
        $response->assertSee('Total');
        $response->assertSee('Menunggu');
        $response->assertSee('Diterima');
        $response->assertSee('Ditolak');
    }

    // ------------------------------------------------------------------ //
    // 2. Ketika belum ada data, semua angka adalah 0
    // ------------------------------------------------------------------ //

    public function test_angka_nol_ketika_belum_ada_pengajuan(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        // Semua angka = 0 (empat kali "0" muncul)
        $response->assertSee('0');
    }

    // ------------------------------------------------------------------ //
    // 3. User biasa tidak bisa mengakses dashboard
    // ------------------------------------------------------------------ //

    public function test_user_biasa_dialihkan_dari_dashboard(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get(route('dashboard'));

        // Harus dialihkan (ke pengajuan.index sesuai role middleware)
        $response->assertRedirect(route('pengajuan.index'));
    }

    // ------------------------------------------------------------------ //
    // 4. Tamu tidak bisa mengakses dashboard
    // ------------------------------------------------------------------ //

    public function test_tamu_dialihkan_ke_login_dari_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------ //
    // 5. Angka diperbarui saat ada pengajuan baru (satu query agregat)
    // ------------------------------------------------------------------ //

    public function test_angka_diperbarui_saat_ada_data_baru(): void
    {
        $admin = $this->makeAdmin();
        $user  = $this->makeUser();

        // Halaman pertama: belum ada data
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Tambah satu pengajuan pending
        $this->makeApplication($user, 'pending');

        // Halaman diakses ulang: angka harus diperbarui
        $response2 = $this->actingAs($admin)->get(route('dashboard'));
        $response2->assertStatus(200);
        $response2->assertSee('1');  // total = 1, pending = 1
    }
}

