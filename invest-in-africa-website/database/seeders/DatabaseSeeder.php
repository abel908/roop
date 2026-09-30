<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DomainSeeder::class,
            SectorSeeder::class,
            SettingSeeder::class,
        ]);

        // First Super Admin — credentials from the environment, never hard-coded.
        $email = env('ADMIN_EMAIL', 'admin@example.com');

        if (! User::query()->where('email', $email)->exists()) {
            $password = env('ADMIN_PASSWORD') ?: Str::password(20);

            User::query()->create([
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'email' => $email,
                'password' => $password,
                'role' => Role::SuperAdmin,
                'locale' => 'fr',
            ]);

            $this->command?->warn("Super Admin created: $email".(env('ADMIN_PASSWORD') ? '' : " / password: $password"));
        }

        if (app()->environment('local', 'staging', 'testing') && env('SEED_DEMO', true)) {
            $this->call(DemoSeeder::class);
        }
    }
}
