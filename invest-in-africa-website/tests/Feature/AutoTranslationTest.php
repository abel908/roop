<?php

namespace Tests\Feature;

use App\Jobs\TranslateMissingLanguages;
use App\Models\ContentTranslation;
use App\Models\Page;
use App\Models\Project;
use App\Models\Setting;
use App\Services\Translator;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DomainSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Automatic completion of missing languages — the model call is faked. */
class AutoTranslationTest extends TestCase
{
    use RefreshDatabase;

    public array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([DomainSeeder::class, SectorSeeder::class, SettingSeeder::class, DemoSeeder::class]);
        config(['services.anthropic.key' => 'test-key']);

        $test = $this;
        $this->app->instance(Translator::class, new class($test) extends Translator
        {
            public function __construct(private $test)
            {
                parent::__construct();
            }

            protected function complete(string $payload, array $schema): ?string
            {
                $this->test->calls[] = $payload;
                $out = [];

                foreach (json_decode($payload, true) as $key => $item) {
                    foreach ($item['to'] as $locale) {
                        $out[$key][$locale] = "[$locale] ".$item['text'];
                    }
                }

                return json_encode($out, JSON_UNESCAPED_UNICODE);
            }
        });
    }

    public function test_project_written_in_one_language_is_completed(): void
    {
        $project = Project::query()->create([
            'title' => ['fr' => 'Centrale solaire'], 'summary' => ['fr' => 'Résumé du projet'],
            'use_of_funds' => ['fr' => ['Équipements', 'Formation']],
            'country' => 'SN', 'stage' => 'growth', 'investment_amount' => 1000000, 'currency' => 'EUR',
            'funding_type' => 'equity', 'status' => 'open', 'is_published' => true,
        ])->refresh();

        $this->assertSame('[en] Centrale solaire', $project->tr('title', 'en'));
        $this->assertSame('[zh] Résumé du projet', $project->tr('summary', 'zh'));
        $this->assertSame(['[en] Équipements', '[en] Formation'], $project->tr('use_of_funds', 'en'));
        $this->assertSame('Centrale solaire', $project->tr('title', 'fr'));
        $this->assertDatabaseHas('activity_logs', ['action' => 'translation.auto']);

        // The sheet now exists in Chinese (no redirect to the catalogue).
        $this->get('/zh/get-involved/invest-in-a-project/'.$project->slugKey())->assertOk()->assertSee('[zh] Centrale solaire');
    }

    public function test_complete_records_trigger_no_call(): void
    {
        $this->calls = [];
        Project::query()->where('reference', 'P-2026-001')->first()->update(['is_featured' => false]);

        $this->assertSame([], $this->calls);
    }

    public function test_page_blocks_and_slugs_are_completed(): void
    {
        $page = Page::query()->create([
            'title' => ['en' => 'Investor guide'], 'slug' => ['en' => 'investor-guide'], 'status' => 'published',
            'blocks' => [['type' => 'text', 'data' => ['heading' => ['en' => 'Why Africa'], 'body' => ['en' => '<p>Growth <strong>markets</strong></p>']]]],
        ])->refresh();

        $this->assertSame('[fr] Investor guide', $page->tr('title', 'fr'));
        $this->assertSame('fr-investor-guide', $page->tr('slug', 'fr'));
        $this->assertSame('investor-guide', $page->tr('slug', 'zh'));
        $this->assertSame('[zh] <p>Growth <strong>markets</strong></p>', $page->blocks[0]['data']['body']['zh']);
        $this->get('/fr/fr-investor-guide')->assertOk();
    }

    public function test_settings_and_text_overrides_are_completed(): void
    {
        Setting::put('impact', ['figures' => [['value' => 12, 'label' => ['en' => 'Countries']]]]);
        TranslateMissingLanguages::dispatch(settingKeys: ['impact']);
        $this->assertSame('[fr] Countries', Setting::get('impact.figures.0.label.fr'));

        ContentTranslation::syncFromFiles();
        $row = ContentTranslation::query()->where(['group' => 'home', 'key' => 'cta.title'])->first();
        $row->update(['fr' => 'Nouveau titre']);
        $this->assertSame('[zh] Nouveau titre', $row->fresh()->zh);
    }

    public function test_can_be_disabled(): void
    {
        Setting::put('translation', ['auto' => false]);
        $this->calls = [];

        Project::query()->create([
            'title' => ['fr' => 'Sans traduction'], 'country' => 'SN', 'stage' => 'idea', 'investment_amount' => 1,
            'currency' => 'EUR', 'funding_type' => 'equity', 'status' => 'open',
        ]);

        $this->assertSame([], $this->calls);
    }
}
