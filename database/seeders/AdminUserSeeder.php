<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@wowzmartialart.test')],
            [
                'name' => env('ADMIN_NAME', 'Academy Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => UserRole::Admin,
                'status' => AccountStatus::Active,
                'email_verified_at' => now(),
            ]
        );
    }
}
