<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\Slide;
use App\Services\SlideshowService;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SlideshowManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_reorders_and_archives_slides_without_publishing(): void
    {
        $this->seed(CmsSeeder::class);
        $pointer = SiteSetting::find(1)->published_version_id;
        $items = Slide::where('location', 'hero')->orderByDesc('id')->get()->toArray();
        $items[0]['is_active'] = false;
        app(SlideshowService::class)->save('hero', $items, 4000);
        $this->assertEquals($items[0]['id'], Slide::where('location', 'hero')->orderBy('sort_order')->first()->id);
        $this->assertFalse(Slide::find($items[0]['id'])->is_active);
        $this->assertEquals($pointer, SiteSetting::find(1)->published_version_id);
    }

    public function test_new_slide_requires_uploaded_media(): void
    {
        $this->seed(CmsSeeder::class);
        $this->expectException(ValidationException::class);
        app(SlideshowService::class)->save('about', [['image_url' => 'https://example.com/fake.jpg', 'is_active' => true]], 5000);
    }

    public function test_cannot_move_slide_between_locations(): void
    {
        $this->seed(CmsSeeder::class);
        $this->expectException(ValidationException::class);
        app(SlideshowService::class)->save('about', [Slide::where('location', 'hero')->first()->toArray()], 5000);
    }

    public function test_draft_can_be_empty_without_changing_public_page(): void
    {
        $this->seed(CmsSeeder::class);
        app(SlideshowService::class)->save('about', [], 5000);
        $this->assertEquals(0, Slide::where('location', 'about')->count());
        $this->get('/')->assertOk();
    }

    public function test_rejects_invalid_location_interval_and_active_count(): void
    {
        $this->seed(CmsSeeder::class);
        foreach ([['wrong', [], 5000], ['hero', [], 2999], ['hero', [], 10001], ['hero', array_fill(0, 11, ['is_active' => true]), 5000]] as [$location,$items,$interval]) {
            try {
                app(SlideshowService::class)->save($location, $items, $interval);
                $this->fail('Invalid slideshow accepted');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
    }
}
