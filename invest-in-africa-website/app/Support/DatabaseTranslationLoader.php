<?php

namespace App\Support;

use App\Models\ContentTranslation;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Decorates the file loader: lang/{locale}/{group}.php provides the default
 * texts, rows edited in the back-office (content_translations) override them.
 */
class DatabaseTranslationLoader implements Loader
{
    /** Groups that are never editable (URL slugs are structural). */
    public const LOCKED_GROUPS = ['routes', 'validation', 'auth', 'pagination', 'passwords', 'admin'];

    private static ?bool $tableExists = null;

    public function __construct(private readonly Loader $files) {}

    public function load($locale, $group, $namespace = null): array
    {
        $lines = $this->files->load($locale, $group, $namespace);

        if (($namespace !== null && $namespace !== '*') || $group === '*'
            || in_array($group, self::LOCKED_GROUPS, true) || ! Locales::isSupported($locale)
            || ! $this->tableExists()) {
            return $lines;
        }

        $overrides = Cache::rememberForever(ContentTranslation::cacheKey($group, $locale), function () use ($group, $locale) {
            return ContentTranslation::query()
                ->where('group', $group)
                ->whereNotNull($locale)
                ->where($locale, '!=', '')
                ->pluck($locale, 'key')
                ->all();
        });

        foreach ($overrides as $key => $value) {
            Arr::set($lines, $key, $value);
        }

        return $lines;
    }

    public function addNamespace($namespace, $hint): void
    {
        $this->files->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path): void
    {
        $this->files->addJsonPath($path);
    }

    public function namespaces(): array
    {
        return $this->files->namespaces();
    }

    private function tableExists(): bool
    {
        if (self::$tableExists === null) {
            try {
                self::$tableExists = Schema::hasTable('content_translations');
            } catch (\Throwable) {
                self::$tableExists = false;
            }
        }

        return self::$tableExists;
    }
}
