<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Seeder;

/**
 * The six official areas of intervention (cahier des charges §3.1, §5.4).
 * English titles are kept exactly as transmitted by the institution.
 * French and Chinese titles, slugs and all descriptive texts are editorial
 * proposals to be validated during the translation phase.
 */
class DomainSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->domains() as $domain) {
            Domain::query()->updateOrCreate(['number' => $domain['number']], $domain);
        }
    }

    private function domains(): array
    {
        return [
            [
                'number' => 1,
                'icon' => 'fdi',
                'slug' => ['en' => 'fdi-promotion-orientation-support', 'fr' => 'promotion-orientation-accompagnement-ide', 'zh' => 'fdi-promotion-orientation-support'],
                'title' => [
                    'en' => 'FDI Promotion, Orientation, and Support',
                    'fr' => 'Promotion, orientation et accompagnement des IDE',
                    'zh' => '外国直接投资促进、引导与支持',
                ],
                'tagline' => [
                    'en' => 'Attracting foreign direct investment and guiding investors at every step of their establishment in Africa.',
                    'fr' => 'Attirer les investissements directs étrangers et guider les investisseurs à chaque étape de leur implantation en Afrique.',
                    'zh' => '吸引外国直接投资，并在投资者落地非洲的每一个阶段提供指导。',
                ],
                'audience' => ['en' => 'Foreign investors', 'fr' => 'Investisseurs étrangers', 'zh' => '外国投资者'],
                'intro' => [
                    'en' => 'Investing in a new market requires reliable information, the right contacts and a clear understanding of local frameworks. We promote Africa as an investment destination and accompany foreign investors from their first questions to the effective launch of their operations.',
                    'fr' => 'Investir sur un nouveau marché exige une information fiable, les bons interlocuteurs et une compréhension claire des cadres locaux. Nous promouvons l’Afrique comme destination d’investissement et accompagnons les investisseurs étrangers, de leurs premières questions jusqu’au démarrage effectif de leurs activités.',
                    'zh' => '进入新市场投资，需要可靠的信息、合适的对接方以及对当地规则的清晰理解。我们推介非洲作为投资目的地，陪伴外国投资者从最初的咨询直至业务正式落地。',
                ],
                'challenges' => [
                    'en' => 'Perceived risk, fragmented information and complex procedures still slow down investment flows towards the continent. Clear orientation and trusted local relays make the difference.',
                    'fr' => 'Le risque perçu, l’éparpillement de l’information et la complexité des procédures freinent encore les flux d’investissement vers le continent. Une orientation claire et des relais locaux de confiance font la différence.',
                    'zh' => '风险认知、信息分散和程序复杂仍在制约流向非洲大陆的投资。清晰的引导和值得信赖的本地对接渠道至关重要。',
                ],
                'services' => [
                    'en' => ['Promotion of investment opportunities by country and sector', 'Orientation towards the relevant institutions and procedures', 'Introductions to local partners and service providers', 'Support throughout establishment and after-care'],
                    'fr' => ['Promotion des opportunités d’investissement par pays et par secteur', 'Orientation vers les institutions et procédures compétentes', 'Mise en relation avec des partenaires et prestataires locaux', 'Accompagnement de l’implantation et suivi après installation'],
                    'zh' => ['按国家和行业推介投资机会', '引导对接相关机构与办理程序', '对接本地合作伙伴与服务机构', '全程陪伴落地并提供后续服务'],
                ],
                'benefits' => [
                    'en' => ['A single point of entry', 'Qualified, verified contacts', 'Time saved on market entry'],
                    'fr' => ['Un point d’entrée unique', 'Des interlocuteurs qualifiés et vérifiés', 'Un gain de temps pour entrer sur le marché'],
                    'zh' => ['统一的对接窗口', '经过核实的专业对接方', '缩短市场进入时间'],
                ],
                'steps' => [
                    'en' => ['Needs assessment', 'Market orientation', 'Introductions', 'Establishment support'],
                    'fr' => ['Analyse du besoin', 'Orientation marché', 'Mises en relation', 'Accompagnement à l’implantation'],
                    'zh' => ['需求评估', '市场引导', '对接引荐', '落地支持'],
                ],
            ],
            [
                'number' => 2,
                'icon' => 'financing',
                'slug' => ['en' => 'african-projects-investment-financing', 'fr' => 'investissement-financement-projets-africains', 'zh' => 'african-projects-investment-financing'],
                'title' => [
                    'en' => 'African Projects Investment & Financing',
                    'fr' => 'Investissement et financement de projets africains',
                    'zh' => '非洲项目投资与融资',
                ],
                'tagline' => [
                    'en' => 'Bringing African projects and investors together, from qualification of the file to the financing round.',
                    'fr' => 'Rapprocher projets africains et investisseurs, de la qualification du dossier jusqu’au tour de financement.',
                    'zh' => '连接非洲项目与投资者，从项目资料审核直至完成融资。',
                ],
                'audience' => ['en' => 'Investors and project holders', 'fr' => 'Investisseurs et porteurs de projets', 'zh' => '投资者与项目方'],
                'intro' => [
                    'en' => 'Promising projects exist in every African country. What they often lack is visibility, structure and access to the right investors. We identify, qualify and present projects to a network of private and institutional investors.',
                    'fr' => 'Des projets prometteurs existent dans chaque pays africain. Il leur manque souvent la visibilité, la structuration et l’accès aux bons investisseurs. Nous identifions, qualifions et présentons les projets à un réseau d’investisseurs privés et institutionnels.',
                    'zh' => '非洲每个国家都不乏有潜力的项目，它们往往缺少的是曝光度、规范的结构以及接触合适投资者的渠道。我们发掘、评估项目，并将其推介给私人及机构投资者网络。',
                ],
                'challenges' => [
                    'en' => 'The financing gap for African projects remains significant. Well-structured files, transparent information and trusted intermediation increase the chances of closing.',
                    'fr' => 'Le déficit de financement des projets africains reste important. Des dossiers bien structurés, une information transparente et une intermédiation de confiance augmentent les chances d’aboutir.',
                    'zh' => '非洲项目的融资缺口依然巨大。结构完善的项目资料、透明的信息和可信赖的中介服务，能够显著提高融资成功率。',
                ],
                'services' => [
                    'en' => ['Reception and qualification of project files', 'Structuring support: business plan, financial model, use of funds', 'Publication of opportunities to investors', 'Organisation of meetings and follow-up of discussions'],
                    'fr' => ['Réception et qualification des dossiers de projets', 'Aide à la structuration : plan d’affaires, modèle financier, usage des fonds', 'Publication des opportunités auprès des investisseurs', 'Organisation des rencontres et suivi des échanges'],
                    'zh' => ['接收并评估项目资料', '协助完善项目结构：商业计划、财务模型、资金用途', '向投资者发布投资机会', '组织洽谈并跟进沟通'],
                ],
                'benefits' => [
                    'en' => ['Qualified deal flow for investors', 'Visibility for serious projects', 'A dedicated project officer'],
                    'fr' => ['Un flux d’opportunités qualifiées pour les investisseurs', 'De la visibilité pour les projets sérieux', 'Un chargé de projets dédié'],
                    'zh' => ['为投资者提供经过筛选的项目来源', '为优质项目提升曝光', '专属项目经理全程跟进'],
                ],
                'steps' => [
                    'en' => ['Project submission', 'Review and qualification', 'Publication', 'Investor matching'],
                    'fr' => ['Soumission du projet', 'Examen et qualification', 'Publication', 'Mise en relation'],
                    'zh' => ['提交项目', '审核评估', '发布推介', '投资对接'],
                ],
            ],
            [
                'number' => 3,
                'icon' => 'talents',
                'slug' => ['en' => 'local-talents-sme-coaching-training', 'fr' => 'talents-locaux-coaching-formation-pme', 'zh' => 'local-talents-sme-coaching-training'],
                'title' => [
                    'en' => 'Local Talents & Ideas, SME Coaching & Training in Africa',
                    'fr' => 'Talents et idées locales, coaching et formation des PME en Afrique',
                    'zh' => '非洲本土人才与创意、中小企业辅导与培训',
                ],
                'tagline' => [
                    'en' => 'Revealing local talents and strengthening SMEs through coaching and training.',
                    'fr' => 'Révéler les talents locaux et renforcer les PME par le coaching et la formation.',
                    'zh' => '发掘本土人才，通过辅导与培训增强中小企业实力。',
                ],
                'audience' => ['en' => 'Talents, entrepreneurs, SMEs', 'fr' => 'Talents, entrepreneurs, PME', 'zh' => '人才、创业者、中小企业'],
                'intro' => [
                    'en' => 'Africa’s growth is carried by its entrepreneurs. We support local talents and ideas and help small and medium-sized enterprises strengthen their management, their offer and their investment readiness.',
                    'fr' => 'La croissance africaine est portée par ses entrepreneurs. Nous soutenons les talents et les idées locales et aidons les petites et moyennes entreprises à renforcer leur gestion, leur offre et leur capacité à accueillir des investisseurs.',
                    'zh' => '非洲的增长由创业者推动。我们支持本土人才与创意，帮助中小企业提升管理水平、完善产品与服务，并增强吸引投资的能力。',
                ],
                'challenges' => [
                    'en' => 'Many SMEs have strong potential but limited access to coaching, training and networks. Targeted support accelerates their development and their bankability.',
                    'fr' => 'De nombreuses PME ont un fort potentiel mais un accès limité au coaching, à la formation et aux réseaux. Un accompagnement ciblé accélère leur développement et leur finançabilité.',
                    'zh' => '许多中小企业潜力巨大，但缺乏获得辅导、培训和人脉资源的渠道。有针对性的支持能够加快其发展，并提升其融资能力。',
                ],
                'services' => [
                    'en' => ['Identification of talents and innovative ideas', 'Individual and collective coaching of entrepreneurs', 'Training in management, finance and governance', 'Preparation for meeting investors'],
                    'fr' => ['Repérage des talents et des idées innovantes', 'Coaching individuel et collectif des entrepreneurs', 'Formation en gestion, finance et gouvernance', 'Préparation à la rencontre des investisseurs'],
                    'zh' => ['发掘人才与创新创意', '为创业者提供一对一及集体辅导', '管理、财务与治理培训', '为对接投资者做好准备'],
                ],
                'benefits' => [
                    'en' => ['Stronger management practices', 'Better access to financing', 'A network of peers and mentors'],
                    'fr' => ['Des pratiques de gestion renforcées', 'Un meilleur accès au financement', 'Un réseau de pairs et de mentors'],
                    'zh' => ['更规范的管理实践', '更便捷的融资渠道', '同行与导师网络'],
                ],
                'steps' => [
                    'en' => ['Diagnosis', 'Coaching plan', 'Training', 'Investment readiness'],
                    'fr' => ['Diagnostic', 'Plan d’accompagnement', 'Formation', 'Préparation à l’investissement'],
                    'zh' => ['诊断评估', '辅导计划', '培训提升', '投资准备'],
                ],
            ],
            [
                'number' => 4,
                'icon' => 'industry',
                'slug' => ['en' => 'vsme-industrialization-manufacturing', 'fr' => 'industrialisation-procedes-fabrication-tpe-pme', 'zh' => 'vsme-industrialization-manufacturing'],
                'title' => [
                    'en' => 'VSME Coaching in Industrialization and Manufacturing Processes',
                    'fr' => 'Accompagnement des TPE-PME dans l’industrialisation et les procédés de fabrication',
                    'zh' => '小微及中小企业工业化与生产流程辅导',
                ],
                'tagline' => [
                    'en' => 'Helping very small and medium enterprises move towards industrial production and local transformation.',
                    'fr' => 'Aider les très petites et moyennes entreprises à passer à la production industrielle et à la transformation locale.',
                    'zh' => '帮助小微及中小企业迈向工业化生产与本地加工。',
                ],
                'audience' => ['en' => 'Industrial VSMEs', 'fr' => 'TPE-PME industrielles', 'zh' => '工业类小微及中小企业'],
                'intro' => [
                    'en' => 'Local transformation of resources is a lever for jobs and added value. We coach very small and medium-sized enterprises in structuring their production, improving their processes and scaling up their industrial activity.',
                    'fr' => 'La transformation locale des ressources est un levier d’emplois et de valeur ajoutée. Nous accompagnons les très petites et moyennes entreprises dans la structuration de leur production, l’amélioration de leurs procédés et le passage à l’échelle industrielle.',
                    'zh' => '资源的本地加工是创造就业和附加值的重要杠杆。我们辅导小微及中小企业规范生产组织、优化工艺流程，实现工业化规模扩张。',
                ],
                'challenges' => [
                    'en' => 'Moving from artisanal to industrial production requires know-how in processes, quality, equipment and financing. Each step can be prepared and secured.',
                    'fr' => 'Passer d’une production artisanale à une production industrielle exige un savoir-faire en procédés, qualité, équipements et financement. Chaque étape peut être préparée et sécurisée.',
                    'zh' => '从手工生产迈向工业化生产，需要在工艺、质量、设备与融资方面具备专业能力。每一步都可以提前规划、稳步推进。',
                ],
                'services' => [
                    'en' => ['Diagnosis of production capacities', 'Optimisation of manufacturing processes and quality', 'Support in selecting equipment and technologies', 'Preparation of industrial investment projects'],
                    'fr' => ['Diagnostic des capacités de production', 'Optimisation des procédés de fabrication et de la qualité', 'Appui au choix des équipements et technologies', 'Préparation des projets d’investissement industriel'],
                    'zh' => ['生产能力诊断', '优化生产工艺与质量管理', '协助选择设备与技术', '筹备工业投资项目'],
                ],
                'benefits' => [
                    'en' => ['Higher productivity and quality', 'More local added value', 'Industrial projects ready for investors'],
                    'fr' => ['Une productivité et une qualité accrues', 'Davantage de valeur ajoutée locale', 'Des projets industriels prêts pour les investisseurs'],
                    'zh' => ['提升生产效率与质量', '增加本地附加值', '打造可对接投资者的工业项目'],
                ],
                'steps' => [
                    'en' => ['Production audit', 'Process improvement', 'Equipment plan', 'Scale-up financing'],
                    'fr' => ['Audit de production', 'Amélioration des procédés', 'Plan d’équipement', 'Financement du passage à l’échelle'],
                    'zh' => ['生产审核', '工艺改进', '设备规划', '扩产融资'],
                ],
            ],
            [
                'number' => 5,
                'icon' => 'diaspora',
                'slug' => ['en' => 'diaspora-investment-guide-relations', 'fr' => 'guide-investissement-relations-diaspora', 'zh' => 'diaspora-investment-guide-relations'],
                'title' => [
                    'en' => 'Diaspora Investment Guide and Relations',
                    'fr' => 'Guide d’investissement et relations avec la diaspora',
                    'zh' => '海外侨民投资指南与关系',
                ],
                'tagline' => [
                    'en' => 'Guiding the African diaspora towards secure, impactful investments on the continent.',
                    'fr' => 'Guider la diaspora africaine vers des investissements sécurisés et utiles sur le continent.',
                    'zh' => '引导非洲海外侨民在非洲大陆进行安全、有影响力的投资。',
                ],
                'audience' => ['en' => 'African diaspora', 'fr' => 'Diaspora africaine', 'zh' => '非洲海外侨民'],
                'intro' => [
                    'en' => 'The diaspora is a major and committed source of investment for Africa. We provide practical guidance and connect diaspora investors with reliable opportunities and partners in their countries of interest.',
                    'fr' => 'La diaspora est une source d’investissement majeure et engagée pour l’Afrique. Nous proposons un accompagnement concret et mettons en relation les investisseurs de la diaspora avec des opportunités et des partenaires fiables dans leurs pays d’intérêt.',
                    'zh' => '海外侨民是非洲重要且积极的投资力量。我们提供切实可行的投资指导，并为侨民投资者对接其目标国家可靠的投资机会与合作伙伴。',
                ],
                'challenges' => [
                    'en' => 'Distance, lack of trusted information and fear of mismanagement often hold back diaspora investment. Guidance and reliable intermediaries build confidence.',
                    'fr' => 'La distance, le manque d’information fiable et la crainte d’une mauvaise gestion freinent souvent l’investissement de la diaspora. Un accompagnement et des intermédiaires fiables créent la confiance.',
                    'zh' => '距离遥远、缺乏可靠信息以及对管理风险的担忧，常常阻碍侨民投资。专业指导和可信赖的中介能够建立信心。',
                ],
                'services' => [
                    'en' => ['Practical investment guide by country', 'Information on legal and tax frameworks', 'Matching with vetted projects and partners', 'Diaspora networking events'],
                    'fr' => ['Guide pratique d’investissement par pays', 'Information sur les cadres juridiques et fiscaux', 'Mise en relation avec des projets et partenaires vérifiés', 'Rencontres et réseautage de la diaspora'],
                    'zh' => ['分国别实用投资指南', '法律与税务框架信息', '对接经过审核的项目与合作伙伴', '侨民交流与对接活动'],
                ],
                'benefits' => [
                    'en' => ['Invest with confidence from abroad', 'Reliable local relays', 'A community of diaspora investors'],
                    'fr' => ['Investir en confiance depuis l’étranger', 'Des relais locaux fiables', 'Une communauté d’investisseurs de la diaspora'],
                    'zh' => ['身在海外也能安心投资', '可靠的本地对接渠道', '侨民投资者社群'],
                ],
                'steps' => [
                    'en' => ['Investor profile', 'Guidance', 'Opportunity matching', 'Follow-up'],
                    'fr' => ['Profil investisseur', 'Orientation', 'Mise en relation', 'Suivi'],
                    'zh' => ['投资者画像', '投资指导', '机会对接', '持续跟进'],
                ],
            ],
            [
                'number' => 6,
                'icon' => 'government',
                'slug' => ['en' => 'african-government-affairs-lobbying', 'fr' => 'affaires-gouvernementales-plaidoyer', 'zh' => 'african-government-affairs-lobbying'],
                'title' => [
                    'en' => 'African Government Affairs and Lobbying',
                    'fr' => 'Affaires gouvernementales africaines et plaidoyer',
                    'zh' => '非洲政府事务与游说',
                ],
                'tagline' => [
                    'en' => 'Building dialogue between investors, institutions and African governments.',
                    'fr' => 'Construire le dialogue entre investisseurs, institutions et gouvernements africains.',
                    'zh' => '搭建投资者、机构与非洲各国政府之间的对话桥梁。',
                ],
                'audience' => ['en' => 'Governments and institutions', 'fr' => 'Gouvernements et institutions', 'zh' => '政府与机构'],
                'intro' => [
                    'en' => 'A favourable investment climate is built together with public authorities. We facilitate dialogue between investors and African governments and advocate for frameworks that encourage responsible investment.',
                    'fr' => 'Un climat d’investissement favorable se construit avec les pouvoirs publics. Nous facilitons le dialogue entre investisseurs et gouvernements africains et portons un plaidoyer en faveur de cadres propices à l’investissement responsable.',
                    'zh' => '良好的投资环境需要与政府部门共同营造。我们促进投资者与非洲各国政府之间的对话，并倡导有利于负责任投资的政策框架。',
                ],
                'challenges' => [
                    'en' => 'Investment decisions depend on stable, predictable and transparent public frameworks. Structured dialogue helps align public priorities and private initiatives.',
                    'fr' => 'Les décisions d’investissement dépendent de cadres publics stables, prévisibles et transparents. Un dialogue structuré aide à aligner priorités publiques et initiatives privées.',
                    'zh' => '投资决策有赖于稳定、可预期、透明的公共政策框架。结构化的对话有助于协调公共优先事项与私营部门的举措。',
                ],
                'services' => [
                    'en' => ['Facilitation of public-private dialogue', 'Institutional introductions for investors', 'Advocacy for an improved investment climate', 'Support to public investment promotion initiatives'],
                    'fr' => ['Facilitation du dialogue public-privé', 'Mises en relation institutionnelles pour les investisseurs', 'Plaidoyer pour l’amélioration du climat des affaires', 'Appui aux initiatives publiques de promotion de l’investissement'],
                    'zh' => ['促进公私部门对话', '为投资者对接政府机构', '倡导改善营商环境', '支持政府投资促进举措'],
                ],
                'benefits' => [
                    'en' => ['Access to the right institutional contacts', 'Better understanding of public priorities', 'A voice for investment in Africa'],
                    'fr' => ['L’accès aux bons interlocuteurs institutionnels', 'Une meilleure compréhension des priorités publiques', 'Une voix pour l’investissement en Afrique'],
                    'zh' => ['对接合适的政府机构人员', '更好地理解公共政策重点', '为非洲投资发声'],
                ],
                'steps' => [
                    'en' => ['Issue analysis', 'Stakeholder mapping', 'Dialogue', 'Advocacy and follow-up'],
                    'fr' => ['Analyse des enjeux', 'Cartographie des acteurs', 'Dialogue', 'Plaidoyer et suivi'],
                    'zh' => ['议题分析', '利益相关方梳理', '开展对话', '倡导与跟进'],
                ],
            ],
        ];
    }
}
