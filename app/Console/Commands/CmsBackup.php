<?php

namespace App\Console\Commands;

use App\Services\MysqlBackup;
use Illuminate\Console\Command;
use Throwable;

class CmsBackup extends Command
{
    protected $signature = 'cms:backup {--destination= : Direktori privat tujuan backup}';

    protected $description = 'Backup MySQL dan media lokal beserta manifest checksum; tidak menghapus backup lama';

    public function handle(MysqlBackup $backup): int
    {
        if (! $this->option('destination')) {
            $this->error('Isi --destination dengan direktori privat di luar web root.');

            return self::FAILURE;
        }
        try {
            $directory = $backup->create($this->option('destination'));
            $this->info('Backup selesai: '.$directory);
            $this->line('Salin ke lokasi berbeda dan simpan APP_KEY secara terpisah. Backup lokal ini belum off-site.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
