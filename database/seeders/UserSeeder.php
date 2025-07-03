<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pengguna Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin', 
                'status' => true, 
            ]
        );

        // Pengguna Manager
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'manager', 
                'status' => true,
            ]
        );

        // Pengguna Staff
        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'role' => 'staff', 
                'status' => true, 
            ]
        );

        // Pengguna Staff 2
        User::updateOrCreate(
            ['email' => 'staff2@example.com'],
            [
                'name' => 'Staff User 2',
                'password' => Hash::make('password'),
                'role' => 'staff', 
                'status' => true, 
            ]
        );
    }
}
