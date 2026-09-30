<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterestRequest;
use App\Models\Project;
use App\Services\Notifier;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;

/** Expression of interest, automatically linked to the project reference (§6.2). */
class InterestController
{
    public function store(StoreInterestRequest $request, Project $project, Notifier $notifier): RedirectResponse
    {
        abort_unless($project->isOpenForInterest(), 403);

        $interest = $project->interests()->create([
            ...$request->safe()->except('consent'),
            'locale' => Locales::current(),
            'consent_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $notifier->interestReceived($interest);

        return redirect()->to(lroute('confirmation'))->with('confirmation', [
            'type' => 'interest',
            'reference' => $interest->reference,
            'email' => $interest->email,
            'project' => $project->reference,
        ]);
    }
}
