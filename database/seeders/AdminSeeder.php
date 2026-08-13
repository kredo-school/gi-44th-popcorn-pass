<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    protected $authPasswordName = 'password_hash';
    
    public function run(): void
    {
        
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'id'            => Str::uuid(),
                'username'      => 'admin',
                'password_hash' => Hash::make('password123'),
                'role'          => 2,
            ]
        );
    }
}