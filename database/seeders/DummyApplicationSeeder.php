<?php

namespace Database\Seeders;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder data dummy untuk pengembangan dan pengujian tampilan.
 *
 * TIDAK dipanggil dari DatabaseSeeder.
 * Jalankan manual:  php artisan db:seed --class=DummyApplicationSeeder
 *
 * Membuat 25 pengajuan dengan status campuran beserta user dummy (role: user).
 * Aman dijalankan berulang — user dummy dibuat dengan firstOrCreate berdasarkan email.
 */
class DummyApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil atau buat akun admin untuk mengisi reviewed_by
        $admin = User::where('role', 'admin')->first()
            ?? User::factory()->create(['role' => 'admin', 'email' => 'admin@blug.test']);

        // Distribusi status: 12 pending, 8 approved, 5 rejected  = 25 total
        $distributions = [
            ['status' => 'pending',  'count' => 12],
            ['status' => 'approved', 'count' => 8],
            ['status' => 'rejected', 'count' => 5],
        ];

        foreach ($distributions as ['status' => $status, 'count' => $count]) {
            for ($i = 0; $i < $count; $i++) {
                // Buat user baru dengan profil lengkap
                $user = User::factory()->create([
                    'role'          => 'user',
                    'nim'           => fake()->unique()->numerify('##########'),
                    'study_program' => fake()->randomElement([
                        'Teknik Informatika',
                        'Sistem Informasi',
                        'Teknik Komputer',
                        'Ilmu Komputer',
                        'Matematika',
                    ]),
                    'batch_year'    => fake()->numberBetween(date('Y') - 6, date('Y')),
                    'phone'         => '08' . fake()->numerify('##########'),
                ]);

                // Buat pengajuan sesuai status
                $app = MembershipApplication::factory()
                    ->{$status}()
                    ->for($user)
                    ->create();

                // Isi reviewed_by untuk yang sudah diputuskan
                if (in_array($status, ['approved', 'rejected'])) {
                    $app->update(['reviewed_by' => $admin->id]);
                }
            }
        }

        $this->command->info('DummyApplicationSeeder: 25 pengajuan dummy berhasil dibuat.');
        $this->command->table(
            ['Status', 'Jumlah'],
            collect($distributions)->map(fn ($d) => [$d['status'], $d['count']])->toArray()
        );
    }
}

