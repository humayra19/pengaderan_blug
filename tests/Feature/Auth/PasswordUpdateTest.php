<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * US-14 — Mengganti Password
 *
 * Menguji penggantian password melalui kartu Ganti Password pada halaman profil:
 * - Sukses memperbarui password (tersimpan dalam bentuk hash, pengguna tetap login, pesan sukses muncul)
 * - Password lama salah ditolak
 * - Konfirmasi password tidak cocok ditolak
 * - Password baru kurang dari 8 karakter ditolak
 * - Galat berada di error bag updatePassword dan tidak bercampur dengan form data diri
 */
class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** Kartu Ganti Password dirender di halaman profil dengan field-field yang tepat. */
    public function test_password_card_is_rendered_on_profile_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSeeText('Ganti password');
        $response->assertSee('name="current_password"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
        $response->assertSeeText('Perbarui Password');
    }

    /** Sukses memperbarui password: password tersimpan dalam bentuk hash, tetap login, dan ada pesan status. */
    public function test_password_can_be_updated_successfully(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password-lama'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password'      => 'password-lama',
                'password'              => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'password-updated');

        // Pengguna tetap login
        $this->assertAuthenticatedAs($user);

        // Password tersimpan dalam bentuk hash yang valid
        $user->refresh();
        $this->assertTrue(Hash::check('password-baru-123', $user->password));
        $this->assertFalse(Hash::check('password-lama', $user->password));

        // Pesan sukses tampak di halaman setelah redirect
        $pageResponse = $this->actingAs($user)->get(route('profile.edit'));
        $pageResponse->assertSeeText('Password berhasil diperbarui.');
    }

    /** Password lama salah harus ditolak oleh validasi (rule current_password). */
    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password-asli'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password'      => 'password-salah',
                'password'              => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect(route('profile.edit'));

        // Password tidak berubah
        $user->refresh();
        $this->assertTrue(Hash::check('password-asli', $user->password));
    }

    /** Konfirmasi password baru tidak cocok harus ditolak oleh validasi. */
    public function test_password_confirmation_must_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password-lama'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password'      => 'password-lama',
                'password'              => 'password-baru-123',
                'password_confirmation' => 'tidak-cocok-456',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'password')
            ->assertRedirect(route('profile.edit'));

        // Password tidak berubah
        $user->refresh();
        $this->assertTrue(Hash::check('password-lama', $user->password));
    }

    /** Password baru kurang dari 8 karakter harus ditolak oleh validasi. */
    public function test_new_password_must_be_at_least_eight_characters(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password-lama'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->put(route('password.update'), [
                'current_password'      => 'password-lama',
                'password'              => '1234567', // 7 karakter
                'password_confirmation' => '1234567',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'password')
            ->assertRedirect(route('profile.edit'));

        // Password tidak berubah
        $user->refresh();
        $this->assertTrue(Hash::check('password-lama', $user->password));
    }
}
