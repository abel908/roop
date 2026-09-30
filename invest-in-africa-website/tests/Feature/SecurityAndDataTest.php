<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Filament\Auth\Login;
use App\Http\Middleware\ProtectAgainstSpam;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\User;
use App\Services\DataRetention;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityAndDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        Mail::fake();
    }

    private function contact(array $overrides = []): array
    {
        return array_merge([
            '_started' => ProtectAgainstSpam::token(), 'website' => '',
            'subject' => 'other', 'full_name' => 'Ada', 'email' => 'ada@example.org',
            'message' => 'Hello, this is a message.', 'consent' => '1',
        ], $overrides);
    }

    public function test_captcha_is_required_after_repeated_submissions(): void
    {
        $this->post('/en/contact-us', $this->contact())->assertRedirect('/en/thank-you');
        $this->post('/en/contact-us', $this->contact())->assertRedirect('/en/thank-you');

        // Third submission within the hour: the self-hosted question is required.
        $this->get('/en/contact-us')->assertSee('name="captcha"', false);
        $this->post('/en/contact-us', $this->contact())->assertSessionHasErrors('captcha');
        $this->assertSame(2, ContactMessage::query()->count());

        // Correct answer: read the expected hash from the session.
        $this->get('/en/contact-us');
        $hash = session('captcha.answer');
        $answer = collect(range(0, 30))->first(fn ($n) => hash('sha256', (string) $n) === $hash);

        $this->post('/en/contact-us', $this->contact(['captcha' => (string) $answer]))->assertRedirect('/en/thank-you');
        $this->assertSame(3, ContactMessage::query()->count());
    }

    public function test_captcha_appears_after_a_spam_attempt(): void
    {
        $this->post('/en/contact-us', $this->contact(['website' => 'spam']))->assertSessionHasErrors('form');
        $this->get('/en/contact-us')->assertSee('name="captcha"', false);
    }

    public function test_back_office_account_is_locked_after_five_failures(): void
    {
        $user = User::factory()->create(['email' => 'officer@example.org', 'password' => 'Correct-Password-2026!', 'role' => Role::Admin]);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::clear('livewire-rate-limiter:'.sha1(Login::class.'|authenticate|127.0.0.1'));
            Livewire::test(Login::class)
                ->set('data.email', 'officer@example.org')->set('data.password', 'wrong')
                ->call('authenticate')->assertHasErrors('data.email');
        }

        RateLimiter::clear('livewire-rate-limiter:'.sha1(Login::class.'|authenticate|127.0.0.1'));
        Livewire::test(Login::class)
            ->set('data.email', 'officer@example.org')->set('data.password', 'Correct-Password-2026!')
            ->call('authenticate')->assertHasErrors('data.email');

        $this->assertGuest();
        $this->assertDatabaseHas('activity_logs', ['action' => 'auth.locked']);
    }

    public function test_permission_matrix_is_configurable(): void
    {
        $editor = User::factory()->create(['role' => Role::Editor]);
        $this->actingAs($editor)->get('/admin/submissions')->assertForbidden();

        Setting::put('permissions', ['editor' => ['pages', 'submissions']]);

        $this->actingAs($editor)->get('/admin/submissions')->assertOk();
        $this->actingAs($editor)->get('/admin/partners')->assertForbidden();
        $this->actingAs($editor)->get('/admin/roles-permissions')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => Role::SuperAdmin]))->get('/admin/roles-permissions')->assertOk();
    }

    public function test_retention_purge_and_anonymisation(): void
    {
        $old = ContactMessage::query()->create([
            'locale' => 'en', 'status' => 'closed', 'subject' => 'other', 'full_name' => 'Old Person',
            'email' => 'old@example.org', 'message' => 'Personal content', 'consent_at' => now(),
        ]);
        $old->forceFill(['updated_at' => now()->subYears(3)])->saveQuietly();

        $recent = ContactMessage::query()->create([
            'locale' => 'en', 'status' => 'closed', 'subject' => 'other', 'full_name' => 'Recent Person',
            'email' => 'recent@example.org', 'message' => 'Personal content', 'consent_at' => now(),
        ]);

        $result = app(DataRetention::class)->purge();

        $this->assertSame(1, $result['contact_messages']);
        $this->assertSame(DataRetention::ANONYMISED, $old->fresh()->full_name);
        $this->assertStringNotContainsString('old@example.org', $old->fresh()->email);
        $this->assertSame('Recent Person', $recent->fresh()->full_name);

        app(DataRetention::class)->anonymise($recent);
        $this->assertSame(DataRetention::ANONYMISED, $recent->fresh()->full_name);
    }
}
