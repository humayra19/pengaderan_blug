<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationStatusTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ //
    // Helpers
    // ------------------------------------------------------------------ //

    private function makeUser(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    private function makeApplication(User $user, string $status, ?string $adminNote = null): MembershipApplication
    {
        return $user->applications()->create([
            'interest'    => config('blug.interests')[0],
            'motivation'  => str_repeat('Motivasi bergabung BLUG sangat kuat. ', 3),
            'status'      => $status,
            'admin_note'  => $adminNote,
            'reviewed_at' => in_array($status, ['approved', 'rejected']) ? now() : null,
        ]);
    }

    private function validPayload(): array
    {
        $currentYear = (int) date('Y');

        return [
            'nim'          => '2201010012',
            'study_program'=> 'Informatika',
            'batch_year'   => $currentYear - 1,
            'phone'        => '081234567890',
            'interest'     => config('blug.interests')[0],
            'motivation'   => str_repeat('Saya ingin bergabung BLUG karena saya suka open source. ', 2),
            'experience'   => 'Pernah menginstal Arch Linux.',
        ];
    }

    // ------------------------------------------------------------------ //
    // Keadaan 1: belum pernah mengajukan — form tampil
    // ------------------------------------------------------------------ //

    public function test_halaman_menampilkan_form_bila_belum_pernah_mengajukan(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Kirim Pengajuan');
        $response->assertSee('Nomor Induk Mahasiswa (NIM)');
        $response->assertDontSee('Menunggu');
        $response->assertDontSee('Diterima');
        $response->assertDontSee('Ditolak');
    }

    // ------------------------------------------------------------------ //
    // Keadaan 2: pending — kartu "Menunggu" + ringkasan isian
    // ------------------------------------------------------------------ //

    public function test_halaman_menampilkan_kartu_menunggu_bila_pending(): void
    {
        $user        = $this->makeUser();
        $application = $this->makeApplication($user, 'pending');

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Menunggu');
        $response->assertSee('Pengajuan sedang ditinjau');
        $response->assertSee($application->interest);
        $response->assertSee($application->motivation);
        $response->assertDontSee('Kirim Pengajuan');   // form tersembunyi
        $response->assertDontSee('Diterima');
        $response->assertDontSee('Ditolak');
    }

    // ------------------------------------------------------------------ //
    // Keadaan 3: approved — kartu "Diterima"
    // ------------------------------------------------------------------ //

    public function test_halaman_menampilkan_kartu_diterima_bila_approved(): void
    {
        $user = $this->makeUser();
        $this->makeApplication($user, 'approved', 'Selamat bergabung!');

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Diterima');
        $response->assertSee('Selamat, pengajuan Anda diterima');
        $response->assertSee('Selamat bergabung!');   // catatan admin
        $response->assertDontSee('Kirim Pengajuan');
        $response->assertDontSee('Menunggu');
        $response->assertDontSee('Ditolak');
    }

    public function test_halaman_menampilkan_kartu_diterima_tanpa_catatan(): void
    {
        $user = $this->makeUser();
        $this->makeApplication($user, 'approved', null);

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Diterima');
        $response->assertDontSee('Catatan dari Admin');
    }

    // ------------------------------------------------------------------ //
    // Keadaan 4: rejected — kartu "Ditolak" + catatan admin
    // ------------------------------------------------------------------ //

    public function test_halaman_menampilkan_kartu_ditolak_bila_rejected(): void
    {
        $user = $this->makeUser();
        $this->makeApplication($user, 'rejected', 'Mohon lengkapi pengalaman teknis Anda.');

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Ditolak');
        $response->assertSee('Pengajuan tidak dapat diterima');
        $response->assertSee('Mohon lengkapi pengalaman teknis Anda.');
        $response->assertDontSee('Kirim Pengajuan');
        $response->assertDontSee('Menunggu');
        $response->assertDontSee('Diterima');
        // Tombol "Ajukan Ulang" tersedia sejak US-07
        $response->assertSee('Ajukan Ulang');
    }

    // ------------------------------------------------------------------ //
    // Aturan server: POST ditolak bila masih pending
    // ------------------------------------------------------------------ //

    public function test_post_pengajuan_ditolak_saat_masih_pending(): void
    {
        $user = $this->makeUser();
        $this->makeApplication($user, 'pending');

        $this->assertDatabaseCount('membership_applications', 1);

        $response = $this->actingAs($user)->post(route('pengajuan.store'), $this->validPayload());

        // Harus redirect ke pengajuan.index dengan pesan error, bukan sukses
        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');

        // Jumlah baris tidak bertambah
        $this->assertDatabaseCount('membership_applications', 1);
    }

    // ------------------------------------------------------------------ //
    // Aturan server: POST ditolak bila sudah approved
    // ------------------------------------------------------------------ //

    public function test_post_pengajuan_ditolak_saat_sudah_approved(): void
    {
        $user = $this->makeUser();
        $this->makeApplication($user, 'approved');

        $this->assertDatabaseCount('membership_applications', 1);

        $response = $this->actingAs($user)->post(route('pengajuan.store'), $this->validPayload());

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');

        // Jumlah baris tidak bertambah
        $this->assertDatabaseCount('membership_applications', 1);
    }

    // ------------------------------------------------------------------ //
    // Kontrol: POST berhasil bila pengajuan terakhir rejected
    // ------------------------------------------------------------------ //

    public function test_post_pengajuan_berhasil_bila_terakhir_rejected(): void
    {
        $user = $this->makeUser();
        $this->makeApplication($user, 'rejected');

        $this->assertDatabaseCount('membership_applications', 1);

        $response = $this->actingAs($user)->post(route('pengajuan.store'), $this->validPayload());

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success');

        // Baris baru terbuat
        $this->assertDatabaseCount('membership_applications', 2);
        $this->assertDatabaseHas('membership_applications', [
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);
    }
}

