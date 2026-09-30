<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DomainSeeder::class,
            SectorSeeder::class,
            SettingSeeder::class,
            MenuSeeder::class,
        ]);

        if (app()->environment('local', 'staging', 'testing') && config('site.seed_demo')) {
            $this->call(DemoSeeder::class);
        }
    }
}
