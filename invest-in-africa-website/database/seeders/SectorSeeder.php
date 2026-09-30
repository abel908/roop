<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

/** Default sectors — the list is administrable in the back-office (§6.3). */
class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            'agriculture' => ['Agriculture & agri-food', 'Agriculture et agroalimentaire', '农业与农产品加工'],
            'energy' => ['Energy & renewables', 'Énergie et renouvelables', '能源与可再生能源'],
            'infrastructure' => ['Infrastructure & construction', 'Infrastructures et BTP', '基础设施与建筑'],
            'manufacturing' => ['Industry & manufacturing', 'Industrie et manufacture', '工业与制造业'],
            'mining' => ['Mining & natural resources', 'Mines et ressources naturelles', '矿业与自然资源'],
            'digital' => ['Digital & technology', 'Numérique et technologies', '数字经济与科技'],
            'health' => ['Health & pharmaceuticals', 'Santé et pharmacie', '医疗与制药'],
            'education' => ['Education & training', 'Éducation et formation', '教育与培训'],
            'finance' => ['Financial services', 'Services financiers', '金融服务'],
            'tourism' => ['Tourism & hospitality', 'Tourisme et hôtellerie', '旅游与酒店'],
            'real-estate' => ['Real estate & housing', 'Immobilier et logement', '房地产与住房'],
            'transport' => ['Transport & logistics', 'Transport et logistique', '交通与物流'],
            'water' => ['Water & sanitation', 'Eau et assainissement', '水务与环境卫生'],
            'other' => ['Other', 'Autre', '其他'],
        ];

        $sort = 0;

        foreach ($sectors as $code => [$en, $fr, $zh]) {
            Sector::query()->updateOrCreate(['code' => $code], [
                'name' => compact('en', 'fr', 'zh'),
                'sort' => $sort += 10,
                'is_active' => true,
            ]);
        }
    }
}
