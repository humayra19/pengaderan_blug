<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengajuan_form_can_be_rendered_for_user_without_application(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Calon Anggota BLUG',
            'email' => 'calon@blug.test',
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Calon Anggota BLUG');
        $response->assertSee('calon@blug.test');
        $response->assertSee('Nomor Induk Mahasiswa (NIM)');
        $response->assertSee('Program Studi');
        $response->assertSee('Angkatan');
        $response->assertSee('Nomor WhatsApp');
        $response->assertSee('Bidang Minat');
        $response->assertSee('Motivasi Bergabung');
        $response->assertSee('Pengalaman atau Skill');
        $response->assertSee('Kirim Pengajuan');

        foreach (config('blug.interests') as $interest) {
            $response->assertSee($interest);
        }
    }

    public function test_user_can_submit_membership_application_and_updates_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $currentYear = (int) date('Y');
        $validMotivation = trim(str_repeat('Saya sangat bersemangat belajar open source di UKM BLUG! ', 2)); // > 50 chars

        $response = $this->actingAs($user)->post(route('pengajuan.store'), [
            'nim' => '2201010012',
            'study_program' => 'Informatika',
            'batch_year' => $currentYear - 1,
            'phone' => '081234567890',
            'interest' => config('blug.interests')[0],
            'motivation' => $validMotivation,
            'experience' => 'Pernah menginstal Ubuntu dan belajar dasar Git.',
        ]);

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success', 'Pengajuan berhasil dikirim. Silakan tunggu peninjauan dari admin.');

        // Verifikasi data profil tersimpan di tabel users
        $user->refresh();
        $this->assertSame('2201010012', $user->nim);
        $this->assertSame('Informatika', $user->study_program);
        $this->assertEquals($currentYear - 1, $user->batch_year);
        $this->assertSame('081234567890', $user->phone);

        // Verifikasi pengajuan tersimpan di membership_applications
        $this->assertDatabaseHas('membership_applications', [
            'user_id' => $user->id,
            'interest' => config('blug.interests')[0],
            'motivation' => $validMotivation,
            'experience' => 'Pernah menginstal Ubuntu dan belajar dasar Git.',
            'status' => 'pending',
        ]);

        // Verifikasi relasi
        $this->assertCount(1, $user->applications);
        $this->assertNotNull($user->latestApplication);
        $this->assertSame('pending', $user->latestApplication->status);
        $this->assertSame($user->id, $user->latestApplication->user->id);
    }

    public function test_form_is_hidden_and_alert_is_shown_if_user_already_has_application(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->applications()->create([
            'interest' => config('blug.interests')[0],
            'motivation' => str_repeat('Motivasi untuk bergabung dengan UKM BLUG ', 3),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        // Kartu status pending tampil
        $response->assertSee('Menunggu');
        $response->assertSee('Pengajuan sedang ditinjau');
        // Form tidak tampil
        $response->assertDontSee('Kirim Pengajuan');
    }

    public function test_validation_fails_with_invalid_fields(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('pengajuan.store'), [
            'nim' => '123', // kurang dari 8 digit
            'study_program' => '',
            'batch_year' => 1990, // lebih tua dari 10 tahun
            'phone' => '0712345678', // bukan 08xx atau 628xx
            'interest' => 'Bidang Tidak Ada',
            'motivation' => 'Terlalu pendek', // < 50 karakter
            'experience' => str_repeat('A', 1001), // > 1000 karakter
        ]);

        $response->assertSessionHasErrors([
            'nim',
            'study_program',
            'batch_year',
            'phone',
            'interest',
            'motivation',
            'experience',
        ]);

        $this->assertDatabaseCount('membership_applications', 0);
    }
}
