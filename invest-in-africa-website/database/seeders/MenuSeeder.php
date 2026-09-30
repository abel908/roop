<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

/** Default navigation, identical to the reference structure (§3.2, §3.3). */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        if (MenuItem::query()->exists()) {
            return;
        }

        $header = [
            ['route', 'about'], ['route', 'mission'], ['mega', 'domains'],
            ['mega', 'involved'], ['route', 'partners'], ['route', 'contact'],
        ];
        $footer = [['route', 'about'], ['route', 'mission'], ['route', 'partners'], ['route', 'contact']];

        foreach (['header' => $header, 'footer' => $footer] as $location => $items) {
            foreach ($items as $i => [$type, $target]) {
                MenuItem::query()->create(['location' => $location, 'type' => $type, 'target' => $target, 'sort' => ($i + 1) * 10]);
            }
        }
    }
}
