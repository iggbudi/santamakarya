<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use RuntimeException;
use ZipArchive;

class BackupArchive
{
    public function verify(string $directory): array
    {
        $manifest = json_decode(@file_get_contents($directory.'/manifest.json') ?: '', true);
        if (! is_array($manifest) || ($manifest['format'] ?? null) !== 1) {
            throw new RuntimeException('Manifest backup tidak valid.');
        }
        foreach (['database.sql', 'media.zip'] as $file) {
            if (! is_file($directory.'/'.$file) || ! hash_equals($manifest['sha256'][$file] ?? '', hash_file('sha256', $directory.'/'.$file))) {
                throw new RuntimeException('Checksum backup tidak cocok: '.$file);
            }
        }

        return $manifest;
    }

    public function extract(string $directory, string $target): void
    {
        $this->verify($directory);
        if (file_exists($target)) {
            throw new RuntimeException('Storage pemulihan harus belum ada.');
        }
        $zip = new ZipArchive;
        if ($zip->open($directory.'/media.zip') !== true) {
            throw new RuntimeException('Arsip media tidak dapat dibaca.');
        }
        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                $zip->getExternalAttributesIndex($i, $system, $attributes);
                if (! $name || str_contains($name, '\\') || str_contains($name, ':') || str_starts_with($name, '/') || in_array('..', explode('/', $name), true) || (($attributes >> 16) & 0170000) === 0120000) {
                    throw new RuntimeException('Path arsip media tidak aman.');
                }
            }
            File::ensureDirectoryExists($target, 0700);
            if (! $zip->extractTo($target)) {
                throw new RuntimeException('Ekstraksi media gagal.');
            }
        } finally {
            $zip->close();
        }
    }
}
