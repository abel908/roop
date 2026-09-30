<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Redirect;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DomainSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([DomainSeeder::class, SectorSeeder::class, SettingSeeder::class, DemoSeeder::class]);
    }

    public static function pages(): array
    {
        return [
            'en home' => ['/en', 'en'],
            'en about' => ['/en/about-us', 'en'],
            'en mission' => ['/en/our-mission', 'en'],
            'en what we do' => ['/en/what-we-do', 'en'],
            'en domain' => ['/en/what-we-do/fdi-promotion-orientation-support', 'en'],
            'en get involved' => ['/en/get-involved', 'en'],
            'en catalogue' => ['/en/get-involved/invest-in-a-project', 'en'],
            'en project' => ['/en/get-involved/invest-in-a-project/p-2026-001', 'en'],
            'en submit' => ['/en/get-involved/submit-a-project', 'en'],
            'en partners' => ['/en/partners', 'en'],
            'en contact' => ['/en/contact-us', 'en'],
            'en legal' => ['/en/legal-notice', 'en'],
            'fr home' => ['/fr', 'fr'],
            'fr about' => ['/fr/a-propos', 'fr'],
            'fr domain' => ['/fr/nos-domaines/promotion-orientation-accompagnement-ide', 'fr'],
            'fr catalogue' => ['/fr/s-engager/investir-dans-un-projet', 'fr'],
            'fr submit' => ['/fr/s-engager/soumettre-un-projet', 'fr'],
            'fr privacy' => ['/fr/politique-de-confidentialite', 'fr'],
            'zh home' => ['/zh', 'zh-Hans'],
            'zh about' => ['/zh/about-us', 'zh-Hans'],
            'zh project' => ['/zh/get-involved/invest-in-a-project/p-2026-002', 'zh-Hans'],
            'zh contact' => ['/zh/contact-us', 'zh-Hans'],
        ];
    }

    #[DataProvider('pages')]
    public function test_every_page_renders_in_its_language(string $url, string $lang): void
    {
        $this->get($url)
            ->assertOk()
            ->assertSee('<html lang="'.$lang.'"', false)
            ->assertSee('hreflang="x-default"', false)
            ->assertSee('rel="canonical"', false);
    }

    public function test_root_redirects_to_default_or_remembered_language(): void
    {
        $this->get('/')->assertRedirect('/en');
        $this->withUnencryptedCookie('site_locale', 'zh')->get('/')->assertRedirect('/zh');
    }

    public function test_hreflang_alternates_point_to_translated_slugs(): void
    {
        $this->get('/en/about-us')
            ->assertSee('hreflang="fr" href="'.url('/fr/a-propos').'"', false)
            ->assertSee('hreflang="zh-Hans" href="'.url('/zh/about-us').'"', false);

        $this->get('/fr/nos-domaines/promotion-orientation-accompagnement-ide')
            ->assertSee('href="'.url('/en/what-we-do/fdi-promotion-orientation-support').'"', false);
    }

    public function test_domain_slug_of_another_language_redirects_to_canonical_slug(): void
    {
        $this->get('/fr/nos-domaines/fdi-promotion-orientation-support')
            ->assertRedirect('/fr/nos-domaines/promotion-orientation-accompagnement-ide');
    }

    public function test_untranslated_project_redirects_to_parent_section_with_message(): void
    {
        $project = Project::query()->where('reference', 'P-2026-001')->first();
        $project->update(['title' => ['en' => 'Only English'], 'summary' => ['en' => 'Only English']]);

        $this->get('/zh/get-involved/invest-in-a-project/p-2026-001')
            ->assertRedirect('/zh/get-involved/invest-in-a-project')
            ->assertSessionHas('notice');
    }

    public function test_unpublished_project_is_not_reachable(): void
    {
        Project::query()->where('reference', 'P-2026-001')->update(['is_published' => false]);

        $this->get('/en/get-involved/invest-in-a-project/p-2026-001')->assertNotFound();
    }

    public function test_catalogue_filters_are_reflected_in_the_url(): void
    {
        $this->get('/en/get-involved/invest-in-a-project?country=SN')
            ->assertOk()
            ->assertSee('invest-in-a-project/p-2026-001"', false)
            ->assertDontSee('invest-in-a-project/p-2026-002"', false);

        $this->get('/en/get-involved/invest-in-a-project?sector=agriculture&stage=early_revenue')
            ->assertSee('invest-in-a-project/p-2026-002"', false)
            ->assertDontSee('invest-in-a-project/p-2026-001"', false);

        $this->get('/en/get-involved/invest-in-a-project?range=over-20m')
            ->assertSee('invest-in-a-project/p-2026-004"', false)
            ->assertDontSee('invest-in-a-project/p-2026-003"', false);
    }

    public function test_chinese_keyword_search_matches_translated_titles(): void
    {
        $this->get('/zh/get-involved/invest-in-a-project?q='.urlencode('腰果'))
            ->assertOk()
            ->assertSee('invest-in-a-project/p-2026-002"', false)
            ->assertDontSee('invest-in-a-project/p-2026-001"', false);
    }

    public function test_trilingual_404_page(): void
    {
        $this->get('/fr/page-inexistante')->assertNotFound()->assertSee('Page introuvable');
        $this->get('/zh/missing')->assertNotFound()->assertSee('页面未找到');
    }

    public function test_back_office_redirects_are_applied(): void
    {
        Redirect::query()->create(['from_path' => '/old-about', 'to_path' => '/en/about-us']);

        $this->get('/old-about')->assertRedirect('/en/about-us')->assertStatus(301);
    }

    public function test_sitemaps_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertSee('sitemap-zh.xml');
        $this->get('/sitemap-fr.xml')->assertOk()
            ->assertSee(url('/fr/a-propos'))
            ->assertSee('hreflang="zh-Hans"', false)
            ->assertSee('hreflang="x-default"', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/en')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_no_google_fonts_or_maps_dependency(): void
    {
        $html = $this->get('/zh')->getContent();

        $this->assertStringNotContainsString('fonts.googleapis.com', $html);
        $this->assertStringNotContainsString('maps.googleapis.com', $html);
        $this->assertStringNotContainsString('recaptcha', $html);
    }
}
