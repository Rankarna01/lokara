<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin Utama (Platform Owner)
        User::updateOrCreate(
            ['email' => 'admin@lokarapos.id'],
            [
                'name' => 'Admin Lokara',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Super Admin Support & Operasional
        User::updateOrCreate(
            ['email' => 'support@lokarapos.id'],
            [
                'name' => 'Support Lokara',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'phone' => '081298765432',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Akun Tenant Owner Demo (Kopi Ruang Pagi)
        User::updateOrCreate(
            ['email' => 'rian@kopiruangpagi.id'],
            [
                'name' => 'Rian Adiputra',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. Akun Tenant Owner Demo (Toko Berkah Jaya)
        User::updateOrCreate(
            ['email' => 'somad@berkahjaya.com'],
            [
                'name' => 'H. Somad',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081298765432',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

