<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Services\MediaService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PrepareBrandAssets extends Command
{
    protected $signature = 'cms:prepare-brand';

    protected $description = 'Tambahkan aset logo transparan ke pustaka dan isi pilihan draft yang masih kosong';

    public function handle(MediaService $media): int
    {
        DB::transaction(function () use ($media) {
            $setting = SiteSetting::lockForUpdate()->findOrFail(1);
            $draft = $setting->draft_payload;
            foreach (['light', 'dark'] as $variant) {
                $name = 'santama-logo-'.$variant.'.png';
                $asset = MediaAsset::where('original_name', $name)->first() ?? $media->upload(new UploadedFile(resource_path('branding/logo-'.$variant.'.png'), $name, 'image/png', null, true));
                $key = 'logo_'.$variant.'_media_id';
                if (empty($draft['identity'][$key])) {
                    $draft['identity'][$key] = $asset->id;
                }
                if ($variant === 'light' && empty($draft['identity']['favicon_media_id'])) {
                    $draft['identity']['favicon_media_id'] = $asset->id;
                }
            }
            $setting->update(['draft_payload' => $draft]);
        });
        $this->info('Logo tersedia di Pustaka Media dan identitas draft. Snapshot publik tidak berubah.');

        return self::SUCCESS;
    }
}
