<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Database\Seeders\TherapistNameSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );

        $this->call(TherapistNameSeeder::class);
    }
}
