<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * US-12 — Menerima atau menolak pengajuan
 *
 * Menguji aksi admin memutuskan pengajuan keanggotaan (Terima / Tolak),
 * validasi catatan wajib saat menolak, perubahan keputusan (koreksi),
 * pembatasan akses hanya untuk admin, serta perubahan status yang
 * langsung tampak bagi pendaftar di /pengajuan.
 */
class ApplicationDecisionTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function createApplicant(): User
    {
        return User::factory()->create([
            'role' => 'user',
        ]);
    }

    private function makeAppRecord(User $user, string $status = 'pending', ?string $note = null): MembershipApplication
    {
        return MembershipApplication::factory()
            ->for($user)
            ->$status()
            ->create([
                'admin_note' => $note,
            ]);
    }

    // ------------------------------------------------------------------ tests

    /** Admin dapat menerima pengajuan (status menjadi approved). */
    public function test_admin_can_approve_application(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'approved',
                'admin_note' => 'Selamat bergabung di BLUG!',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $app->refresh();
        $this->assertEquals('approved', $app->status);
        $this->assertEquals('Selamat bergabung di BLUG!', $app->admin_note);
        $this->assertEquals($admin->id, $app->reviewed_by);
        $this->assertNotNull($app->reviewed_at);

        // Langsung tampak oleh pendaftar di /pengajuan
        $pengajuanResponse = $this->actingAs($applicant)->get(route('pengajuan.index'));
        $pengajuanResponse->assertStatus(200);
        $pengajuanResponse->assertSeeText('Diterima');
        $pengajuanResponse->assertSeeText('Selamat bergabung di BLUG!');
    }

    /** Admin dapat menerima pengajuan tanpa catatan (catatan bersifat opsional). */
    public function test_admin_can_approve_application_without_note(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'approved',
                'admin_note' => '',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $app->refresh();
        $this->assertEquals('approved', $app->status);
        $this->assertNull($app->admin_note);
    }

    /** Admin dapat menolak pengajuan dengan catatan yang jelas. */
    public function test_admin_can_reject_application_with_note(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'rejected',
                'admin_note' => 'Motivasi belum mencerminkan komitmen belajar.',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $app->refresh();
        $this->assertEquals('rejected', $app->status);
        $this->assertEquals('Motivasi belum mencerminkan komitmen belajar.', $app->admin_note);
        $this->assertEquals($admin->id, $app->reviewed_by);
        $this->assertNotNull($app->reviewed_at);

        // Langsung tampak oleh pendaftar di /pengajuan
        $pengajuanResponse = $this->actingAs($applicant)->get(route('pengajuan.index'));
        $pengajuanResponse->assertStatus(200);
        $pengajuanResponse->assertSeeText('Ditolak');
        $pengajuanResponse->assertSeeText('Motivasi belum mencerminkan komitmen belajar.');
    }

    /** Menolak pengajuan tanpa catatan harus ditolak oleh validasi. */
    public function test_rejecting_without_note_fails_validation(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->from(route('dashboard'))
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'rejected',
                'admin_note' => '',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('admin_note');

        $app->refresh();
        // Status tetap pending, tidak berubah
        $this->assertEquals('pending', $app->status);
    }

    /** Menolak pengajuan hanya dengan spasi harus ditolak oleh validasi. */
    public function test_rejecting_with_whitespace_note_fails_validation(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->from(route('dashboard'))
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'rejected',
                'admin_note' => '    ',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('admin_note');

        $app->refresh();
        $this->assertEquals('pending', $app->status);
    }

    /** Admin dapat mengubah keputusan yang sudah dibuat (untuk koreksi). */
    public function test_admin_can_update_previous_decision(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        // Awalnya rejected
        $app = $this->makeAppRecord($applicant, 'rejected', 'Alasan penolakan awal');

        // Admin mengoreksi menjadi approved
        $response = $this->actingAs($admin)
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'approved',
                'admin_note' => 'Dikoreksi: pendaftar memenuhi syarat.',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $app->refresh();
        $this->assertEquals('approved', $app->status);
        $this->assertEquals('Dikoreksi: pendaftar memenuhi syarat.', $app->admin_note);
        $this->assertEquals($admin->id, $app->reviewed_by);

        // Pendaftar sekarang melihat status approved
        $pengajuanResponse = $this->actingAs($applicant)->get(route('pengajuan.index'));
        $pengajuanResponse->assertSeeText('Diterima');
    }

    /** User biasa tidak bisa mengakses rute keputusan pengajuan. */
    public function test_regular_user_cannot_access_decision_route(): void
    {
        $user      = $this->createApplicant();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($user)
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'approved',
                'admin_note' => 'Mencoba ubah status secara ilegal',
            ]);

        // Dialihkan oleh role middleware
        $response->assertStatus(302);
        $response->assertRedirect(route('pengajuan.index'));

        $app->refresh();
        // Status tetap pending
        $this->assertEquals('pending', $app->status);
        $this->assertNull($app->reviewed_by);
    }

    /** Pengunjung belum login tidak bisa mengakses rute keputusan. */
    public function test_guest_cannot_access_decision_route(): void
    {
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->patch(route('dashboard.pengajuan.update', $app->id), [
            'decision' => 'approved',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

        $app->refresh();
        $this->assertEquals('pending', $app->status);
    }

    /** Simpan keputusan mempertahankan query filter pencarian & halaman pagination. */
    public function test_decision_redirect_preserves_query_string(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision'   => 'approved',
                'admin_note' => 'OK',
                'q'          => 'Budi',
                'status'     => 'pending',
                'page'       => 2,
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard', [
            'q'      => 'Budi',
            'status' => 'pending',
            'page'   => 2,
        ]));
    }

    /** Nilai decision yang tidak valid gagal pada validasi. */
    public function test_invalid_decision_value_fails_validation(): void
    {
        $admin     = $this->createAdmin();
        $applicant = $this->createApplicant();
        $app       = $this->makeAppRecord($applicant, 'pending');

        $response = $this->actingAs($admin)
            ->from(route('dashboard'))
            ->patch(route('dashboard.pengajuan.update', $app->id), [
                'decision' => 'something_invalid',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('decision');

        $app->refresh();
        $this->assertEquals('pending', $app->status);
    }
}
