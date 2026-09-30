<?php

namespace App\Models;

use App\Enums\FundingType;
use App\Enums\ProjectStage;
use App\Enums\SubmissionStatus;
use App\Models\Concerns\HasReference;
use App\Support\Countries;
use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * File received through "Submit a Project" (§6.3) and processed through
 * the workflow of §6.4.
 */
class Submission extends Model
{
    use HasReference;

    protected $fillable = [
        'reference', 'locale', 'status', 'full_name', 'organization', 'email', 'phone', 'country',
        'position', 'project_name', 'project_country', 'sector_id', 'description', 'stage',
        'investment_amount', 'currency', 'funding_type', 'timeline', 'consent_at', 'certified_at',
        'assigned_to', 'internal_notes', 'ip_address', 'user_agent',
    ];

    public static function referencePrefix(): string
    {
        return 'S';
    }

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
            'stage' => ProjectStage::class,
            'funding_type' => FundingType::class,
            'investment_amount' => 'decimal:2',
            'consent_at' => 'datetime',
            'certified_at' => 'datetime',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SubmissionDocument::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function amountLabel(?string $locale = null): string
    {
        return Money::format($this->investment_amount, $this->currency, $locale);
    }

    public function countryName(?string $locale = null): string
    {
        return Countries::name($this->project_country, $locale);
    }
}
