<?php

namespace Tests\Feature\Cms;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Services\CmsDraftService;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_preparation_is_idempotent_draft_only_and_resolves_logo_urls(): void
    {
        Storage::fake('public');
        $this->seed(CmsSeeder::class);
        $before = SiteSetting::find(1)->published_version_id;
        $this->artisan('cms:prepare-brand')->assertSuccessful();
        $this->artisan('cms:prepare-brand')->assertSuccessful();
        $this->assertEquals(2, MediaAsset::count());
        $this->assertEquals($before, SiteSetting::find(1)->published_version_id);
        $payload = app(CmsDraftService::class)->build();
        $this->assertCount(2, $payload['media']);
        $html = view('landing', ['settings' => $payload['settings'], 'slides' => $payload['slides'], 'portfolio' => $payload['portfolio']])->render();
        $this->assertEquals(3, substr_count($html, 'alt="SANTAMA KARYA"'));
        $this->assertStringContainsString('rel="icon"', $html);
    }
}
