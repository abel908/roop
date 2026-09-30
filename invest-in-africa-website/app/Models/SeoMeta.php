<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    use HasTranslations;

    protected array $translatable = ['title', 'description'];

    protected $fillable = ['page_key', 'title', 'description', 'og_image', 'noindex'];

    protected function casts(): array
    {
        return ['noindex' => 'boolean'];
    }
}
