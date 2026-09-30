<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Filament\Resources\Pages\PageResource;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use App\Services\ImageOptimizer;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DomainSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([DomainSeeder::class, SectorSeeder::class, SettingSeeder::class, MenuSeeder::class, DemoSeeder::class]);
    }

    private function page(array $overrides = []): Page
    {
        return Page::query()->create(array_merge([
            'title' => ['en' => 'Investor guide', 'fr' => 'Guide investisseur', 'zh' => '投资者指南'],
            'slug' => ['en' => 'investor-guide', 'fr' => 'guide-investisseur', 'zh' => 'investor-guide'],
            'status' => Page::STATUS_PUBLISHED,
            'blocks' => [
                ['type' => 'hero', 'data' => ['title' => ['en' => 'Hero EN', 'fr' => 'Hero FR', 'zh' => '标题'], 'dark' => true]],
                ['type' => 'text', 'data' => ['body' => ['en' => '<p>Body <script>x</script>text</p>', 'fr' => '<p>Texte FR</p>']]],
                ['type' => 'stats', 'data' => ['items' => [['value' => 12, 'label' => ['en' => 'Countries']]]]],
            ],
        ], $overrides));
    }

    public function test_published_block_page_renders_in_three_languages(): void
    {
        $this->page();

        $this->get('/en/investor-guide')->assertOk()->assertSee('Hero EN')->assertSee('Body text')->assertDontSee('<script>x', false);
        $this->get('/fr/guide-investisseur')->assertOk()->assertSee('Hero FR')->assertSee('hreflang="zh-Hans" href="'.url('/zh/investor-guide').'"', false);
        $this->get('/zh/investor-guide')->assertOk()->assertSee('标题');
        $this->get('/fr/investor-guide')->assertRedirect('/fr/guide-investisseur');
    }

    public function test_drafts_and_scheduled_pages_are_hidden_but_previewable(): void
    {
        $draft = $this->page(['status' => Page::STATUS_DRAFT]);
        $this->get('/en/investor-guide')->assertNotFound();

        $draft->update(['status' => Page::STATUS_PUBLISHED, 'published_at' => now()->addDay()]);
        $this->get('/en/investor-guide')->assertNotFound();

        $url = PageResource::previewUrl($draft, 'fr');
        $this->get($url)->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create(['role' => Role::Editor]))->get($url)->assertOk()->assertSee('Hero FR');
        $this->actingAs(User::factory()->create(['role' => Role::ProjectOfficer]))->get($url)->assertForbidden();
    }

    public function test_pages_are_listed_in_sitemaps(): void
    {
        $this->page();

        $this->get('/sitemap-fr.xml')->assertSee(url('/fr/guide-investisseur'));
    }

    public function test_previous_versions_can_be_restored(): void
    {
        $this->actingAs(User::factory()->create(['role' => Role::Admin]));
        $project = Project::query()->where('reference', 'P-2026-001')->firstOrFail();
        $original = $project->tr('title', 'en');

        $project->update(['title' => ['en' => 'Changed title', 'fr' => 'Titre modifié', 'zh' => '改']]);
        $this->assertSame(1, $project->revisions()->count());

        $project->restoreRevision($project->revisions()->first());

        $this->assertSame($original, $project->fresh()->tr('title', 'en'));
        $this->assertDatabaseHas('activity_logs', ['action' => 'project.restored']);
    }

    public function test_menus_are_administrable_per_language(): void
    {
        $page = $this->page();
        MenuItem::query()->create([
            'location' => 'header', 'type' => 'page', 'sort' => 5,
            'page_id' => $page->id,
            'label' => ['en' => 'Guide', 'fr' => 'Le guide', 'zh' => '指南'],
        ]);
        MenuItem::query()->where(['location' => 'header', 'target' => 'partners'])->update(['is_active' => false]);

        $this->get('/fr')->assertSee('Le guide')->assertSee('/fr/guide-investisseur');
        $this->get('/zh')->assertSee('指南');
        $this->get('/en')->assertDontSee('class="nav-link" >Partners', false);
    }

    public function test_uploaded_images_get_avif_and_webp_variants(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('cover.jpg', 2000, 1200)->store('projects', 'public');

        $manifest = app(ImageOptimizer::class)->optimize($path);

        $this->assertSame(2000, $manifest['width']);
        $this->assertSame([480, 960, 1600], array_keys($manifest['variants']['webp']));
        $this->assertArrayHasKey('avif', $manifest['variants']);
        Storage::disk('public')->assertExists($manifest['variants']['webp'][960]);

        Project::query()->where('reference', 'P-2026-001')->update(['cover_image' => $path]);
        $this->get('/en/get-involved/invest-in-a-project')->assertSee('type="image/avif"', false)->assertSee('960w');
    }
}
