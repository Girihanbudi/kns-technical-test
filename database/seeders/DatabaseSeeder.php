<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ROOT_ADMIN_EMAIL', 'root@example.com');
        $adminPassword = env('ROOT_ADMIN_PASSWORD', 'changeme');
        $adminName = env('ROOT_ADMIN_NAME', 'Root Admin');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => $adminPassword,
                'role' => UserRole::Administrator->value,
                'active' => true,
            ]
        );
    }
}
