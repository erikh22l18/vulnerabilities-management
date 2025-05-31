<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        User::factory()->create([
            'name' => 'Leader User',
            'email' => 'leader@example.com',
            'password' => Hash::make('password123'),
        ]);

        User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
