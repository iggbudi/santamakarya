<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Response;

class LandingPageController extends Controller
{
    public function __invoke(): Response
    {
        $version = SiteSetting::with('publishedVersion')->find(1)?->publishedVersion;
        abort_unless($version, 503, 'Halaman belum tersedia.');

        $payload = $version->payload;

        return response()->view('landing', [
            'settings' => $payload['settings'],
            'slides' => $payload['slides'],
            'portfolio' => $payload['portfolio'],
        ])->header('Cache-Control', 'no-cache, must-revalidate');
    }
}
