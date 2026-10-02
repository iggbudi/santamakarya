<?php

namespace Tests\Feature\Cms;

use App\Filament\Pages\AboutSlideshow;
use App\Filament\Pages\HeroSlideshow;
use App\Filament\Resources\MediaAssets\Pages\ManageMediaAssets;
use App\Filament\Resources\PortfolioProjects\Pages\ManagePortfolioProjects;
use App\Models\MediaAsset;
use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Models\Slide;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DraftEditorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_both_slideshow_forms_save_and_reload_record_ids(): void
    {
        foreach ([HeroSlideshow::class => 'hero', AboutSlideshow::class => 'about'] as $page => $location) {
            Livewire::test($page)->fillForm(['interval' => 6000])->call('save')->assertHasNoFormErrors();
            $this->assertGreaterThan(0, Slide::where('location', $location)->count());
        }
    }

    public function test_project_requires_cover_and_can_be_created_then_archived(): void
    {
        $category = PortfolioCategory::first();
        $data = ['title' => 'Proyek draft baru', 'category_id' => $category->id, 'sort_order' => 2, 'is_active' => true];
        Livewire::test(ManagePortfolioProjects::class)->callAction('create', data: $data)->assertHasFormErrors(['cover_media_asset_id' => 'required']);
        $media = MediaAsset::create(['path' => 'media/test.png', 'mime_type' => 'image/png', 'size_bytes' => 100, 'width' => 10, 'height' => 10, 'original_name' => 'test.png']);
        Livewire::test(ManagePortfolioProjects::class)->callAction('create', data: [...$data, 'cover_media_asset_id' => $media->id])->assertHasNoFormErrors();
        $project = PortfolioProject::where('title', $data['title'])->firstOrFail();
        Livewire::test(ManagePortfolioProjects::class)->callAction(TestAction::make('edit')->table($project), data: [...$data, 'cover_media_asset_id' => $media->id, 'is_active' => false])->assertHasNoFormErrors();
        $this->assertFalse($project->fresh()->is_active);
        $this->get('/')->assertDontSee('Proyek draft baru');
    }

    public function test_media_description_can_be_cleared(): void
    {
        $media = MediaAsset::create(['path' => 'media/test.png', 'mime_type' => 'image/png', 'size_bytes' => 100, 'width' => 10, 'height' => 10, 'original_name' => 'test.png', 'alt_text' => 'Deskripsi lama']);
        Livewire::test(ManageMediaAssets::class)
            ->callAction(TestAction::make('edit')->table($media), data: ['alt_text' => null])->assertHasNoFormErrors();
        $this->assertSame('', $media->fresh()->alt_text);
    }
}
