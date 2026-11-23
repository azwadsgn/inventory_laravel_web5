<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Update akun Admin yang sudah ada (match by email)
        User::updateOrCreate(
            ['email' => 'admin@kios.com'],
            [
                'name'     => 'Admin KSA',
                'username' => 'admin',
                'email'    => 'admin@kios.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Update akun Staff yang sudah ada (match by email)
        User::updateOrCreate(
            ['email' => 'staff@kios.com'],
            [
                'name'     => 'Staff Gudang',
                'username' => 'staff',
                'email'    => 'staff@kios.com',
                'password' => Hash::make('password123'),
                'role'     => 'staff',
            ]
        );
    }
}