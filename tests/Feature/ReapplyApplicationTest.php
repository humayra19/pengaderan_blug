<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReapplyApplicationTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ //
    // Helpers
    // ------------------------------------------------------------------ //

    private function makeUser(): User
    {
        return User::factory()->create([
            'role'          => 'user',
            'nim'           => '2201010001',
            'study_program' => 'Teknik Informatika',
            'batch_year'    => (int) date('Y') - 1,
            'phone'         => '081234567890',
        ]);
    }

    private function validPayload(array $override = []): array
    {
        return array_merge([
            'nim'           => '2201010099',
            'study_program' => 'Informatika',
            'batch_year'    => (int) date('Y') - 1,
            'phone'         => '081234567890',
            'interest'      => config('blug.interests')[0],
            'motivation'    => str_repeat('Saya ingin bergabung BLUG karena saya suka open source. ', 2),
            'experience'    => 'Pernah menginstal Arch Linux.',
        ], $override);
    }

    // ------------------------------------------------------------------ //
    // 1. Tombol "Ajukan Ulang" muncul di keadaan rejected
    // ------------------------------------------------------------------ //

    public function test_tombol_ajukan_ulang_muncul_saat_rejected(): void
    {
        $user = $this->makeUser();
        $user->applications()->create([
            'interest'    => config('blug.interests')[0],
            'motivation'  => str_repeat('Motivasi bergabung BLUG sangat kuat. ', 3),
            'status'      => 'rejected',
            'admin_note'  => 'Mohon perbaiki berkas.',
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Ajukan Ulang');
        $response->assertSee(route('pengajuan.index', ['ulang' => '1']));
    }

    // ------------------------------------------------------------------ //
    // 2. Tombol "Ajukan Ulang" TIDAK muncul di pending / approved
    // ------------------------------------------------------------------ //

    public function test_tombol_ajukan_ulang_tidak_muncul_saat_pending(): void
    {
        $user = $this->makeUser();
        $user->applications()->create([
            'interest'   => config('blug.interests')[0],
            'motivation' => str_repeat('Motivasi bergabung BLUG sangat kuat. ', 3),
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Ajukan Ulang');
    }

    // ------------------------------------------------------------------ //
    // 3. GET ?ulang=1 pada rejected: form terbuka dengan isian lama
    // ------------------------------------------------------------------ //

    public function test_get_ulang_membuka_form_dengan_isian_lama(): void
    {
        $user        = $this->makeUser();
        $oldInterest = config('blug.interests')[1];
        $oldMotivasi = str_repeat('Motivasi lama saya untuk bergabung dengan BLUG. ', 2);
        $oldExp      = 'Pengalaman lama: pernah menginstal Linux.';

        $user->applications()->create([
            'interest'    => $oldInterest,
            'motivation'  => $oldMotivasi,
            'experience'  => $oldExp,
            'status'      => 'rejected',
            'admin_note'  => 'Coba lagi.',
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.index', ['ulang' => '1']));

        $response->assertStatus(200);
        // Form tampil (ada tombol submit)
        $response->assertSee('Kirim Pengajuan');
        // Isian lama terisi sebagai nilai awal
        $response->assertSee($oldInterest);
        $response->assertSee(e($oldMotivasi));
        $response->assertSee(e($oldExp));
        // Data profil user juga terisi (NIM dari users)
        $response->assertSee($user->nim);
    }

    // ------------------------------------------------------------------ //
    // 4. GET ?ulang=1 pada pending: diabaikan, kartu pending tetap tampil
    // ------------------------------------------------------------------ //

    public function test_get_ulang_diabaikan_saat_pending(): void
    {
        $user = $this->makeUser();
        $user->applications()->create([
            'interest'   => config('blug.interests')[0],
            'motivation' => str_repeat('Motivasi bergabung BLUG. ', 4),
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('pengajuan.index', ['ulang' => '1']));

        $response->assertStatus(200);
        // Kartu Menunggu tetap tampil, form tidak terbuka
        $response->assertSee('Menunggu');
        $response->assertDontSee('Kirim Pengajuan');
    }

    // ------------------------------------------------------------------ //
    // 5. Inti US-07: rejected → ajukan ulang → 2 baris, terakhir pending
    // ------------------------------------------------------------------ //

    public function test_ajukan_ulang_membuat_baris_baru_dan_status_pending(): void
    {
        $user = $this->makeUser();

        // Buat pengajuan pertama yang ditolak
        $user->applications()->create([
            'interest'    => config('blug.interests')[0],
            'motivation'  => str_repeat('Motivasi pertama saya untuk bergabung BLUG. ', 2),
            'experience'  => 'Pengalaman pertama.',
            'status'      => 'rejected',
            'admin_note'  => 'Kurang pengalaman.',
            'reviewed_at' => now(),
        ]);

        $this->assertDatabaseCount('membership_applications', 1);

        // Kirim pengajuan ulang
        $response = $this->actingAs($user)->post(route('pengajuan.store'), $this->validPayload([
            'motivation' => str_repeat('Motivasi ulang saya lebih kuat setelah diperbaiki. ', 2),
            'experience' => 'Sekarang sudah lebih berpengalaman.',
        ]));

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success');

        // Harus ada 2 baris di DB
        $this->assertDatabaseCount('membership_applications', 2);

        // Baris lama tetap ada dengan status rejected
        $this->assertDatabaseHas('membership_applications', [
            'user_id' => $user->id,
            'status'  => 'rejected',
        ]);

        // Baris baru berstatus pending
        $this->assertDatabaseHas('membership_applications', [
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);

        // latestApplication menunjuk ke yang baru (pending)
        $user->refresh();
        $user->load('latestApplication');
        $this->assertSame('pending', $user->latestApplication->status);
    }

    // ------------------------------------------------------------------ //
    // 6. Aturan server masih berlaku: rejected → pending → POST ditolak
    // ------------------------------------------------------------------ //

    public function test_aturan_server_masih_berlaku_setelah_ajukan_ulang(): void
    {
        $user = $this->makeUser();

        // Pengajuan pertama ditolak
        $user->applications()->create([
            'interest'    => config('blug.interests')[0],
            'motivation'  => str_repeat('Motivasi pertama bergabung BLUG. ', 3),
            'status'      => 'rejected',
            'reviewed_at' => now(),
        ]);

        // Ajukan ulang berhasil → sekarang ada 1 pending
        $this->actingAs($user)->post(route('pengajuan.store'), $this->validPayload());
        $this->assertDatabaseCount('membership_applications', 2);

        // Coba kirim lagi saat masih pending → harus ditolak
        $response = $this->actingAs($user)->post(route('pengajuan.store'), $this->validPayload());

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('error');
        $response->assertSessionMissing('success');

        // Jumlah baris tidak bertambah (tetap 2)
        $this->assertDatabaseCount('membership_applications', 2);
    }
}

