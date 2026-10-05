<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\SiteSetting;
use App\Models\Slide;

class CmsDraftService
{
    public function build(): array
    {
        $settings = SiteSetting::findOrFail(1)->draft_payload;
        // Backfill drafts that predate the named-contact / second-number fields.
        $settings['contact']['whatsapp_name'] = $settings['contact']['whatsapp_name'] ?? null;
        $settings['contact']['whatsapp_number_2'] = $settings['contact']['whatsapp_number_2'] ?? null;
        $settings['contact']['whatsapp_name_2'] = $settings['contact']['whatsapp_name_2'] ?? null;
        $settings['contact']['phone_display_2'] = $settings['contact']['phone_display_2'] ?? null;
        foreach ($settings['services']['items'] as &$item) {
            if (! empty($item['image_media_id'])) {
                $item['image_url'] = MediaAsset::find($item['image_media_id'])?->url;
            }
        }
        unset($item);
        if (! empty($settings['contact']['background_media_id'])) {
            $settings['contact']['background_url'] = MediaAsset::find($settings['contact']['background_media_id'])?->url;
        }
        $settings['seo']['og_image_url'] = MediaAsset::find($settings['seo']['og_image_media_id'] ?? null)?->url;
        $slides = [];
        foreach (['hero', 'about'] as $location) {
            $slides[$location] = Slide::with('mediaAsset')->where('location', $location)->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()->map(function ($slide) {
                $data = $slide->only(['id', 'media_asset_id', 'alt_text', 'position_x', 'position_y', 'sort_order', 'is_active']);
                $data['image_url'] = $slide->mediaAsset?->url ?? $slide->image_url;

                return $data;
            })->all();
        }
        $projects = PortfolioProject::with(['category', 'coverMediaAsset'])->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $categories = PortfolioCategory::whereIn('id', $projects->pluck('category_id'))->orderBy('sort_order')->orderBy('id')->get()->map(fn ($c) => $c->only(['id', 'name', 'slug', 'sort_order']))->all();
        $projectData = $projects->map(function ($project) {
            return [...$project->only(['id', 'title', 'category_id', 'cover_media_asset_id', 'alt_text', 'caption', 'sort_order', 'is_active']),
                'category_slug' => $project->category->slug, 'category_name' => $project->category->name, 'image_url' => $project->coverMediaAsset?->url ?? $project->image_url];
        })->all();
        foreach (['logo_light', 'logo_dark', 'favicon'] as $key) {
            $settings['identity'][$key.'_url'] = MediaAsset::find($settings['identity'][$key.'_media_id'] ?? null)?->url;
        }
        $payload = ['schema_version' => 1, 'settings' => $settings, 'slides' => $slides, 'portfolio' => ['categories' => $categories, 'projects' => $projectData]];
        $ids = app(MediaService::class)->referencedIds($payload);
        $payload['media'] = MediaAsset::whereIn('id', $ids)->orderBy('id')->get()->map(fn ($m) => [...$m->only(['id', 'path', 'thumbnail_path', 'mime_type', 'width', 'height', 'alt_text']), 'url' => $m->url])->all();

        return $payload;
    }
}
