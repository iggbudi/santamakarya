<?php

namespace Tests\Feature\Cms;

use App\Services\BackupArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class BackupArchiveTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = base_path('.runtime/archive-test-'.Str::uuid());
        File::ensureDirectoryExists($this->directory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);
        parent::tearDown();
    }

    private function bundle(string $entry = 'media/photo.png'): void
    {
        file_put_contents($this->directory.'/database.sql', 'CREATE TABLE example (id INT);');
        $zip = new ZipArchive;
        $zip->open($this->directory.'/media.zip', ZipArchive::CREATE);
        $zip->addFromString($entry, 'test image bytes');
        $zip->close();
        file_put_contents($this->directory.'/manifest.json', json_encode([
            'format' => 1, 'source_database' => 'source',
            'sha256' => ['database.sql' => hash_file('sha256', $this->directory.'/database.sql'), 'media.zip' => hash_file('sha256', $this->directory.'/media.zip')],
        ]));
    }

    public function test_backup_integrity_is_checked_before_extracting_media(): void
    {
        $this->bundle();
        app(BackupArchive::class)->extract($this->directory, $this->directory.'/restored');
        $this->assertSame('test image bytes', file_get_contents($this->directory.'/restored/media/photo.png'));
        file_put_contents($this->directory.'/database.sql', 'corrupt', FILE_APPEND);
        $this->expectException(\RuntimeException::class);
        app(BackupArchive::class)->verify($this->directory);
    }

    public function test_restore_refuses_zip_paths_outside_target(): void
    {
        $this->bundle('../escape.png');
        try {
            app(BackupArchive::class)->extract($this->directory, $this->directory.'/restored');
            $this->fail('Traversal accepted');
        } catch (\RuntimeException $e) {
            $this->assertFileDoesNotExist($this->directory.'/escape.png');
            $this->assertDirectoryDoesNotExist($this->directory.'/restored');
        }
    }
}
