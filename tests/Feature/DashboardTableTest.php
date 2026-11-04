<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTableTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeUserWithApplication(string $status): MembershipApplication
    {
        $user = User::factory()->create([
            'role'          => 'user',
            'nim'           => fake()->unique()->numerify('##########'),
            'study_program' => 'Teknik Informatika',
        ]);

        return MembershipApplication::factory()
            ->{$status}()
            ->for($user)
            ->create();
    }

    // ------------------------------------------------------------------ //
    // 1. Tabel tampil dengan baris sesuai data
    // ------------------------------------------------------------------ //

    public function test_tabel_menampilkan_daftar_pengajuan(): void
    {
        $admin = $this->makeAdmin();

        $app1 = $this->makeUserWithApplication('pending');
        $app2 = $this->makeUserWithApplication('approved');
        $app3 = $this->makeUserWithApplication('rejected');

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);

        // Nama pendaftar tampil
        $response->assertSee($app1->user->name);
        $response->assertSee($app2->user->name);
        $response->assertSee($app3->user->name);

        // Badge status tampil
        $response->assertSee('status-pending',   false);
        $response->assertSee('status-approved',  false);
        $response->assertSee('status-rejected',  false);
        $response->assertSee('Menunggu');
        $response->assertSee('Diterima');
        $response->assertSee('Ditolak');

        // Tombol Lihat ada
        $response->assertSee('Lihat');

        // Tidak muncul pesan kosong
        $response->assertDontSee('Belum ada pengajuan.');
    }

    // ------------------------------------------------------------------ //
    // 2. Pesan kosong jika belum ada data
    // ------------------------------------------------------------------ //

    public function test_tabel_menampilkan_pesan_kosong_jika_belum_ada_data(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada pengajuan.');
    }

    // ------------------------------------------------------------------ //
    // 3. Pagination muncul saat data lebih dari 10 baris
    // ------------------------------------------------------------------ //

    public function test_pagination_muncul_saat_lebih_dari_10_baris(): void
    {
        $admin = $this->makeAdmin();

        // Buat 15 pengajuan
        for ($i = 0; $i < 15; $i++) {
            $this->makeUserWithApplication('pending');
        }

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        // Halaman 1 hanya menampilkan 10 baris, tidak 15
        $this->assertSame(10, substr_count($response->getContent(), 'Lihat'));
        // Link pagination ada
        $response->assertSee('pagination');
    }

    // ------------------------------------------------------------------ //
    // 4. Urutan terbaru di atas
    // ------------------------------------------------------------------ //

    public function test_urutan_terbaru_di_atas(): void
    {
        $admin = $this->makeAdmin();

        // Buat dua user dengan nama unik
        $userLama = User::factory()->create(['role' => 'user', 'name' => 'Pendaftar Lama']);
        $userBaru = User::factory()->create(['role' => 'user', 'name' => 'Pendaftar Baru']);

        // Buat aplikasi dengan created_at yang jelas berbeda
        $lama = MembershipApplication::factory()->pending()->for($userLama)->create([
            'created_at' => now()->subDays(10),
        ]);
        $baru = MembershipApplication::factory()->approved()->for($userBaru)->create([
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        $content = $response->getContent();

        // Gunakan atribut data-application-id yang unik per baris
        $posisiBaru = strpos($content, 'data-application-id="' . $baru->id . '"');
        $posisiLama = strpos($content, 'data-application-id="' . $lama->id . '"');

        $this->assertNotFalse($posisiBaru, 'Pengajuan baru tidak ditemukan di halaman.');
        $this->assertNotFalse($posisiLama, 'Pengajuan lama tidak ditemukan di halaman.');
        $this->assertLessThan(
            $posisiLama,
            $posisiBaru,
            'Pengajuan terbaru harus muncul lebih dulu (atas) daripada yang lama.'
        );
    }

    // ------------------------------------------------------------------ //
    // 5. Eager loading: relasi user dapat diakses tanpa error
    // ------------------------------------------------------------------ //

    public function test_eager_loading_user_berjalan_tanpa_error(): void
    {
        $admin = $this->makeAdmin();

        // Buat 5 pengajuan dari 5 user berbeda
        for ($i = 0; $i < 5; $i++) {
            $this->makeUserWithApplication('pending');
        }

        // Jika terjadi N+1 dan eager loading tidak bekerja, query lazy load
        // akan menyebabkan lebih banyak hit ke DB, tapi test ini memverifikasi
        // minimal bahwa data ditampilkan dengan benar untuk semua baris.
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Semua nama user (5 baris) harus tampil
        $apps = \App\Models\MembershipApplication::with('user')->latest()->get();
        foreach ($apps as $app) {
            $response->assertSee($app->user->name);
        }
    }

    // ------------------------------------------------------------------ //
    // 6. Factory MembershipApplication bekerja dengan state helpers
    // ------------------------------------------------------------------ //

    public function test_factory_berfungsi_dengan_semua_state(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $pending  = MembershipApplication::factory()->pending()->for($user)->create();
        $approved = MembershipApplication::factory()->approved()->for($user)->create();
        $rejected = MembershipApplication::factory()->rejected()->for($user)->create();

        $this->assertSame('pending',  $pending->status);
        $this->assertNull($pending->reviewed_at);

        $this->assertSame('approved', $approved->status);
        $this->assertNotNull($approved->reviewed_at);

        $this->assertSame('rejected', $rejected->status);
        $this->assertNotNull($rejected->admin_note);
        $this->assertNotNull($rejected->reviewed_at);
    }
}
