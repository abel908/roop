<?php

use App\Http\Controllers\SeoController;
use App\Models\ContentTranslation;
use App\Services\Backup;
use App\Services\DataRetention;
use App\Services\ImageOptimizer;
use App\Services\IndexNow;
use App\Support\Locales;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('content:sync', function () {
    $count = ContentTranslation::syncFromFiles();
    $this->info("$count new text key(s) registered for editing in the back-office.");
})->purpose('Register the texts of lang/*.php in the back-office (Contents & translations)');

Artisan::command('media:optimize {--force : Regenerate existing variants}', function (ImageOptimizer $optimizer) {
    $paths = collect(Storage::disk('public')->allFiles())
        ->reject(fn (string $path) => str_contains($path, 'optimized/'))
        ->filter(fn (string $path) => ImageOptimizer::supports($path))
        ->filter(fn (string $path) => $this->option('force') || ! ImageOptimizer::manifest($path));

    $this->withProgressBar($paths, fn (string $path) => $optimizer->optimize($path));
    $this->newLine();
    $this->info($paths->count().' image(s) optimised (AVIF + WebP).');
})->purpose('Generate the AVIF / WebP responsive variants of every uploaded image (§10.4)');

Artisan::command('data:purge', function (DataRetention $retention) {
    foreach ($retention->purge() as $type => $count) {
        $this->line("$type: $count");
    }
})->purpose('Apply the personal data retention periods (§11.2)');

Artisan::command('site:backup {--show-password : Display the archive password}', function () {
    if ($this->option('show-password')) {
        $this->line(Backup::password());

        return;
    }

    $backup = new Backup;
    $archive = $backup->run();
    $pruned = $backup->prune(config('site.backup.keep_days'));
    $this->info('Encrypted backup created: '.$archive." ($pruned old archive(s) removed)");
})->purpose('Encrypted backup of the database and stored files (§11.1)');

// Scheduler — run `php artisan schedule:run` every minute (cron) in production.
Schedule::command('site:backup')->dailyAt('02:30')->onOneServer();
Schedule::command('data:purge')->dailyAt('03:30')->onOneServer();
Schedule::command('queue:prune-failed --hours=720')->weekly();

Artisan::command('seo:indexnow', function () {
    $urls = [];
    foreach (Locales::codes() as $locale) {
        $xml = app(SeoController::class)->sitemap($locale)->getContent();
        preg_match_all('#<loc>([^<]+)</loc>#', $xml, $matches);
        $urls = array_merge($urls, $matches[1]);
    }
    $ok = app(IndexNow::class)->submit($urls);
    $this->line(count($urls).' URL(s) '.($ok ? 'submitted to IndexNow.' : 'not submitted (IndexNow disabled or unreachable).'));
})->purpose('Notify search engines of every page of the site (IndexNow)');

// Weekly full notification in production, in addition to the per-page notifications.
Schedule::command('seo:indexnow')->weekly()->when(fn () => IndexNow::enabled());
