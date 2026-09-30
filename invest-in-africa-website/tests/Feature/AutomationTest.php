<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\ContentTranslation;
use App\Models\Domain;
use App\Models\Project;
use App\Models\Setting;
use App\Models\User;
use App\Services\Backup;
use App\Services\IndexNow;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DomainSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Everything that must happen without manual intervention. */
class AutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_install_is_complete_and_idempotent(): void
    {
        File::delete(storage_path('app/private/first-admin.txt'));
        config(['site.admin.email' => 'owner@example.org', 'site.admin.password' => null]);

        $this->artisan('site:install', ['--force' => true])->assertSuccessful();

        $admin = User::query()->where('role', Role::SuperAdmin)->firstOrFail();
        $this->assertSame('owner@example.org', $admin->email);
        $this->assertFileExists(storage_path('app/private/first-admin.txt'));
        $this->assertDatabaseCount('domains', 6);
        $this->assertDatabaseHas('menu_items', ['location' => 'header']);
        $this->assertGreaterThan(100, ContentTranslation::query()->count());

        // Content edited in the back-office survives the next deployment.
        Domain::query()->where('number', 1)->first()->update(['tagline' => ['en' => 'Edited', 'fr' => 'Modifié', 'zh' => '已修改']]);
        $this->artisan('site:install', ['--force' => true])->assertSuccessful();
        $this->assertSame(1, User::query()->where('role', Role::SuperAdmin)->count());
        $this->assertSame('Edited', Domain::query()->where('number', 1)->first()->tr('tagline', 'en'));

        File::delete(storage_path('app/private/first-admin.txt'));
    }

    public function test_backups_are_encrypted_without_configuration(): void
    {
        config(['site.backup.password' => null]);

        $this->assertNotEmpty(Backup::password());
        $this->assertSame(Backup::password(), Backup::password());
    }

    public function test_published_projects_are_notified_to_search_engines(): void
    {
        $this->seed([DomainSeeder::class, SectorSeeder::class, SettingSeeder::class, DemoSeeder::class]);
        config(['site.indexnow.enabled' => true]);
        Http::fake(['*' => Http::response('', 200)]);

        Project::query()->where('reference', 'P-2026-002')->first()->update(['status' => 'funding']);
        Project::query()->where('reference', 'P-2026-003')->first()->update(['is_published' => false]);

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request['key'] === IndexNow::key()
            && count($request['urlList']) === 3
            && str_contains($request['urlList'][1], '/fr/s-engager/investir-dans-un-projet/p-2026-002'));

        $this->get('/'.IndexNow::key().'.txt')->assertOk()->assertSee(IndexNow::key());
        $this->get('/0000000000000000.txt')->assertNotFound();
    }

    public function test_legal_pages_are_complete_and_follow_settings(): void
    {
        $this->seed(SettingSeeder::class);

        $this->get('/en/legal-notice')->assertOk()->assertDontSee('to be validated')->assertSee('legal representative of The Invest In Africa Initiative');
        $this->get('/fr/politique-de-confidentialite')->assertSee('24 mois');

        Setting::put('legal', ['director' => 'Jane Doe', 'host' => 'Host SA, Dakar']);
        Setting::put('contact', ['email' => 'info@example.org']);
        Setting::put('search', ['google' => 'abc123']);

        $this->get('/en/legal-notice')->assertSee('Publication director: Jane Doe')->assertSee('Host SA, Dakar')->assertSee('mailto:info@example.org', false);
        $this->get('/en')->assertSee('<meta name="google-site-verification" content="abc123">', false);
    }
}
