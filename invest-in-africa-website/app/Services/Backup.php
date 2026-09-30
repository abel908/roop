<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Daily encrypted backup of the database and files (§11.1 "Sauvegardes"):
 * AES-256 archive, kept 30 rolling days. Copy the archives off-site
 * (object storage or another server) with the hosting provider's tools.
 */
class Backup
{
    public function __construct(private readonly ?string $directory = null) {}

    public function directory(): string
    {
        return $this->directory ?? config('site.backup.path');
    }

    public function run(): string
    {
        $password = config('site.backup.password');

        if (! $password) {
            throw new RuntimeException('BACKUP_PASSWORD must be set: backups are always encrypted.');
        }

        File::ensureDirectoryExists($this->directory());
        $archive = $this->directory().'/backup-'.now()->format('Y-m-d-His').'.zip';
        $dump = $this->dumpDatabase();

        $zip = new ZipArchive;
        $zip->open($archive, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->setPassword($password);
        $this->add($zip, $dump, 'database/'.basename($dump), $password);

        foreach (['app/private', 'app/public'] as $folder) {
            $root = storage_path($folder);

            if (! is_dir($root)) {
                continue;
            }

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                $this->add($zip, $file->getPathname(), 'storage/'.$folder.'/'.substr($file->getPathname(), strlen($root) + 1), $password);
            }
        }

        $zip->close();
        @unlink($dump);

        return $archive;
    }

    /** Deletes archives older than the retention period. */
    public function prune(int $days = 30): int
    {
        $deleted = 0;

        foreach (glob($this->directory().'/backup-*.zip') ?: [] as $file) {
            if (filemtime($file) < now()->subDays($days)->getTimestamp()) {
                @unlink($file) && $deleted++;
            }
        }

        return $deleted;
    }

    private function add(ZipArchive $zip, string $path, string $name, string $password): void
    {
        $zip->addFile($path, $name);
        $zip->setEncryptionName($name, ZipArchive::EM_AES_256, $password);
    }

    private function dumpDatabase(): string
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");
        $target = storage_path('app/backup-'.uniqid().'.sql');

        if ($config['driver'] === 'sqlite') {
            DB::connection()->getPdo()->exec('PRAGMA wal_checkpoint');
            copy($config['database'], $target);

            return $target;
        }

        if (in_array($config['driver'], ['mysql', 'mariadb'], true)) {
            $process = new Process([
                'mysqldump', '--single-transaction', '--quick', '--routines',
                '--host='.$config['host'], '--port='.$config['port'], '--user='.$config['username'],
                '--result-file='.$target, $config['database'],
            ], null, ['MYSQL_PWD' => (string) $config['password']]);
            $process->setTimeout(900)->mustRun();

            return $target;
        }

        throw new RuntimeException("Unsupported database driver for backups: {$config['driver']}");
    }
}
