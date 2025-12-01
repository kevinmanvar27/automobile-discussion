<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'madhuram.motors@gmail.com',
            'password' => Hash::make('madhuram@motors'),
            'shop_name' => 'Admin Panel',
            'mobile_no' => '0000000000',
            'city' => 'Admin City',
            'address' => 'Admin Address',
            'verified' => true, // Admin is automatically verified
        ]);
    }
}