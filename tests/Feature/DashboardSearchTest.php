<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSearchTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * Buat aplikasi milik user baru dengan nama/NIM tertentu.
     */
    private function makeApp(string $name, string $nim, string $status = 'pending'): MembershipApplication
    {
        $user = User::factory()->create([
            'name' => $name,
            'nim'  => $nim,
            'role' => 'user',
        ]);

        return MembershipApplication::factory()
            ->for($user)
            ->$status()
            ->create();
    }

    // ------------------------------------------------------------------ tests

    /** Cari berdasarkan nama: hanya yang cocok yang muncul. */
    public function test_search_by_name_returns_matching_rows(): void
    {
        $this->makeApp('Budi Santoso', '12345001');
        $this->makeApp('Siti Rahayu', '12345002');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['q' => 'Budi']));

        $response->assertStatus(200);
        $response->assertSeeText('Budi Santoso');
        $response->assertDontSeeText('Siti Rahayu');
    }

    /** Cari berdasarkan NIM: hanya yang cocok yang muncul. */
    public function test_search_by_nim_returns_matching_rows(): void
    {
        $this->makeApp('Andi Wijaya', '20231001');
        $this->makeApp('Dewi Lestari', '20232002');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['q' => '20231001']));

        $response->assertStatus(200);
        $response->assertSeeText('Andi Wijaya');
        $response->assertDontSeeText('Dewi Lestari');
    }

    /** Filter status pending: hanya yang berstatus pending yang muncul. */
    public function test_filter_by_pending_shows_only_pending(): void
    {
        $this->makeApp('Pending User', '99991000', 'pending');
        $this->makeApp('Approved User', '99992000', 'approved');
        $this->makeApp('Rejected User', '99993000', 'rejected');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSeeText('Pending User');
        $response->assertDontSeeText('Approved User');
        $response->assertDontSeeText('Rejected User');
    }

    /** Filter status approved: hanya yang berstatus approved yang muncul. */
    public function test_filter_by_approved_shows_only_approved(): void
    {
        $this->makeApp('Pending User', '88881000', 'pending');
        $this->makeApp('Approved User', '88882000', 'approved');
        $this->makeApp('Rejected User', '88883000', 'rejected');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSeeText('Approved User');
        $response->assertDontSeeText('Pending User');
        $response->assertDontSeeText('Rejected User');
    }

    /** Filter status rejected: hanya yang berstatus rejected yang muncul. */
    public function test_filter_by_rejected_shows_only_rejected(): void
    {
        $this->makeApp('Pending User', '77771000', 'pending');
        $this->makeApp('Approved User', '77772000', 'approved');
        $this->makeApp('Rejected User', '77773000', 'rejected');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['status' => 'rejected']));

        $response->assertStatus(200);
        $response->assertSeeText('Rejected User');
        $response->assertDontSeeText('Pending User');
        $response->assertDontSeeText('Approved User');
    }

    /** Gabungan q + status: hanya yang memenuhi KEDUA kriteria yang muncul. */
    public function test_combined_search_and_filter(): void
    {
        $this->makeApp('Rudi Pending', '55551001', 'pending');
        $this->makeApp('Rudi Approved', '55552002', 'approved');
        $this->makeApp('Lain Pending', '55553003', 'pending');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['q' => 'Rudi', 'status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSeeText('Rudi Pending');
        $response->assertDontSeeText('Rudi Approved');
        $response->assertDontSeeText('Lain Pending');
    }

    /** Nilai status tidak valid diabaikan — tidak error, semua baris tampil. */
    public function test_invalid_status_value_is_ignored(): void
    {
        $this->makeApp('User Satu', '11110001', 'pending');
        $this->makeApp('User Dua', '11110002', 'approved');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['status' => 'invalid_value']));

        $response->assertStatus(200);
        $response->assertSeeText('User Satu');
        $response->assertSeeText('User Dua');
    }

    /** Hasil kosong saat filter aktif: tampil "Tidak ada pengajuan yang cocok." */
    public function test_empty_results_with_filter_shows_correct_message(): void
    {
        $this->makeApp('Ada User', '22220001', 'pending');

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['q' => 'zzz_tidak_ada_zzz']));

        $response->assertStatus(200);
        $response->assertSeeText('Tidak ada pengajuan yang cocok.');
        $response->assertDontSeeText('Belum ada pengajuan.');
    }

    /** Hasil kosong tanpa filter: tampil "Belum ada pengajuan." */
    public function test_empty_results_without_filter_shows_correct_message(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeText('Belum ada pengajuan.');
        $response->assertDontSeeText('Tidak ada pengajuan yang cocok.');
    }

    /** Tautan pagination mengandung parameter q dan status. */
    public function test_pagination_link_preserves_query_string(): void
    {
        // Buat 15 aplikasi dengan nama yang bisa dicari agar ada 2 halaman
        for ($i = 1; $i <= 15; $i++) {
            $this->makeApp(
                'Cari User ' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                '30' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'pending',
            );
        }

        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['q' => 'Cari', 'status' => 'pending']));

        $response->assertStatus(200);
        // Tautan pagination halaman 2 harus mengandung q dan status
        $response->assertSee('page=2');
        $response->assertSee('q=Cari');
        $response->assertSee('status=pending');
    }

    /** Kartu ringkasan tetap menampilkan semua data, tidak ikut filter. */
    public function test_stat_cards_are_not_affected_by_filter(): void
    {
        $this->makeApp('Pending Satu', '40001001', 'pending');
        $this->makeApp('Pending Dua', '40001002', 'pending');
        $this->makeApp('Approved Satu', '40002001', 'approved');
        $this->makeApp('Rejected Satu', '40003001', 'rejected');

        // Filter hanya menampilkan pending, tapi kartu total harus tetap 4
        $response = $this->actingAs($this->admin())
            ->get(route('dashboard', ['status' => 'pending']));

        $response->assertStatus(200);

        // Kartu Total harus menampilkan 4 (semua data)
        $response->assertSee('>4<', false);

        // Kartu Menunggu = 2, Diterima = 1, Ditolak = 1
        // Cek via teks di stat-card; gunakan regex supaya tidak salah tangkap
        $html = $response->getContent();
        preg_match_all('/<div class="stat-value">(.*?)<\/div>/s', $html, $matches);
        $values = array_map('trim', $matches[1]);

        $this->assertEquals('4', $values[0]); // Total
        $this->assertEquals('2', $values[1]); // Menunggu
        $this->assertEquals('1', $values[2]); // Diterima
        $this->assertEquals('1', $values[3]); // Ditolak
    }

    /** Tombol Reset muncul bila ada filter aktif, tersembunyi bila tidak ada. */
    public function test_reset_link_visible_only_when_filter_active(): void
    {
        $admin = $this->admin();

        // Tanpa filter: tombol Reset tidak ada
        $responseNoFilter = $this->actingAs($admin)->get(route('dashboard'));
        $responseNoFilter->assertStatus(200);
        $responseNoFilter->assertDontSeeText('Reset');

        // Dengan filter q: tombol Reset ada
        $responseWithQ = $this->actingAs($admin)->get(route('dashboard', ['q' => 'abc']));
        $responseWithQ->assertStatus(200);
        $responseWithQ->assertSeeText('Reset');

        // Dengan filter status: tombol Reset ada
        $responseWithStatus = $this->actingAs($admin)->get(route('dashboard', ['status' => 'pending']));
        $responseWithStatus->assertStatus(200);
        $responseWithStatus->assertSeeText('Reset');
    }
}

