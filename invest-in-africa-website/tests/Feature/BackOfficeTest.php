<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\ContentTranslation;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\User;
use Database\Seeders\DomainSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BackOfficeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([DomainSeeder::class, SectorSeeder::class, SettingSeeder::class]);
    }

    private function user(Role $role): User
    {
        return User::factory()->create(['role' => $role, 'locale' => 'fr', 'is_active' => true]);
    }

    public static function matrix(): array
    {
        // Role / module matrix of §8.3.
        return [
            'super admin settings' => [Role::SuperAdmin, '/admin/site-settings', 200],
            'admin settings' => [Role::Admin, '/admin/site-settings', 403],
            'admin users' => [Role::Admin, '/admin/users', 200],
            'editor pages' => [Role::Editor, '/admin/domains', 200],
            'editor submissions' => [Role::Editor, '/admin/submissions', 403],
            'translator translations' => [Role::Translator, '/admin/content-translations', 200],
            'translator projects' => [Role::Translator, '/admin/projects', 403],
            'officer submissions' => [Role::ProjectOfficer, '/admin/submissions', 200],
            'officer messages' => [Role::ProjectOfficer, '/admin/contact-messages', 200],
            'officer partners' => [Role::ProjectOfficer, '/admin/partners', 403],
            'officer users' => [Role::ProjectOfficer, '/admin/users', 403],
        ];
    }

    #[DataProvider('matrix')]
    public function test_permission_matrix(Role $role, string $url, int $status): void
    {
        $this->actingAs($this->user($role))->get($url)->assertStatus($status);
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_confidential_documents_need_signed_url_and_permission(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('submissions/S-2026-0001/file.pdf', 'content');

        $submission = Submission::query()->create([
            'locale' => 'en', 'full_name' => 'A', 'organization' => 'B', 'email' => 'a@b.c', 'phone' => '+1 555 0100',
            'country' => 'SN', 'position' => 'CEO', 'project_name' => 'P', 'project_country' => 'SN',
            'description' => str_repeat('x', 60), 'stage' => 'idea', 'investment_amount' => 1000, 'currency' => 'USD',
            'funding_type' => 'equity', 'timeline' => 'soon', 'consent_at' => now(), 'certified_at' => now(),
        ]);
        $document = SubmissionDocument::query()->create([
            'submission_id' => $submission->id, 'original_name' => 'plan.pdf',
            'path' => 'submissions/S-2026-0001/file.pdf', 'mime_type' => 'application/pdf', 'size' => 7,
        ]);

        $signed = $document->temporaryUrl();
        $unsigned = route('admin.documents.download', $document);

        $this->get($signed)->assertRedirect('/admin/login');
        $this->actingAs($this->user(Role::ProjectOfficer))->get($unsigned)->assertForbidden();
        $this->actingAs($this->user(Role::Editor))->get($signed)->assertForbidden();
        $this->actingAs($this->user(Role::ProjectOfficer))->get($signed)->assertOk()->assertDownload('plan.pdf');

        $this->assertDatabaseHas('activity_logs', ['action' => 'document.downloaded']);
    }

    public function test_texts_edited_in_back_office_override_files(): void
    {
        ContentTranslation::syncFromFiles();

        ContentTranslation::query()->where(['group' => 'home', 'key' => 'cta.title'])->firstOrFail()
            ->update(['fr' => 'Titre modifié depuis le back-office']);

        $this->get('/fr')->assertSee('Titre modifié depuis le back-office');
        $this->get('/en')->assertSee(__('home.cta.title', [], 'en'));
    }
}
