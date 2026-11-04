<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Jalankan seeder akun admin.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@blug.test');
        $name = env('ADMIN_NAME', 'Administrator BLUG');
        $password = env('ADMIN_PASSWORD', 'password');

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = Hash::make($password);
        $user->role = 'admin';
        $user->save();
    }
}

