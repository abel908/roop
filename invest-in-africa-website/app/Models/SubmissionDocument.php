<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * Confidential attachment: stored on the private disk, only reachable from
 * the back-office through a temporary signed link, every access logged (§11.3).
 */
class SubmissionDocument extends Model
{
    protected $fillable = ['submission_id', 'original_name', 'path', 'mime_type', 'size'];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function temporaryUrl(int $minutes = 15): string
    {
        return URL::temporarySignedRoute('admin.documents.download', now()->addMinutes($minutes), ['document' => $this->id]);
    }

    public function humanSize(): string
    {
        $size = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 1).' '.$units[$i];
    }
}
