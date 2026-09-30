<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\SubmissionDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Confidential submission documents: temporary signed link, authorised
 * roles only, every access logged (§11.3).
 */
class DocumentDownloadController
{
    public function __invoke(SubmissionDocument $document): StreamedResponse
    {
        abort_unless(auth()->user()?->canManage('submissions'), 403);
        abort_unless(Storage::disk('local')->exists($document->path), 404);

        ActivityLog::record('document.downloaded', $document->submission, [
            'document' => $document->original_name,
        ]);

        return Storage::disk('local')->download($document->path, $document->original_name, [
            'Content-Type' => $document->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
