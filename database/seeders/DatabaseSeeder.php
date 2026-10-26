<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => Role::Admin],
        );

        User::query()->updateOrCreate(
            ['email' => 'superadmin@example.com'],
            ['name' => 'Super admin', 'password' => 'password', 'role' => Role::SuperAdmin],
        );
    }
}
