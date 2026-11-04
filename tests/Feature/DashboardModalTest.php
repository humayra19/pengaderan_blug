<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * US-11 — Membaca detail pengajuan
 *
 * Modal diisi dari atribut data-* tombol "Lihat". Test ini memverifikasi
 * bahwa seluruh data yang diperlukan tersedia di atribut tersebut dan
 * dirender dengan benar di HTML halaman dashboard.
 */
class DashboardModalTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ helpers

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * Buat aplikasi lengkap: user dengan semua field terisi.
     */
    private function makeFullApp(string $status = 'pending', string $adminNote = ''): MembershipApplication
    {
        $user = User::factory()->create([
            'name'          => 'Budi Santoso',
            'email'         => 'budi@example.com',
            'nim'           => '20231001',
            'study_program' => 'Teknik Informatika',
            'batch_year'    => '2023',
            'phone'         => '081234567890',
            'role'          => 'user',
        ]);

        return MembershipApplication::factory()
            ->for($user)
            ->$status()
            ->create([
                'interest'   => 'Web Development',
                'motivation' => "Saya ingin belajar bersama.\nBaris kedua motivasi.",
                'experience' => 'Pernah ikut workshop Laravel.',
                'admin_note' => $adminNote,
            ]);
    }

    // ------------------------------------------------------------------ tests

    /** Tombol "Lihat" muncul di baris pengajuan (tidak lagi disabled). */
    public function test_lihat_button_is_rendered_for_each_row(): void
    {
        $this->makeFullApp('pending');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Lihat');
        // Tombol tidak disabled
        $response->assertDontSee('disabled');
    }

    /** Semua atribut data-* wajib ada di tombol "Lihat". */
    public function test_lihat_button_has_all_data_attributes(): void
    {
        $app = $this->makeFullApp('pending');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));

        $html = $response->getContent();

        // Atribut-atribut yang dibutuhkan JS
        $this->assertStringContainsString('data-name="Budi Santoso"',          $html);
        $this->assertStringContainsString('data-email="budi@example.com"',     $html);
        $this->assertStringContainsString('data-nim="20231001"',               $html);
        $this->assertStringContainsString('data-study-program="Teknik Informatika"', $html);
        $this->assertStringContainsString('data-batch="2023"',                 $html);
        $this->assertStringContainsString('data-whatsapp="081234567890"',      $html);
        $this->assertStringContainsString('data-interest="Web Development"',   $html);
        $this->assertStringContainsString('data-status="pending"',             $html);
        $this->assertStringContainsString('data-application-id="' . $app->id . '"', $html);
        $this->assertStringContainsString('data-bs-toggle="modal"',            $html);
        $this->assertStringContainsString('data-bs-target="#applicationModal"', $html);
    }

    /** Motivasi (teks multi-baris) ada di atribut data-motivation. */
    public function test_motivation_with_newline_is_in_data_attribute(): void
    {
        $this->makeFullApp('pending');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        // Blade meng-escape \n dalam atribut — cari minimal kata kunci
        $this->assertStringContainsString('Saya ingin belajar bersama.', $html);
    }

    /** Pengalaman ada di atribut data-experience. */
    public function test_experience_is_in_data_attribute(): void
    {
        $this->makeFullApp('pending');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        $this->assertStringContainsString('Pernah ikut workshop Laravel.', $html);
    }

    /** Catatan admin ada di atribut bila tidak kosong. */
    public function test_admin_note_in_data_attribute_when_present(): void
    {
        $this->makeFullApp('approved', 'Selamat bergabung di BLUG!');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        $this->assertStringContainsString('Selamat bergabung di BLUG!', $html);
        $this->assertStringContainsString('data-admin-note=', $html);
    }

    /** data-admin-note kosong (string kosong) bila catatan tidak ada. */
    public function test_admin_note_empty_in_data_attribute_when_absent(): void
    {
        $this->makeFullApp('pending', '');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        $this->assertStringContainsString('data-admin-note=""', $html);
    }

    /** Elemen modal #applicationModal ada di halaman. */
    public function test_modal_element_exists_in_page(): void
    {
        $this->makeFullApp('pending');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="applicationModal"', false);
        $response->assertSee('data-modal="name"', false);
        $response->assertSee('data-modal="email"', false);
        $response->assertSee('data-modal="nim"', false);
        $response->assertSee('data-modal="study-program"', false);
        $response->assertSee('data-modal="batch"', false);
        $response->assertSee('data-modal="whatsapp"', false);
        $response->assertSee('data-modal="interest"', false);
        $response->assertSee('data-modal="motivation"', false);
        $response->assertSee('data-modal="experience"', false);
        $response->assertSee('data-modal="created-at"', false);
        $response->assertSee('data-modal="status"', false);
        $response->assertSee('data-modal="admin-note"', false);
        $response->assertSee('data-modal-row="admin-note"', false);
    }

    /** Tombol "Tutup" ada di modal footer. */
    public function test_modal_has_close_button(): void
    {
        $this->makeFullApp('pending');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));

        $response->assertSee('Tutup');
        $response->assertSee('data-bs-dismiss="modal"', false);
    }

    /** Status approved menghasilkan data-status="approved" di tombol. */
    public function test_approved_application_has_correct_status_attribute(): void
    {
        $this->makeFullApp('approved', 'Diterima!');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        $this->assertStringContainsString('data-status="approved"', $html);
    }

    /** Status rejected menghasilkan data-status="rejected" di tombol. */
    public function test_rejected_application_has_correct_status_attribute(): void
    {
        $this->makeFullApp('rejected', 'Kurang pengalaman.');

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        $this->assertStringContainsString('data-status="rejected"', $html);
    }

    /** Karakter khusus di nilai di-escape oleh Blade (tidak rawan XSS). */
    public function test_special_characters_are_blade_escaped_in_data_attributes(): void
    {
        $user = User::factory()->create([
            'name'  => 'Budi "XSS" <script>',
            'email' => 'xss@example.com',
            'role'  => 'user',
        ]);

        MembershipApplication::factory()
            ->for($user)
            ->pending()
            ->create([
                'interest'   => 'Security',
                'motivation' => 'Test',
                'experience' => '',
            ]);

        $response = $this->actingAs($this->admin())->get(route('dashboard'));
        $html     = $response->getContent();

        // Blade harus meng-escape < dan " agar tidak ada tag mentah di atribut
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
