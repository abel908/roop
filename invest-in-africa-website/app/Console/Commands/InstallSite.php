<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\ContentTranslation;
use App\Models\User;
use App\Services\IndexNow;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * One-command, idempotent installation and update: safe to run on every
 * deployment or container start. Nothing has to be done by hand.
 */
class InstallSite extends Command
{
    protected $signature = 'site:install {--force : Run in production without confirmation}';

    protected $description = 'Install or update the site: database, first Super Admin, storage link, editable texts, image variants, caches';

    public function handle(): int
    {
        $this->components->info('The Invest In Africa Initiative — installation / update');

        $this->step('Database migrations', fn () => Artisan::call('migrate', ['--force' => true]));
        $this->step('Reference data (areas, sectors, settings, menus)', fn () => Artisan::call('db:seed', ['--force' => true]));
        $this->step('First Super Admin', fn () => $this->ensureSuperAdmin());
        $this->step('Public storage link', fn () => File::exists(public_path('storage')) ?: Artisan::call('storage:link'));
        $this->step('Editable texts', fn () => ContentTranslation::syncFromFiles());
        $this->step('AVIF / WebP image variants', fn () => Artisan::call('media:optimize'));

        if (app()->isProduction()) {
            $this->step('Caches (configuration, routes, views, back-office)', function () {
                Artisan::call('optimize');
                Artisan::call('filament:optimize');
            });

            if (IndexNow::enabled()) {
                $this->step('Notify search engines (IndexNow)', fn () => Artisan::call('seo:indexnow'));
            }
        }

        $this->components->info('Done.');

        return self::SUCCESS;
    }

    private function step(string $label, callable $callback): void
    {
        $this->components->task($label, function () use ($callback) {
            $callback();

            return true;
        });
    }

    /**
     * Creates the first Super Admin when none exists. The generated password
     * is written once to a private file and shown in the logs.
     */
    private function ensureSuperAdmin(): void
    {
        if (User::query()->where('role', Role::SuperAdmin)->exists()) {
            return;
        }

        $email = config('site.admin.email') ?: 'admin@'.(parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost');
        $password = config('site.admin.password') ?: Str::password(20);

        User::query()->updateOrCreate(['email' => $email], [
            'name' => config('site.admin.name'),
            'password' => $password,
            'role' => Role::SuperAdmin,
            'locale' => 'fr',
            'is_active' => true,
        ]);

        $file = storage_path('app/private/first-admin.txt');
        File::put($file, 'Back-office: '.rtrim(config('app.url'), '/')."/admin\nEmail: $email\nPassword: $password\n\nChange this password and enable two-factor authentication after your first login, then delete this file.\n");
        @chmod($file, 0600);

        $this->newLine();
        $this->components->warn("Super Admin created — $email / $password (also saved in storage/app/private/first-admin.txt)");
    }
}
