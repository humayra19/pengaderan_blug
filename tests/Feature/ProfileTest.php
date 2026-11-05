<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * US-13 — Mengubah Data Diri
 *
 * Menguji tampilan profil sesuai peran, pembaruan data diri (nama, email, WA, NIM, prodi, angkatan),
 * validasi email unik, pencegahan admin mengubah field akademik,
 * serta memastikan baris pengajuan keanggotaan tidak terpengaruh saat profil berubah.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function createApplicantUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name'          => 'Pendaftar Asli',
            'email'         => 'pendaftar@example.com',
            'phone'         => '081234567890',
            'nim'           => '20231001',
            'study_program' => 'Teknik Informatika',
            'batch_year'    => '2023',
            'role'          => 'user',
        ], $attributes));
    }

    private function createAdminUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name'  => 'Admin BLUG',
            'email' => 'admin@blug.test',
            'phone' => '089876543210',
            'role'  => 'admin',
        ], $attributes));
    }

    // ------------------------------------------------------------------ tests

    /** Halaman profil tampil untuk pengguna dengan role user dan menampilkan field akademik. */
    public function test_profile_page_is_displayed_for_user(): void
    {
        $user = $this->createApplicantUser();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSeeText('Data diri');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="phone"', false);
        $response->assertSee('name="nim"', false);
        $response->assertSee('name="study_program"', false);
        $response->assertSee('name="batch_year"', false);
        $response->assertSeeText('Keluar');
    }

    /** Admin tidak melihat field NIM, program studi, dan angkatan di profil. */
    public function test_profile_page_for_admin_does_not_show_academic_fields(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSeeText('Data diri');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="phone"', false);
        // Admin TIDAK melihat field akademik
        $response->assertDontSee('name="nim"', false);
        $response->assertDontSee('name="study_program"', false);
        $response->assertDontSee('name="batch_year"', false);
    }

    /** Pendaftar (role: user) dapat memperbarui seluruh data diri miliknya. */
    public function test_user_can_update_profile_information(): void
    {
        $user = $this->createApplicantUser();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name'          => 'Nama Baru Pendaftar',
            'email'         => 'baru@example.com',
            'phone'         => '082233445566',
            'nim'           => '20239999',
            'study_program' => 'Sistem Informasi',
            'batch_year'    => '2024',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nama Baru Pendaftar', $user->name);
        $this->assertSame('baru@example.com', $user->email);
        $this->assertSame('082233445566', $user->phone);
        $this->assertSame('20239999', $user->nim);
        $this->assertSame('Sistem Informasi', $user->study_program);
        $this->assertSame('2024', (string) $user->batch_year);
    }

    /** Admin dapat memperbarui nama, email, dan nomor WhatsApp miliknya. */
    public function test_admin_can_update_profile_information(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->patch(route('profile.update'), [
            'name'  => 'Admin Baru',
            'email' => 'adminbaru@blug.test',
            'phone' => '087711223344',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertSame('Admin Baru', $admin->name);
        $this->assertSame('adminbaru@blug.test', $admin->email);
        $this->assertSame('087711223344', $admin->phone);
    }

    /** Email yang bentrok dengan akun lain ditolak oleh validasi. */
    public function test_email_must_be_unique_except_for_current_user(): void
    {
        $user1 = $this->createApplicantUser(['email' => 'user1@example.com']);
        $user2 = $this->createApplicantUser(['email' => 'user2@example.com']);

        // User 2 mencoba memakai email user 1
        $response = $this->actingAs($user2)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name'          => 'User Dua',
                'email'         => 'user1@example.com',
                'phone'         => '081234567890',
                'nim'           => '20231002',
                'study_program' => 'Teknik Informatika',
                'batch_year'    => '2023',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors('email');

        $user2->refresh();
        $this->assertSame('user2@example.com', $user2->email);

        // Namun user 1 boleh menyimpan profil dengan email miliknya sendiri (tidak bentrok)
        $selfResponse = $this->actingAs($user1)
            ->patch(route('profile.update'), [
                'name'          => 'User Satu Diperbarui',
                'email'         => 'user1@example.com',
                'phone'         => '081234567890',
                'nim'           => '20231001',
                'study_program' => 'Teknik Informatika',
                'batch_year'    => '2023',
            ]);

        $selfResponse->assertSessionHasNoErrors();
    }

    /** Admin tidak bisa mengubah NIM (atau field akademik) lewat request manual. */
    public function test_admin_cannot_update_nim_via_manual_request(): void
    {
        $admin = $this->createAdminUser(['nim' => null]);

        $response = $this->actingAs($admin)->patch(route('profile.update'), [
            'name'          => 'Admin Tetap',
            'email'         => 'admin@blug.test',
            'phone'         => '089876543210',
            'nim'           => '123456789', // Request ilegal yang disuntikkan
            'study_program' => 'Teknik Informatika',
            'batch_year'    => '2023',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $admin->refresh();
        // Field NIM tetap null, tidak terpengaruh request manual
        $this->assertNull($admin->nim);
        $this->assertNull($admin->study_program);
        $this->assertNull($admin->batch_year);
    }

    /**
     * Perubahan profil TIDAK mengubah baris membership_applications yang sudah ada.
     */
    public function test_profile_update_does_not_modify_existing_membership_applications(): void
    {
        $user = $this->createApplicantUser();

        $application = MembershipApplication::factory()->create([
            'user_id'    => $user->id,
            'interest'   => 'Web Development',
            'motivation' => 'Motivasi asli sebelum profil diubah oleh pengguna.',
            'experience' => 'Pengalaman awal.',
            'status'     => 'pending',
        ]);

        // User mengubah data profil
        $this->actingAs($user)->patch(route('profile.update'), [
            'name'          => 'Nama Baru Sekali',
            'email'         => 'barusekali@example.com',
            'phone'         => '089988776655',
            'nim'           => '20237777',
            'study_program' => 'Sains Data',
            'batch_year'    => '2022',
        ]);

        $application->refresh();

        // Data pada baris pengajuan tetap utuh, tidak tersentuh
        $this->assertSame('Web Development', $application->interest);
        $this->assertSame('Motivasi asli sebelum profil diubah oleh pengguna.', $application->motivation);
        $this->assertSame('Pengalaman awal.', $application->experience);
        $this->assertSame('pending', $application->status);
    }

    /** Validasi nomor WhatsApp harus berformat 08xx atau 628xx. */
    public function test_phone_format_must_be_valid(): void
    {
        $user = $this->createApplicantUser();

        $response = $this->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('profile.update'), [
                'name'          => 'Test Name',
                'email'         => 'test@example.com',
                'phone'         => '021123456', // Bukan 08xx atau 628xx
                'nim'           => '20231001',
                'study_program' => 'Teknik Informatika',
                'batch_year'    => '2023',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('phone');
    }

    /** Tombol keluar tersedia di halaman profil dan berfungsi me-logout pengguna. */
    public function test_logout_button_is_rendered_and_can_log_out(): void
    {
        $user = $this->createApplicantUser();

        $pageResponse = $this->actingAs($user)->get(route('profile.edit'));
        $pageResponse->assertSee('action="' . route('logout') . '"', false);
        $pageResponse->assertSeeText('Keluar');

        $logoutResponse = $this->actingAs($user)->post(route('logout'));
        $logoutResponse->assertStatus(302);
        $logoutResponse->assertRedirect(route('beranda'));

        $this->assertGuest();
    }
}
