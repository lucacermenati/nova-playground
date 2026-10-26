<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        for ($i = 0; $i < 200; $i++) {
            $createdAt = now()->subDays(random_int(0, 180))->subMinutes(random_int(0, 1440));

            User::query()->forceCreate([
                'name' => $faker->name(),
                'email' => Str::lower(Str::random(6)).'@'.$faker->safeEmailDomain(),
                'email_verified_at' => random_int(0, 3) === 0 ? null : $createdAt->copy()->addHours(random_int(1, 48)),
                'password' => bcrypt('password'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
