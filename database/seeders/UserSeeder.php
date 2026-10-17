<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Admin KampusPay', 'admin@kampuspay.test', User::ROLE_ADMIN],
            ['Customer Demo', 'customer@kampuspay.test', User::ROLE_CUSTOMER],
            ['Merchant Demo', 'merchant@kampuspay.test', User::ROLE_MERCHANT],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => 'password', 'role' => $role, 'is_active' => true],
            );
        }
    }
}