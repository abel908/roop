<?php

namespace App\Models;

use App\Enums\InvestorType;
use App\Enums\RequestStatus;
use App\Models\Concerns\HasReference;
use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Investor's expression of interest in a published project (§6.2). */
class InterestExpression extends Model
{
    use HasReference;

    protected $fillable = [
        'reference', 'project_id', 'locale', 'status', 'full_name', 'organization', 'investor_type',
        'email', 'phone', 'country', 'amount', 'message', 'consent_at', 'assigned_to',
        'internal_notes', 'ip_address',
    ];

    public static function referencePrefix(): string
    {
        return 'I';
    }

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'investor_type' => InvestorType::class,
            'amount' => 'decimal:2',
            'consent_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function amountLabel(?string $locale = null): string
    {
        return $this->amount ? Money::format($this->amount, $this->project?->currency ?? 'USD', $locale) : '—';
    }
}
