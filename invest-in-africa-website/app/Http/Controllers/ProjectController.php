<?php

namespace App\Http\Controllers;

use App\Enums\FundingType;
use App\Enums\InvestorType;
use App\Enums\ProjectStage;
use App\Enums\ProjectStatus;
use App\Enums\Region;
use App\Models\Project;
use App\Models\Sector;
use App\Support\Countries;
use App\Support\Locales;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * "Invest in a Project" — catalogue, combinable filters reflected in the URL,
 * project sheet (§6.2).
 */
class ProjectController
{
    /** Investment ranges used by the catalogue filter. */
    public const RANGES = [
        'under-1m' => [0, 1_000_000],
        '1m-5m' => [1_000_000, 5_000_000],
        '5m-20m' => [5_000_000, 20_000_000],
        'over-20m' => [20_000_000, null],
    ];

    public function __construct(private readonly Seo $seo) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'size:2'],
            'region' => ['nullable', 'in:'.implode(',', array_column(Region::cases(), 'value'))],
            'sector' => ['nullable', 'string', 'max:48'],
            'stage' => ['nullable', 'in:'.implode(',', array_column(ProjectStage::cases(), 'value'))],
            'range' => ['nullable', 'in:'.implode(',', array_keys(self::RANGES))],
            'funding' => ['nullable', 'in:'.implode(',', array_column(FundingType::cases(), 'value'))],
            'sort' => ['nullable', 'in:recent,amount_desc,amount_asc'],
        ]);

        $query = Project::published()->with('sector');

        $this->applyFilters($query, $filters);

        match ($filters['sort'] ?? 'recent') {
            'amount_desc' => $query->orderByDesc('investment_amount'),
            'amount_asc' => $query->orderBy('investment_amount'),
            default => $query->orderByDesc('is_featured')->latest('published_at'),
        };

        $this->seo->page('invest')
            ->crumb(__('site.nav.get_involved'), lroute('get-involved'))
            ->crumb(__('site.nav.invest'));

        if (array_filter($filters)) {
            $this->seo->noindex = true;
        }

        $publishedCountries = Project::published()->distinct()->pluck('country')->all();

        return view('projects.index', [
            'projects' => $query->paginate(12)->withQueryString(),
            'filters' => $filters,
            'activeFilters' => count(array_filter(array_diff_key($filters, ['sort' => 1]))),
            'countries' => array_intersect_key(Countries::all(), array_flip($publishedCountries)),
            'sectors' => Sector::active()->get(),
            'ranges' => array_keys(self::RANGES),
        ]);
    }

    public function show(Project $project): View|RedirectResponse
    {
        $locale = Locales::current();

        // Missing translation → parent section in the chosen language, with an explicit message (§7.3).
        if (! $project->isTranslatedIn($locale, ['title', 'summary'])) {
            return redirect()->to(lroute('invest'))->with('notice', __('site.translation_unavailable'));
        }

        $project->load(['sector', 'domain']);

        $this->seo->title($project->tr('title'))
            ->description($project->tr('summary'))
            ->image($project->coverUrl())
            ->alternates(collect(Locales::codes())->mapWithKeys(fn ($l) => [$l => $project->url($l)])->all())
            ->crumb(__('site.nav.get_involved'), lroute('get-involved'))
            ->crumb(__('site.nav.invest'), lroute('invest'))
            ->crumb($project->reference);

        return view('projects.show', [
            'project' => $project,
            'related' => Project::published()
                ->whereKeyNot($project->id)
                ->where(fn ($q) => $q->where('sector_id', $project->sector_id)->orWhere('region', $project->region))
                ->with('sector')
                ->take(3)
                ->get(),
            'investorTypes' => InvestorType::options(),
            'countries' => Countries::all(),
            'isOpen' => $project->isOpenForInterest(),
            'statusClosed' => $project->status === ProjectStatus::Closed,
        ]);
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if ($term = trim($filters['q'] ?? '')) {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
            $query->where(fn (Builder $q) => $q
                ->where('reference', 'like', $like)
                ->orWhere('title', 'like', $like)
                ->orWhere('summary', 'like', $like));
        }

        if ($country = $filters['country'] ?? null) {
            $query->where('country', strtoupper($country));
        }

        if ($region = $filters['region'] ?? null) {
            $query->where('region', $region);
        }

        if ($sector = $filters['sector'] ?? null) {
            $query->whereHas('sector', fn (Builder $q) => $q->where('code', $sector));
        }

        if ($stage = $filters['stage'] ?? null) {
            $query->where('stage', $stage);
        }

        if ($funding = $filters['funding'] ?? null) {
            $query->where('funding_type', $funding);
        }

        if ($range = $filters['range'] ?? null) {
            [$min, $max] = self::RANGES[$range];
            $query->where('investment_amount', '>=', $min);

            if ($max !== null) {
                $query->where('investment_amount', '<', $max);
            }
        }
    }
}
