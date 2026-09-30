<?php

namespace App\Models;

use App\Enums\FundingType;
use App\Enums\ProjectStage;
use App\Enums\ProjectStatus;
use App\Enums\Region;
use App\Models\Concerns\HasReference;
use App\Models\Concerns\HasTranslations;
use App\Support\Countries;
use App\Support\Locales;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Investment opportunity published in "Invest in a Project" (§6.2).
 */
class Project extends Model
{
    use HasReference, HasTranslations;

    protected array $translatable = ['title', 'summary', 'description', 'use_of_funds', 'impact', 'timeline'];

    protected $fillable = [
        'reference', 'title', 'summary', 'description', 'use_of_funds', 'impact', 'timeline',
        'country', 'region', 'sector_id', 'domain_id', 'stage', 'investment_amount', 'currency',
        'funding_type', 'status', 'description_on_request', 'cover_image', 'gallery',
        'public_document', 'confidential_document', 'jobs_expected', 'is_featured',
        'is_published', 'published_at', 'submission_id',
    ];

    public static function referencePrefix(): string
    {
        return 'P';
    }

    public static function referencePadding(): int
    {
        return 3;
    }

    protected function casts(): array
    {
        return [
            'stage' => ProjectStage::class,
            'funding_type' => FundingType::class,
            'status' => ProjectStatus::class,
            'region' => Region::class,
            'gallery' => 'array',
            'investment_amount' => 'decimal:2',
            'description_on_request' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $project) {
            if ($project->country) {
                $project->country = strtoupper($project->country);
                $project->region = Countries::regionOf($project->country) ?? $project->region;
            }

            if ($project->is_published && ! $project->published_at) {
                $project->published_at = now();
            }
        });
    }

    /** Public URL segment: the lower-cased reference (e.g. p-2026-001). */
    public function slugKey(): string
    {
        return strtolower($this->reference);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function interests(): HasMany
    {
        return $this->hasMany(InterestExpression::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function url(?string $locale = null): string
    {
        return lroute('project', ['project' => $this->slugKey()], $locale ?? Locales::current());
    }

    public function countryName(?string $locale = null): string
    {
        return Countries::name($this->country, $locale);
    }

    public function amountLabel(?string $locale = null): string
    {
        return Money::format($this->investment_amount, $this->currency, $locale);
    }

    public function amountCompact(?string $locale = null): string
    {
        return Money::compact($this->investment_amount, $this->currency, $locale);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    /** @return array<int, string> */
    public function galleryUrls(): array
    {
        return collect($this->gallery ?? [])->map(fn ($path) => Storage::disk('public')->url($path))->all();
    }

    public function isOpenForInterest(): bool
    {
        return in_array($this->status, [ProjectStatus::Open, ProjectStatus::Funding], true);
    }
}
