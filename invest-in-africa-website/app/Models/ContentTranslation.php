<?php

namespace App\Models;

use App\Models\Concerns\HasRevisions;
use App\Support\DatabaseTranslationLoader;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;

/**
 * Back-office overrides of the texts stored in lang/{en,fr,zh}/*.php.
 * Pages, interface labels, form messages and email templates are all
 * editable here, the three languages side by side (§8.1).
 */
class ContentTranslation extends Model
{
    use HasRevisions;

    protected $fillable = ['group', 'key', 'en', 'fr', 'zh', 'updated_by'];

    protected static function booted(): void
    {
        static::saved(fn (self $row) => static::flush($row->group));
        static::deleted(fn (self $row) => static::flush($row->group));
    }

    public static function cacheKey(string $group, string $locale): string
    {
        return "content_translations.$group.$locale";
    }

    public static function flush(string $group): void
    {
        foreach (Locales::codes() as $locale) {
            Cache::forget(static::cacheKey($group, $locale));
        }
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Default text shipped in lang/{locale}/{group}.php. */
    public function fileValue(string $locale): ?string
    {
        static $loaded = [];
        $loaded[$locale][$this->group] ??= (new FileLoader(new Filesystem, lang_path()))->load($locale, $this->group);
        $value = Arr::get($loaded[$locale][$this->group], $this->key);

        return is_string($value) ? $value : null;
    }

    /** Text actually displayed: back-office override, otherwise file default. */
    public function effective(string $locale): ?string
    {
        return filled($this->{$locale}) ? $this->{$locale} : $this->fileValue($locale);
    }

    public function isMissing(string $locale): bool
    {
        return blank($this->effective($locale));
    }

    /**
     * Registers every text key found in the English files so it can be edited.
     * Returns the number of keys created.
     */
    public static function syncFromFiles(): int
    {
        $created = 0;
        $loader = new FileLoader(new Filesystem, lang_path());

        foreach (glob(lang_path('en/*.php')) as $file) {
            $group = basename($file, '.php');

            if (in_array($group, DatabaseTranslationLoader::LOCKED_GROUPS, true)) {
                continue;
            }

            foreach (Arr::dot($loader->load('en', $group)) as $key => $value) {
                if (! is_string($value)) {
                    continue;
                }

                $row = static::query()->firstOrCreate(['group' => $group, 'key' => (string) $key]);
                $created += (int) $row->wasRecentlyCreated;
            }
        }

        return $created;
    }
}
