<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_admin_user_properly(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@blug.test'))->first();

        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check(env('ADMIN_PASSWORD', 'password'), $admin->password));
    }

    public function test_admin_seeder_is_idempotent(): void
    {
        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $count = User::where('email', env('ADMIN_EMAIL', 'admin@blug.test'))->count();
        $this->assertSame(1, $count);
    }
}

