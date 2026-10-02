<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

class MysqlBackup
{
    public const TABLES = ['users', 'site_settings', 'page_versions', 'media_assets', 'slides', 'portfolio_projects', 'portfolio_categories'];

    public function fingerprints(): array
    {
        $result = [];
        foreach (self::TABLES as $table) {
            $rows = DB::table($table)->orderBy('id')->get();
            $result[$table] = ['count' => $rows->count(), 'sha256' => hash('sha256', $rows->toJson())];
        }

        return $result;
    }

    public function create(string $destination): string
    {
        $connection = config('database.connections.'.config('database.default'));
        if (($connection['driver'] ?? '') !== 'mysql' || config('filesystems.disks.public.driver') !== 'local') {
            throw new RuntimeException('Backup ini membutuhkan MySQL dan storage public lokal.');
        }
        File::ensureDirectoryExists($destination, 0700);
        $resolved = str_replace('\\', '/', realpath($destination));
        $public = str_replace('\\', '/', realpath(public_path()));
        if (str_starts_with(strtolower($resolved).'/', strtolower($public).'/')) {
            throw new RuntimeException('Backup tidak boleh berada dalam web root public.');
        }
        $directory = $resolved.'/backup-'.gmdate('Ymd-His').'-'.Str::uuid();
        File::ensureDirectoryExists($directory, 0700);
        DB::transaction(function () use ($connection, $directory) {
            // Serialize snapshot/media reference changes and deletions during capture.
            $setting = SiteSetting::lockForUpdate()->findOrFail(1);
            $state = $this->fingerprints();
            $this->runClient('mysqldump', $connection, ['--single-transaction', '--skip-lock-tables', '--no-tablespaces', '--set-gtid-purged=OFF', '--skip-add-locks', '--result-file='.$directory.'/database.sql', $connection['database']]);
            $zip = new ZipArchive;
            if ($zip->open($directory.'/media.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Arsip backup tidak dapat dibuat.');
            }
            $files = [];
            try {
                $disk = Storage::disk('public');
                foreach ($disk->allFiles() as $path) {
                    if (is_link($disk->path($path))) {
                        throw new RuntimeException('Symlink media tidak didukung oleh backup.');
                    }
                    $files[$path] = hash_file('sha256', $disk->path($path));
                    if (! $zip->addFile($disk->path($path), $path)) {
                        throw new RuntimeException('Media tidak dapat ditambahkan ke backup.');
                    }
                }
                // A valid empty zip also covers an installation with no uploads yet.
                if (! $files) {
                    $zip->addEmptyDir('media');
                }
            } finally {
                if (! $zip->close()) {
                    throw new RuntimeException('Penulisan arsip media gagal.');
                }
            }
            $manifest = ['format' => 1, 'created_at' => gmdate(DATE_ATOM), 'source_database' => $connection['database'], 'published_version_id' => $setting->published_version_id,
                'tables' => $state, 'media_files' => $files,
                'sha256' => ['database.sql' => hash_file('sha256', $directory.'/database.sql'), 'media.zip' => hash_file('sha256', $directory.'/media.zip')]];
            file_put_contents($directory.'/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        });
        app(BackupArchive::class)->verify($directory);

        return $directory;
    }

    public function runClient(string $binary, array $connection, array $arguments, $input = null): void
    {
        $optionFile = tempnam(sys_get_temp_dir(), 'santama-mysql-');
        if (! $optionFile) {
            throw new RuntimeException('File opsi MySQL tidak dapat dibuat.');
        }
        @chmod($optionFile, 0600);
        try {
            $quote = fn ($value) => '"'.str_replace(['\\', '"', "\r", "\n"], ['\\\\', '\\"', '\\r', '\\n'], (string) $value).'"';
            $contents = "[client]\n";
            foreach (['host' => 'host', 'port' => 'port', 'username' => 'user', 'password' => 'password'] as $key => $option) {
                $contents .= $option.'='.$quote($connection[$key])."\n";
            }
            if (! file_put_contents($optionFile, $contents)) {
                throw new RuntimeException('File opsi MySQL gagal ditulis.');
            }
            $process = new Process([config('cms-backup.'.$binary), '--defaults-extra-file='.$optionFile, ...$arguments]);
            $process->setTimeout(120)->setInput($input)->run();
            if (! $process->isSuccessful()) {
                // Avoid printing command configuration, credentials, or SQL contents.
                throw new RuntimeException('Proses '.$binary.' gagal (exit '.$process->getExitCode().'). Periksa akses database dan versi client.');
            }
        } finally {
            @unlink($optionFile);
        }
    }
}
