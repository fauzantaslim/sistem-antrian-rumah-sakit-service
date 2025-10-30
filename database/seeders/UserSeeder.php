<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first counter for assignment
        $counter = Counter::first();

        $users = [
            [
                'user_id' => (string) Str::ulid(),
                'google_id' => null,
                'full_name' => 'Fauzan Taslim',
                'email' => 'fauzantaslim123@gmail.com',
                'email_verified_at' => now(),
                'role' => 'admin',
                'counter_id' => $counter?->counter_id,
            ],
            [
                'user_id' => (string) Str::ulid(),
                'google_id' => null,
                'full_name' => 'Admin System',
                'email' => 'admin@sistem-antrian.com',
                'email_verified_at' => now(),
                'role' => 'admin',
                'counter_id' => null,
            ],
            [
                'user_id' => (string) Str::ulid(),
                'google_id' => null,
                'full_name' => 'Petugas Loket 1',
                'email' => 'petugas1@sistem-antrian.com',
                'email_verified_at' => now(),
                'role' => 'petugas',
                'counter_id' => $counter?->counter_id,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
