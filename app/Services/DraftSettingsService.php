<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DraftSettingsService
{
    public function updateIdentity(array $data): void
    {
        $validated = Validator::make($data, [
            'company_name' => ['required', 'string', 'max:255'],
            'brand_name' => ['required', 'string', 'max:100'],
            'brand_suffix' => ['nullable', 'string', 'max:100'],
            'tagline' => ['required', 'string', 'max:300'],
            'logo_light_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'logo_dark_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'favicon_media_id' => ['nullable', 'integer', 'exists:media_assets,id'],
        ])->validate();
        DB::transaction(function () use ($validated) {
            $settings = SiteSetting::lockForUpdate()->findOrFail(1);
            $draft = $settings->draft_payload;
            $draft['identity'] = [...$draft['identity'], ...$validated];
            $settings->update(['draft_payload' => $draft]);
        });
    }
}
