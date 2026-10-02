<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\Slide;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SlideshowService
{
    public function save(string $location, array $items, int $interval): void
    {
        Validator::make(compact('location', 'items', 'interval'), [
            'location' => 'required|in:hero,about', 'interval' => 'required|integer|between:3000,10000',
            'items' => 'array|max:30', 'items.*.id' => 'nullable|integer|distinct',
            'items.*.media_asset_id' => 'nullable|integer|exists:media_assets,id',
            'items.*.alt_text' => 'nullable|string|max:255', 'items.*.position_x' => 'nullable|integer|between:0,100',
            'items.*.position_y' => 'nullable|integer|between:0,100', 'items.*.is_active' => 'required|boolean',
        ])->validate();
        if (count(array_filter($items, fn ($i) => $i['is_active'])) > 10) {
            throw ValidationException::withMessages(['items' => 'Maksimal 10 foto aktif.']);
        }
        DB::transaction(function () use ($location, $items, $interval) {
            $settings = SiteSetting::lockForUpdate()->findOrFail(1);
            $keep = [];
            foreach ($items as $order => $item) {
                $slide = empty($item['id']) ? new Slide(['location' => $location]) : Slide::where('location', $location)->find($item['id']);
                if (! $slide) {
                    throw ValidationException::withMessages(['items' => 'Foto tidak termasuk slideshow ini.']);
                }
                $media = $item['media_asset_id'] ?? null;
                if (! $media && ! $slide->image_url) {
                    throw ValidationException::withMessages(['items' => 'Pilih gambar dari Pustaka Media.']);
                }
                $slide->fill(['media_asset_id' => $media, 'image_url' => $media ? null : $slide->image_url,
                    'alt_text' => $item['alt_text'] ?? '', 'position_x' => $item['position_x'] ?? 50, 'position_y' => $item['position_y'] ?? 50,
                    'sort_order' => $order, 'is_active' => $item['is_active']])->save();
                $keep[] = $slide->id;
            }
            Slide::where('location', $location)->whereNotIn('id', $keep)->delete();
            $draft = $settings->draft_payload;
            $draft[$location]['interval_ms'] = $interval;
            $settings->update(['draft_payload' => $draft]);
        });
    }
}
