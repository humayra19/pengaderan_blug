<?php

namespace Database\Factories;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MembershipApplication>
 */
class MembershipApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status   = fake()->randomElement(['pending', 'approved', 'rejected']);
        $reviewed = in_array($status, ['approved', 'rejected']);

        return [
            // user_id harus di-set oleh pemanggil atau via for()
            'interest'    => fake()->randomElement(config('blug.interests', [
                '[Bidang 1]', '[Bidang 2]', '[Bidang 3]', '[Bidang 4]',
            ])),
            'motivation'  => fake()->paragraphs(2, true),
            'experience'  => fake()->optional(0.7)->paragraph(),
            'status'      => $status,
            'admin_note'  => $reviewed ? fake()->optional(0.6)->sentence() : null,
            'reviewed_by' => null,   // di-set oleh seeder bila perlu
            'reviewed_at' => $reviewed ? fake()->dateTimeBetween('-3 months', 'now') : null,
        ];
    }

    // ------------------------------------------------------------------ //
    // State helpers
    // ------------------------------------------------------------------ //

    public function pending(): static
    {
        return $this->state([
            'status'      => 'pending',
            'admin_note'  => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state([
            'status'      => 'approved',
            'admin_note'  => fake()->optional(0.5)->sentence(),
            'reviewed_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'      => 'rejected',
            'admin_note'  => fake()->sentence(),   // wajib saat rejected (PRD bagian 9)
            'reviewed_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }
}

