<?php

use App\Models\ContentTranslation;
use Illuminate\Support\Facades\Artisan;

Artisan::command('content:sync', function () {
    $count = ContentTranslation::syncFromFiles();
    $this->info("$count new text key(s) registered for editing in the back-office.");
})->purpose('Register the texts of lang/*.php in the back-office (Contents & translations)');
