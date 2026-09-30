<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasTranslations;

    protected array $translatable = ['name'];

    protected $fillable = ['code', 'name', 'sort', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    /** @return array<int, string> */
    public static function options(?string $locale = null): array
    {
        return static::active()->get()->mapWithKeys(fn (self $s) => [$s->id => $s->tr('name', $locale)])->all();
    }
}
