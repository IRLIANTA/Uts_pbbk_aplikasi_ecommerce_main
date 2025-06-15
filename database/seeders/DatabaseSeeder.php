<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tambahkan username agar tidak error
        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser', // Tambahkan ini
            'email' => 'test@example.com',
        ]);

        // Jalankan UserSeeder tambahan jika diperlukan
        $this->call(UserSeeder::class);
    }
}
