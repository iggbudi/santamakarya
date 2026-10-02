<?php

use App\Services\BackupArchive;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

// Integration regression: a disposable probe schema stands in for protected source data.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

try {
    if (! $app->environment('local')) {
        throw new RuntimeException('Local only');
    }
    $bundle = realpath($argv[1] ?? '');
    app(BackupArchive::class)->verify($bundle);
    $connection = config('database.connections.mysql');
    $root = json_decode(file_get_contents(base_path('.runtime/database-credentials.json')), true, 512, JSON_THROW_ON_ERROR);
    $pdo = new PDO('mysql:host='.$connection['host'].';port='.$connection['port'], 'root', $root['root'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $probe = 'santama_restore_guard_'.bin2hex(random_bytes(6));
    $pdo->exec('CREATE DATABASE `'.$probe.'`');
    $pdo->exec('CREATE TABLE `'.$probe.'`.`protected_probe` (id INT PRIMARY KEY)');
    $pdo->exec('INSERT INTO `'.$probe.'`.`protected_probe` VALUES (1)');
    $fixture = base_path('.runtime/restore-guard-'.bin2hex(random_bytes(6)));
    File::copyDirectory($bundle, $fixture);
    file_put_contents($fixture.'/database.sql', "\nDELETE FROM `".$probe."`.`protected_probe`;\n", FILE_APPEND);
    $manifest = json_decode(file_get_contents($fixture.'/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
    $manifest['sha256']['database.sql'] = hash_file('sha256', $fixture.'/database.sql');
    file_put_contents($fixture.'/manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
    $process = new Process([PHP_BINARY, base_path('scripts/restore-drill.php'), $fixture]);
    $process->setTimeout(120)->run();
    $protected = (int) $pdo->query('SELECT COUNT(*) FROM `'.$probe.'`.`protected_probe`')->fetchColumn() === 1;
    if (! $protected || $process->isSuccessful() || ! str_contains($process->getErrorOutput(), 'stage: import')) {
        throw new RuntimeException('Restore did not isolate writes to its target');
    }
    echo "PASS: qualified write outside restore schema denied; protected probe remains unchanged.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'FAIL: restore isolation regression ('.get_class($e).").\n");
    exit(1);
}
