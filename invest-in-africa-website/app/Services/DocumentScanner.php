<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Antivirus scan of uploaded documents (§6.3, §11.1) with ClamAV: either a
 * clamd daemon over TCP (CLAMD_HOST, used by the Docker stack) or a local
 * binary (ANTIVIRUS_BINARY). Without either, uploads rely on the other
 * controls (whitelist, real MIME type, private storage).
 */
class DocumentScanner
{
    public function isClean(UploadedFile $file): bool
    {
        if ($host = config('site.uploads.clamd_host')) {
            return $this->scanWithDaemon($host, config('site.uploads.clamd_port'), $file->getRealPath());
        }

        if ($binary = config('site.uploads.antivirus_binary')) {
            return $this->scanWithBinary($binary, $file->getRealPath());
        }

        return true;
    }

    /** clamd INSTREAM protocol. */
    private function scanWithDaemon(string $host, int $port, string $path): bool
    {
        $socket = @fsockopen($host, $port, $errno, $error, 5);

        if (! $socket) {
            Log::error('Antivirus daemon unreachable', ['host' => $host, 'error' => $error]);

            return false;
        }

        stream_set_timeout($socket, 60);
        fwrite($socket, "zINSTREAM\0");
        $handle = fopen($path, 'rb');

        while (! feof($handle)) {
            $chunk = fread($handle, 8192);
            fwrite($socket, pack('N', strlen($chunk)).$chunk);
        }

        fclose($handle);
        fwrite($socket, pack('N', 0));
        $reply = trim((string) stream_get_contents($socket), "\0\n ");
        fclose($socket);

        if (str_ends_with($reply, 'OK')) {
            return true;
        }

        Log::warning('Upload rejected by antivirus', ['reply' => $reply]);

        return false;
    }

    private function scanWithBinary(string $binary, string $path): bool
    {
        $process = new Process([$binary, '--no-summary', $path]);
        $process->setTimeout(60);
        $process->run();

        // ClamAV: 0 = clean, 1 = infected, 2 = error.
        if ($process->getExitCode() !== 0) {
            Log::warning('Upload rejected by antivirus', ['exit' => $process->getExitCode(), 'output' => $process->getOutput()]);

            return false;
        }

        return true;
    }
}
