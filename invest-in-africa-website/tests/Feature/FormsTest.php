<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Http\Middleware\ProtectAgainstSpam;
use App\Mail\BrandedMail;
use App\Models\ContactMessage;
use App\Models\InterestExpression;
use App\Models\Project;
use App\Models\Sector;
use App\Models\Setting;
use App\Models\Submission;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DomainSeeder;
use Database\Seeders\SectorSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([DomainSeeder::class, SectorSeeder::class, SettingSeeder::class, DemoSeeder::class]);
        Mail::fake();
        Storage::fake('local');
    }

    private function antispam(): array
    {
        return ['_started' => ProtectAgainstSpam::token(), 'website' => ''];
    }

    private function submission(array $overrides = []): array
    {
        return array_merge($this->antispam(), [
            'full_name' => 'Awa Diallo',
            'organization' => 'Solaris SARL',
            'email' => 'awa@example.org',
            'phone' => '+221 77 123 45 67',
            'country' => 'SN',
            'position' => 'CEO',
            'project_name' => 'Solar cold rooms',
            'project_country' => 'SN',
            'sector_id' => Sector::query()->where('code', 'energy')->value('id'),
            'description' => str_repeat('A solar-powered cold chain for fishing communities. ', 3),
            'stage' => 'growth',
            'investment_amount' => '1 500 000',
            'currency' => 'EUR',
            'funding_type' => 'mixed',
            'timeline' => 'Within 12 months',
            'consent' => '1',
            'certify' => '1',
        ], $overrides);
    }

    public function test_project_submission_is_stored_with_private_documents_and_emails(): void
    {
        Setting::put('notifications', ['submissions' => ['projects@example.org']]);

        $response = $this->post('/fr/s-engager/soumettre-un-projet', $this->submission([
            'documents' => [
                UploadedFile::fake()->create('business-plan.pdf', 800, 'application/pdf'),
                UploadedFile::fake()->image('site.jpg'),
            ],
        ]));

        $response->assertRedirect('/fr/merci');

        $submission = Submission::query()->firstOrFail();
        $this->assertMatchesRegularExpression('/^S-\d{4}-0001$/', $submission->reference);
        $this->assertSame('fr', $submission->locale);
        $this->assertSame(SubmissionStatus::Received, $submission->status);
        $this->assertEquals(1500000, (float) $submission->investment_amount);
        $this->assertCount(2, $submission->documents);

        // Renamed and stored outside the public space.
        $document = $submission->documents->first();
        $this->assertStringStartsWith("submissions/{$submission->reference}/", $document->path);
        $this->assertStringNotContainsString('business-plan', $document->path);
        Storage::disk('local')->assertExists($document->path);

        // Acknowledgement in the visitor's language + team notification.
        Mail::assertQueued(BrandedMail::class, fn (BrandedMail $m) => $m->template === 'submission_received' && $m->hasTo('awa@example.org') && $m->locale === 'fr');
        Mail::assertQueued(BrandedMail::class, fn (BrandedMail $m) => $m->template === 'submission_team' && $m->hasTo('projects@example.org'));

        $this->followRedirects($response)->assertSee($submission->reference);
    }

    public function test_forbidden_file_types_are_rejected(): void
    {
        $this->post('/en/get-involved/submit-a-project', $this->submission([
            'documents' => [UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload')],
        ]))->assertSessionHasErrors('documents.0');

        $this->assertSame(0, Submission::query()->count());
    }

    public function test_too_many_or_too_large_files_are_rejected(): void
    {
        $this->post('/en/get-involved/submit-a-project', $this->submission([
            'documents' => array_map(fn ($i) => UploadedFile::fake()->create("doc$i.pdf", 10, 'application/pdf'), range(1, 6)),
        ]))->assertSessionHasErrors('documents');

        $this->post('/en/get-involved/submit-a-project', $this->submission([
            'documents' => [UploadedFile::fake()->create('huge.pdf', 11000, 'application/pdf')],
        ]))->assertSessionHasErrors('documents.0');
    }

    public function test_required_fields_and_certification_are_validated(): void
    {
        $this->post('/en/get-involved/submit-a-project', $this->submission(['email' => 'not-an-email', 'certify' => null, 'stage' => 'unknown']))
            ->assertSessionHasErrors(['email', 'certify', 'stage']);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/en/contact-us', array_merge($this->contact(), ['website' => 'http://spam.example']))
            ->assertSessionHasErrors('form');

        $this->assertSame(0, ContactMessage::query()->count());
    }

    public function test_forms_filled_too_fast_are_blocked(): void
    {
        config(['site.antispam.min_seconds' => 5]);

        $this->post('/en/contact-us', $this->contact())->assertSessionHasErrors('form');
        $this->post('/en/contact-us', array_merge($this->contact(), ['_started' => 'tampered']))->assertSessionHasErrors('form');
    }

    private function contact(array $overrides = []): array
    {
        return array_merge($this->antispam(), [
            'subject' => 'partnership',
            'full_name' => 'Li Wei',
            'email' => 'li@example.cn',
            'message' => 'We would like to discuss a partnership.',
            'consent' => '1',
        ], $overrides);
    }

    public function test_contact_message_is_routed_by_subject(): void
    {
        Setting::put('notifications', ['contact_partnership' => ['partners@example.org'], 'contact_other' => ['info@example.org']]);

        $this->post('/zh/contact-us', $this->contact())->assertRedirect('/zh/thank-you');

        $message = ContactMessage::query()->firstOrFail();
        $this->assertSame('zh', $message->locale);

        Mail::assertQueued(BrandedMail::class, fn (BrandedMail $m) => $m->template === 'contact_team' && $m->hasTo('partners@example.org'));
        Mail::assertQueued(BrandedMail::class, fn (BrandedMail $m) => $m->template === 'contact_received' && $m->locale === 'zh');
    }

    public function test_expression_of_interest_is_linked_to_the_project(): void
    {
        $project = Project::query()->where('reference', 'P-2026-001')->firstOrFail();

        $this->post('/en/get-involved/invest-in-a-project/p-2026-001', array_merge($this->antispam(), [
            'full_name' => 'Jane Investor',
            'investor_type' => 'fund',
            'email' => 'jane@fund.example',
            'phone' => '+44 20 1234 5678',
            'country' => 'GB',
            'amount' => '500000',
            'consent' => '1',
        ]))->assertRedirect('/en/thank-you');

        $interest = InterestExpression::query()->firstOrFail();
        $this->assertTrue($interest->project->is($project));
        Mail::assertQueued(BrandedMail::class, fn (BrandedMail $m) => $m->template === 'interest_received');
    }

    public function test_interest_is_refused_on_closed_projects(): void
    {
        Project::query()->where('reference', 'P-2026-001')->update(['status' => 'closed']);

        $this->post('/en/get-involved/invest-in-a-project/p-2026-001', array_merge($this->antispam(), [
            'full_name' => 'Jane', 'investor_type' => 'fund', 'email' => 'jane@fund.example',
            'phone' => '+44 20 1234 5678', 'country' => 'GB', 'consent' => '1',
        ]))->assertForbidden();
    }

    public function test_confirmation_page_requires_a_submission(): void
    {
        $this->get('/en/thank-you')->assertRedirect('/en');
    }

    public function test_forms_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/en/contact-us', $this->contact());
        }

        $this->post('/en/contact-us', $this->contact())->assertStatus(429);
    }
}
