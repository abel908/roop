<?php

namespace Database\Seeders;

use App\Enums\FundingType;
use App\Enums\ProjectStage;
use App\Enums\ProjectStatus;
use App\Models\Domain;
use App\Models\Project;
use App\Models\Sector;
use Illuminate\Database\Seeder;

/**
 * Reproducible DEMONSTRATION data for local and pre-production environments
 * only (§9.3). These projects are fictitious: they illustrate the catalogue,
 * filters and project sheets until real opportunities are published.
 * Never run in production.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $sector = fn (string $code) => Sector::query()->where('code', $code)->value('id');
        $domain = fn (int $number) => Domain::query()->where('number', $number)->value('id');

        $projects = [
            [
                'reference' => 'P-2026-001',
                'country' => 'SN', 'sector' => 'energy', 'domain' => 2,
                'stage' => ProjectStage::Growth, 'amount' => 4_500_000, 'currency' => 'EUR', 'funding' => FundingType::Mixed,
                'status' => ProjectStatus::Open, 'featured' => true, 'jobs' => 120,
                'title' => ['en' => '[Demo] Solar mini-grids for rural communities', 'fr' => '[Démo] Mini-réseaux solaires pour communautés rurales', 'zh' => '[示例] 农村社区太阳能微电网'],
                'summary' => [
                    'en' => 'Deployment of 40 solar mini-grids providing reliable electricity to rural villages and small businesses.',
                    'fr' => 'Déploiement de 40 mini-réseaux solaires fournissant une électricité fiable aux villages ruraux et aux petites entreprises.',
                    'zh' => '建设40个太阳能微电网，为农村村庄和小型企业提供稳定电力。',
                ],
            ],
            [
                'reference' => 'P-2026-002',
                'country' => 'CI', 'sector' => 'agriculture', 'domain' => 4,
                'stage' => ProjectStage::EarlyRevenue, 'amount' => 1_800_000, 'currency' => 'USD', 'funding' => FundingType::Equity,
                'status' => ProjectStatus::Open, 'featured' => true, 'jobs' => 85,
                'title' => ['en' => '[Demo] Cashew processing unit', 'fr' => '[Démo] Unité de transformation de noix de cajou', 'zh' => '[示例] 腰果加工厂'],
                'summary' => [
                    'en' => 'Industrial unit processing raw cashew nuts locally to capture added value and create skilled jobs.',
                    'fr' => 'Unité industrielle de transformation locale de la noix de cajou brute pour capter la valeur ajoutée et créer des emplois qualifiés.',
                    'zh' => '在当地加工生腰果的工业化工厂，提升附加值并创造技术型就业岗位。',
                ],
            ],
            [
                'reference' => 'P-2026-003',
                'country' => 'KE', 'sector' => 'digital', 'domain' => 3,
                'stage' => ProjectStage::Prototype, 'amount' => 650_000, 'currency' => 'USD', 'funding' => FundingType::Equity,
                'status' => ProjectStatus::Funding, 'featured' => true, 'jobs' => 25,
                'title' => ['en' => '[Demo] Mobile platform for SME invoicing', 'fr' => '[Démo] Plateforme mobile de facturation pour PME', 'zh' => '[示例] 中小企业移动开票平台'],
                'summary' => [
                    'en' => 'A mobile-first application helping small businesses issue invoices, track payments and access working capital.',
                    'fr' => 'Une application mobile qui aide les petites entreprises à émettre des factures, suivre leurs paiements et accéder à du fonds de roulement.',
                    'zh' => '一款移动优先的应用，帮助小企业开具发票、跟踪回款并获得流动资金。',
                ],
            ],
            [
                'reference' => 'P-2026-004',
                'country' => 'MA', 'sector' => 'water', 'domain' => 2,
                'stage' => ProjectStage::Expansion, 'amount' => 22_000_000, 'currency' => 'EUR', 'funding' => FundingType::Debt,
                'status' => ProjectStatus::Open, 'featured' => false, 'jobs' => 210,
                'title' => ['en' => '[Demo] Desalination plant for irrigation', 'fr' => '[Démo] Station de dessalement pour l’irrigation', 'zh' => '[示例] 农业灌溉海水淡化厂'],
                'summary' => [
                    'en' => 'Expansion of a desalination plant supplying water to farmers in a water-stressed agricultural region.',
                    'fr' => 'Extension d’une station de dessalement alimentant en eau les agriculteurs d’une région agricole en stress hydrique.',
                    'zh' => '扩建海水淡化厂，为缺水农业地区的农户供水。',
                ],
            ],
            [
                'reference' => 'P-2026-005',
                'country' => 'GH', 'sector' => 'health', 'domain' => 2,
                'stage' => ProjectStage::Growth, 'amount' => 3_200_000, 'currency' => 'USD', 'funding' => FundingType::Mixed,
                'status' => ProjectStatus::Open, 'featured' => true, 'jobs' => 60,
                'title' => ['en' => '[Demo] Regional diagnostic laboratory network', 'fr' => '[Démo] Réseau régional de laboratoires d’analyses', 'zh' => '[示例] 区域医学检验实验室网络'],
                'summary' => [
                    'en' => 'Creation of four diagnostic laboratories to bring quality medical testing closer to secondary cities.',
                    'fr' => 'Création de quatre laboratoires d’analyses pour rapprocher des examens médicaux de qualité des villes secondaires.',
                    'zh' => '新建四家医学检验实验室，让二线城市的居民就近获得高质量检测服务。',
                ],
            ],
            [
                'reference' => 'P-2026-006',
                'country' => 'RW', 'sector' => 'tourism', 'domain' => 1,
                'stage' => ProjectStage::Idea, 'amount' => 900_000, 'currency' => 'USD', 'funding' => FundingType::Equity,
                'status' => ProjectStatus::Open, 'featured' => false, 'jobs' => 40,
                'title' => ['en' => '[Demo] Eco-lodge and community tourism', 'fr' => '[Démo] Éco-lodge et tourisme communautaire', 'zh' => '[示例] 生态旅舍与社区旅游'],
                'summary' => [
                    'en' => 'An eco-lodge designed with local communities, combining conservation, training and sustainable tourism.',
                    'fr' => 'Un éco-lodge conçu avec les communautés locales, alliant conservation, formation et tourisme durable.',
                    'zh' => '与当地社区共同打造的生态旅舍，融合自然保护、技能培训与可持续旅游。',
                ],
            ],
        ];

        foreach ($projects as $data) {
            Project::query()->updateOrCreate(['reference' => $data['reference']], [
                'title' => $data['title'],
                'summary' => $data['summary'],
                'description' => [
                    'en' => '<p>This is a <strong>demonstration project</strong> used to illustrate the catalogue before real opportunities are published. Market, business model, team and use of funds will be described here.</p>',
                    'fr' => '<p>Ceci est un <strong>projet de démonstration</strong> destiné à illustrer le catalogue avant la publication des opportunités réelles. Le marché, le modèle économique, l’équipe et l’usage des fonds seront décrits ici.</p>',
                    'zh' => '<p>这是一个<strong>示例项目</strong>，用于在正式项目发布前展示项目目录。此处将介绍市场、商业模式、团队及资金用途。</p>',
                ],
                'use_of_funds' => [
                    'en' => ['Equipment and installation — 60%', 'Working capital — 25%', 'Training and technical assistance — 15%'],
                    'fr' => ['Équipements et installation — 60 %', 'Fonds de roulement — 25 %', 'Formation et assistance technique — 15 %'],
                    'zh' => ['设备与安装 — 60%', '流动资金 — 25%', '培训与技术支持 — 15%'],
                ],
                'impact' => [
                    'en' => "Around {$data['jobs']} direct jobs, local supply chains and ESG reporting.",
                    'fr' => "Environ {$data['jobs']} emplois directs, filières d’approvisionnement locales et reporting ESG.",
                    'zh' => "约创造{$data['jobs']}个直接就业岗位，带动本地供应链，并进行ESG报告。",
                ],
                'timeline' => [
                    'en' => 'Financial close within 6 months, operations within 18 months.',
                    'fr' => 'Bouclage financier sous 6 mois, mise en service sous 18 mois.',
                    'zh' => '6个月内完成融资，18个月内投入运营。',
                ],
                'country' => $data['country'],
                'sector_id' => $sector($data['sector']),
                'domain_id' => $domain($data['domain']),
                'stage' => $data['stage'],
                'investment_amount' => $data['amount'],
                'currency' => $data['currency'],
                'funding_type' => $data['funding'],
                'status' => $data['status'],
                'jobs_expected' => $data['jobs'],
                'is_featured' => $data['featured'],
                'is_published' => true,
                'published_at' => now()->subDays((int) substr($data['reference'], -1)),
            ]);
        }
    }
}
