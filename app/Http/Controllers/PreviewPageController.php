<?php

namespace App\Http\Controllers;

use App\Services\CmsDraftService;
use Illuminate\Http\Request;

class PreviewPageController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! $request->user()) {
            return redirect()->route('filament.admin.auth.login')->header('Cache-Control', 'no-store, private');
        }
        abort_unless($request->user()->is_admin, 403);
        $payload = app(CmsDraftService::class)->build();

        return response()->view('landing', [
            'settings' => $payload['settings'], 'slides' => $payload['slides'], 'portfolio' => $payload['portfolio'], 'isPreview' => true,
        ])->header('Cache-Control', 'no-store, private')->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
