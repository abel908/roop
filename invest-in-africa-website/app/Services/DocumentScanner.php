<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Antivirus scan of uploaded documents (§6.3, §11.1). Uses ClamAV when
 * ANTIVIRUS_BINARY is configured (clamdscan recommended on the server).
 */
class DocumentScanner
{
    public function isClean(UploadedFile $file): bool
    {
        $binary = config('site.uploads.antivirus_binary');

        if (! $binary) {
            return true;
        }

        $process = new Process([$binary, '--no-summary', $file->getRealPath()]);
        $process->setTimeout(60);
        $process->run();

        // ClamAV: 0 = clean, 1 = infected, 2 = error.
        if ($process->getExitCode() === 1) {
            Log::warning('Infected upload rejected', ['name' => $file->getClientOriginalName()]);

            return false;
        }

        if ($process->getExitCode() !== 0) {
            Log::error('Antivirus scan failed', ['output' => $process->getErrorOutput()]);

            return false;
        }

        return true;
    }
}
