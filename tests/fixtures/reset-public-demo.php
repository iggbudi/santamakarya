<?php

use App\Models\PageVersion;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\PublicationService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (! $app->environment('local')) {
    throw new RuntimeException('Local only');
}
$setting = SiteSetting::with('publishedVersion')->findOrFail(1);
if (($setting->publishedVersion->payload['settings']['identity']['brand_name'] ?? '') === 'SANTAMA KARYA UJI') {
    $version = PageVersion::where('payload->settings->identity->brand_name', '!=', 'SANTAMA KARYA UJI')->orderByDesc('id')->firstOrFail();
    app(PublicationService::class)->restore($version, User::where('is_admin', true)->firstOrFail());
    echo "Public demo marker restored to previous real content.\n";
} else {
    echo "Public content already has no demo marker.\n";
}
