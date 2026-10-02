<?php

namespace App\Services;

use App\Models\PortfolioProject;
use App\Models\SiteSetting;
use App\Models\Slide;

class DashboardSummary
{
    public function get(): array
    {
        $published = SiteSetting::with('publishedVersion')->find(1)?->publishedVersion;

        return ['projects' => PortfolioProject::where('is_active', true)->count(), 'hero' => Slide::where('location', 'hero')->where('is_active', true)->count(), 'about' => Slide::where('location', 'about')->where('is_active', true)->count(), 'published_version_id' => $published?->id, 'published_at' => $published?->created_at];
    }
}
