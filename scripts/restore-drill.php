<?php

use App\Http\Controllers\LandingPageController;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\BackupArchive;
use App\Services\CmsDraftService;
use App\Services\MysqlBackup;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

// Imports precede bootstrap statements so aliases also apply during bootstrap.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$stage = 'verify';
$temporaryAccounts = [];
$pdo = null;
$exitCode = 1;

try {
    if (! $app->environment('local')) {
        throw new RuntimeException('Drill ini hanya untuk APP_ENV=local.');
    }
    $bundle = realpath($argv[1] ?? '');
    if (! $bundle || ! is_dir($bundle)) {
        throw new RuntimeException('Berikan direktori backup sebagai argumen.');
    }
    $archive = app(BackupArchive::class);
    $manifest = $archive->verify($bundle);
    $originalConnection = config('database.connections.mysql');
    if ($manifest['source_database'] !== $originalConnection['database']) {
        throw new RuntimeException('Backup bukan dari database lokal yang sedang diverifikasi.');
    }
    $sql = file_get_contents($bundle.'/database.sql');
    if (preg_match('/^\s*(?:USE\s|CREATE\s+DATABASE\s|DROP\s+DATABASE\s|SOURCE\s|\\\\!)/mi', $sql)) {
        throw new RuntimeException('Dump tidak boleh mengganti database atau menjalankan perintah client.');
    }
    $sourceState = app(MysqlBackup::class)->fingerprints();
    $sourcePointer = SiteSetting::findOrFail(1)->published_version_id;
    $sourceHtml = app(LandingPageController::class)()->getContent();
    $rootCredentials = json_decode(file_get_contents(base_path('.runtime/database-credentials.json')), true, 512, JSON_THROW_ON_ERROR);
    $adminConnection = [...$originalConnection, 'username' => 'root', 'password' => $rootCredentials['root']];
    $pdo = new PDO('mysql:host='.$adminConnection['host'].';port='.$adminConnection['port'], 'root', $adminConnection['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $database = 'santama_restore_'.gmdate('Ymd_His').'_'.bin2hex(random_bytes(4));
    if ($database === $originalConnection['database'] || ! preg_match('/^santama_restore_[a-z0-9_]+$/', $database)) {
        throw new RuntimeException('Nama database pemulihan tidak aman.');
    }
    $directory = base_path('.runtime/restore-drills/'.$database);
    if (file_exists($directory)) {
        throw new RuntimeException('Direktori drill sudah ada.');
    }
    // Validate/extract the entire zip before allocating/importing a database.
    $archive->extract($bundle, $directory.'/media');
    $pdo->exec('CREATE DATABASE `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $restoreUsername = 'santama_drill_'.bin2hex(random_bytes(6));
    $restorePassword = bin2hex(random_bytes(24));
    foreach (['localhost', '127.0.0.1'] as $host) {
        $accountSql = $pdo->quote($restoreUsername).'@'.$pdo->quote($host);
        $pdo->exec('CREATE USER '.$accountSql.' IDENTIFIED BY '.$pdo->quote($restorePassword));
        $temporaryAccounts[] = $accountSql;
        $pdo->exec('GRANT ALL PRIVILEGES ON `'.$database.'`.* TO '.$accountSql);
    }
    // Root provisions only the new schema/account; SQL runs with schema-scoped grants.
    $restoreConnection = [...$originalConnection, 'username' => $restoreUsername, 'password' => $restorePassword, 'database' => $database];
    $input = fopen($bundle.'/database.sql', 'rb');
    $stage = 'import';
    try {
        app(MysqlBackup::class)->runClient('mysql', $restoreConnection, ['--binary-mode', '--local-infile=0', $database], $input);
    } finally {
        fclose($input);
    }
    $stage = 'check';
    config(['database.connections.mysql' => $restoreConnection, 'filesystems.disks.public.root' => $directory.'/media']);
    DB::purge('mysql');
    Storage::forgetDisk('public');
    $checks = [];
    $checks['database_records_match_backup'] = app(MysqlBackup::class)->fingerprints() === $manifest['tables'];
    $checks['published_version_pointer'] = SiteSetting::findOrFail(1)->published_version_id === $manifest['published_version_id'];
    $checks['published_html_matches_source'] = app(LandingPageController::class)()->getContent() === $sourceHtml;
    $checks['restored_media_checksums'] = true;
    foreach ($manifest['media_files'] as $path => $hash) {
        if (! Storage::disk('public')->exists($path) || ! hash_equals($hash, hash_file('sha256', Storage::disk('public')->path($path)))) {
            $checks['restored_media_checksums'] = false;
        }
    }
    $draft = app(CmsDraftService::class)->build();
    $checks['hero_and_about_active'] = count($draft['slides']['hero']) > 0 && count($draft['slides']['about']) > 0;
    $checks['portfolio_present'] = count($draft['portfolio']['projects']) > 0;
    $checks['draft_logos_present'] = ! empty($draft['settings']['identity']['logo_light_url']) && ! empty($draft['settings']['identity']['logo_dark_url']);
    $accountFile = base_path('.runtime/admin-account.json');
    if (! is_file($accountFile)) {
        throw new RuntimeException('Akun admin lokal dibutuhkan untuk verifikasi login hasil pemulihan.');
    }
    $account = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($accountFile)), true, 512, JSON_THROW_ON_ERROR);
    $admin = User::where('email', $account['email'])->first();
    $checks['admin_password_and_panel_access'] = $admin && Hash::check($account['password'], $admin->password) && $admin->canAccessPanel(Filament\Facades\Filament::getPanel('admin'));
    config(['database.connections.mysql' => $originalConnection]);
    DB::purge('mysql');
    $checks['source_database_unchanged'] = app(MysqlBackup::class)->fingerprints() === $sourceState && SiteSetting::findOrFail(1)->published_version_id === $sourcePointer;
    $report = ['tested_at' => gmdate(DATE_ATOM), 'backup' => $bundle, 'restore_database' => $database, 'restore_storage' => $directory.'/media', 'media_file_count' => count($manifest['media_files']), 'checks' => $checks, 'success' => ! in_array(false, $checks, true)];
    file_put_contents($directory.'/report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    $exitCode = $report['success'] ? 0 : 1;
} catch (Throwable $e) {
    // Do not echo exception details that may contain database connection credentials.
    fwrite(STDERR, 'Restore drill gagal ('.get_class($e).', stage: '.$stage.'). Periksa backup/client/konfigurasi lokal.'.PHP_EOL);
} finally {
    foreach ($temporaryAccounts as $accountSql) {
        try {
            $pdo->exec('DROP USER '.$accountSql);
        } catch (Throwable) {
            fwrite(STDERR, 'Akun drill sementara gagal dibersihkan; periksa akun santama_drill_* pada instance lokal.'.PHP_EOL);
            $exitCode = 1;
        }
    }
}
exit($exitCode);
