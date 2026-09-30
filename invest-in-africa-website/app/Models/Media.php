<?php

namespace App\Models;

use App\Jobs\OptimizeImage;
use App\Models\Concerns\HasTranslations;
use App\Services\ImageOptimizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Media library item (§8.1, §8.2): image, video or document, with an
 * alternative text per language and optimised WebP variants for images.
 */
class Media extends Model
{
    use HasTranslations;

    protected $table = 'media';

    protected array $translatable = ['alt'];

    protected $fillable = ['file', 'type', 'mime_type', 'size', 'width', 'height', 'alt', 'variants', 'poster'];

    protected function casts(): array
    {
        return ['variants' => 'array'];
    }

    protected static function booted(): void
    {
        static::saved(function (self $media) {
            if ($media->type === 'image' && ($media->wasRecentlyCreated || $media->wasChanged('file'))) {
                OptimizeImage::dispatch($media->file, $media->id);
            }
        });

        static::deleted(fn (self $media) => $media->isImage() && app(ImageOptimizer::class)->forget($media->file));
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->file);
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function altText(?string $locale = null): string
    {
        return (string) $this->tr('alt', $locale);
    }
}
