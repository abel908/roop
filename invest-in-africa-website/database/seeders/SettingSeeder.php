<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Initial settings. Official contact details, social accounts, history,
 * leadership and impact data are "À valider par The Invest In Africa
 * Initiative": they are left empty and filled from the back-office.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'contact' => ['email' => null, 'phone' => null, 'address' => null, 'hours' => null, 'lat' => null, 'lng' => null],
            'socials' => ['linkedin' => null, 'x' => null, 'facebook' => null, 'instagram' => null, 'youtube' => null, 'wechat' => null],
            'hero' => ['video' => null, 'poster' => null],
            // Structural facts taken from the cahier des charges itself — replace with impact data once transmitted.
            'impact' => ['figures' => [
                ['value' => 54, 'suffix' => '', 'label' => ['en' => 'African countries in our scope', 'fr' => 'pays africains dans notre champ d’action', 'zh' => '覆盖的非洲国家']],
                ['value' => 6, 'suffix' => '', 'label' => ['en' => 'areas of intervention', 'fr' => 'domaines d’intervention', 'zh' => '业务领域']],
                ['value' => 2, 'suffix' => '', 'label' => ['en' => 'dedicated pathways: invest or submit a project', 'fr' => 'parcours dédiés : investir ou soumettre un projet', 'zh' => '专属通道：投资或提交项目']],
                ['value' => 3, 'suffix' => '', 'label' => ['en' => 'working languages: English, French, Chinese', 'fr' => 'langues de travail : anglais, français, chinois', 'zh' => '工作语言：英语、法语、中文']],
            ]],
            'about' => ['timeline' => [], 'leaders' => [], 'values' => []],
            'notifications' => [
                'submissions' => [],
                'interests' => [],
                'contact_investment' => [],
                'contact_project' => [],
                'contact_partnership' => [],
                'contact_press' => [],
                'contact_other' => [],
            ],
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
