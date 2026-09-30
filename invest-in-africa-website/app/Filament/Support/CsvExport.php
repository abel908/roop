<?php

namespace App\Filament\Support;

use App\Models\ActivityLog;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV exports (Excel compatible: UTF-8 BOM, semicolon separator) of files,
 * expressions of interest and messages (§8.4).
 */
class CsvExport
{
    /** @param array<string, Closure|string> $columns heading => attribute or closure */
    public static function headerAction(string $filename, Closure $query, array $columns): Action
    {
        return Action::make('export')
            ->label(__('admin.export_csv'))
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(fn ($livewire) => self::download(
                $filename,
                $livewire->getFilteredTableQuery()?->get() ?? $query()->get(),
                $columns,
            ));
    }

    /** @param array<string, Closure|string> $columns */
    public static function bulkAction(string $filename, array $columns): BulkAction
    {
        return BulkAction::make('export')
            ->label(__('admin.export_csv'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(fn (Collection $records) => self::download($filename, $records, $columns));
    }

    /** @param array<string, Closure|string> $columns */
    public static function download(string $filename, iterable $records, array $columns): StreamedResponse
    {
        ActivityLog::record('export', null, ['file' => $filename]);

        return response()->streamDownload(function () use ($records, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_keys($columns), ';');

            foreach ($records as $record) {
                fputcsv($out, array_map(function ($column) use ($record) {
                    $value = $column instanceof Closure ? $column($record) : data_get($record, $column);

                    return match (true) {
                        $value instanceof \BackedEnum => $value instanceof HasLabel ? $value->getLabel() : $value->value,
                        $value instanceof \DateTimeInterface => $value->format('Y-m-d H:i'),
                        default => (string) $value,
                    };
                }, $columns), ';');
            }

            fclose($out);
        }, $filename.'-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
