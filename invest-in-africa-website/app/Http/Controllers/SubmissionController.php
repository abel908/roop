<?php

namespace App\Http\Controllers;

use App\Enums\FundingType;
use App\Enums\ProjectStage;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Sector;
use App\Models\Submission;
use App\Services\DocumentScanner;
use App\Services\Notifier;
use App\Support\Countries;
use App\Support\Locales;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** "Submit a Project" — three-step institutional form (§6.3). */
class SubmissionController
{
    public function __construct(private readonly Seo $seo) {}

    public function create(): View
    {
        $this->seo->page('submit')
            ->crumb(__('site.nav.get_involved'), lroute('get-involved'))
            ->crumb(__('site.nav.submit'));

        return view('submissions.create', [
            'countries' => Countries::all(),
            'africanCountries' => Countries::african(),
            'sectors' => Sector::options(),
            'stages' => ProjectStage::options(),
            'fundingTypes' => FundingType::options(),
            'currencies' => config('site.currencies'),
            'uploads' => config('site.uploads'),
        ]);
    }

    public function store(StoreSubmissionRequest $request, DocumentScanner $scanner, Notifier $notifier): RedirectResponse
    {
        $files = $request->file('documents', []);

        foreach ($files as $i => $file) {
            if (! $scanner->isClean($file)) {
                throw ValidationException::withMessages(["documents.$i" => __('forms.file_rejected')]);
            }
        }

        $submission = DB::transaction(function () use ($request, $files) {
            $submission = Submission::create([
                ...$request->safe()->except(['documents', 'consent', 'certify']),
                'locale' => Locales::current(),
                'consent_at' => now(),
                'certified_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            ]);

            foreach ($files as $file) {
                // Renamed, stored outside the public space (§11.3).
                $extension = strtolower($file->getClientOriginalExtension());
                $path = $file->storeAs("submissions/{$submission->reference}", Str::uuid().'.'.$extension, 'local');

                $submission->documents()->create([
                    'original_name' => Str::limit($file->getClientOriginalName(), 250, ''),
                    'path' => $path,
                    'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                    'size' => $file->getSize(),
                ]);
            }

            return $submission;
        });

        $notifier->submissionReceived($submission);

        return redirect()->to(lroute('confirmation'))->with('confirmation', [
            'type' => 'submission',
            'reference' => $submission->reference,
            'email' => $submission->email,
        ]);
    }
}
