<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sav.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin12345'),
                'role' => 'Admin'
            ]
        );

        User::updateOrCreate(
            ['email' => 'tech@sav.com'],
            [
                'name' => 'Technicien',
                'password' => Hash::make('tech12345'),
                'role' => 'Technicien'
            ]
        );

        User::updateOrCreate(
            ['email' => 'agent@sav.com'],
            [
                'name' => 'Agent SAV',
                'password' => Hash::make('agent12345'),
                'role' => 'Agent'
            ]
        );
    }
}