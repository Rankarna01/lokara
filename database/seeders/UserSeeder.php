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

        // 5. Akun Tenant Owner Demo Salon (Putri Beauty Care)
        User::updateOrCreate(
            ['email' => 'hapis@putribeautycare.site'],
            [
                'name' => 'Hapis Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081377889900',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 6. Akun Kasir POS Demo Salon (Putri Beauty Care)
        User::updateOrCreate(
            ['email' => 'dina@putribeauty.com'],
            [
                'name' => 'Dina Safitri - Kasir Salon',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567802',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 7. Akun Tenant Owner Demo Salon (Studio Rona)
        User::updateOrCreate(
            ['email' => 'contact@studiorona.id'],
            [
                'name' => 'Rona Melinda',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081377889911',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 8. Akun Tenant Owner Demo Barbershop (Gentleman Barbershop)
        User::updateOrCreate(
            ['email' => 'bambang@gentlemanbarber.id'],
            [
                'name' => 'Bambang Barber',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'phone' => '081298765411',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 9. Akun Kapster / Kasir Barbershop (Doni Barber)
        User::updateOrCreate(
            ['email' => 'doni.barber@studiorona.id'],
            [
                'name' => 'Doni Barber (Senior Kapster)',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081377889922',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

