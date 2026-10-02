<?php

namespace Tests\Feature\Cms;

use App\Models\PageVersion;
use App\Models\SiteSetting;
use App\Models\Slide;
use App\Models\User;
use App\Services\MediaService;
use App\Services\PublicationService;
use Database\Seeders\CmsSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_malformed_extra_section_item_is_rejected_without_changing_publication(): void
    {
        $setting = SiteSetting::find(1);
        $pointer = $setting->published_version_id;
        $draft = $setting->draft_payload;
        $draft['services']['items'][] = [];
        $setting->update(['draft_payload' => $draft]);
        try {
            app(PublicationService::class)->publish($this->admin());
            $this->fail('Malformed extra card was published');
        } catch (ValidationException $error) {
            $this->assertNotEmpty($error->errors());
        }
        $this->assertEquals($pointer, $setting->fresh()->published_version_id);
        $this->assertEquals(1, PageVersion::count());
        $this->get('/')->assertOk();
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->forceFill(['is_admin' => true])->save();

        return $u;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        Storage::fake('public');
    }

    public function test_publish_changes_public_snapshot_and_records_actor(): void
    {
        $setting = SiteSetting::find(1);
        $draft = $setting->draft_payload;
        $draft['hero']['headline'] = 'Judul baru dipublikasikan';
        $setting->update(['draft_payload' => $draft]);
        $this->get('/')->assertDontSee('Judul baru dipublikasikan');
        $actor = $this->admin();
        $version = app(PublicationService::class)->publish($actor);
        $this->assertEquals($actor->id, $version->created_by);
        $this->assertEquals($version->id, $setting->fresh()->published_version_id);
        $this->get('/')->assertSee('Judul baru dipublikasikan');
    }

    public function test_restore_creates_new_version_without_overwriting_draft(): void
    {
        $old = PageVersion::first();
        $setting = SiteSetting::find(1);
        $draft = $setting->draft_payload;
        $draft['hero']['headline'] = 'Pekerjaan draft tetap';
        $setting->update(['draft_payload' => $draft]);
        $actor = $this->admin();
        app(PublicationService::class)->publish($actor);
        $restored = app(PublicationService::class)->restore($old, $actor);
        $this->assertNotEquals($old->id, $restored->id);
        $this->assertSame($old->payload, $restored->payload);
        $this->assertSame($draft, $setting->fresh()->draft_payload);
        $this->get('/')->assertDontSee('Pekerjaan draft tetap');
    }

    public function test_empty_slideshow_rejects_publication_without_changing_pointer_or_history(): void
    {
        foreach (['hero', 'about'] as $location) {
            $pointer = SiteSetting::find(1)->published_version_id;
            Slide::where('location', $location)->update(['is_active' => false]);
            try {
                app(PublicationService::class)->publish($this->admin());
                $this->fail('Empty slider published');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('slides.'.$location, $e->errors());
            }
            $this->assertEquals($pointer, SiteSetting::find(1)->published_version_id);
            $this->assertEquals(1, PageVersion::count());
            Slide::where('location', $location)->update(['is_active' => true]);
        }
    }

    public function test_non_admin_cannot_publish_or_restore(): void
    {
        $actor = User::factory()->create();
        foreach (['publish', 'restore'] as $action) {
            try {
                $service = app(PublicationService::class);
                $action === 'publish' ? $service->publish($actor) : $service->restore(PageVersion::first(), $actor);
                $this->fail('Unauthorized action allowed');
            } catch (AuthorizationException $e) {
                $this->assertEquals(1, PageVersion::count());
            }
        }
    }

    public function test_published_historical_media_cannot_be_deleted_after_draft_replacement(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('cover.png'));
        $slide = Slide::where('location', 'about')->first();
        $original = $slide->image_url;
        $slide->update(['media_asset_id' => $asset->id, 'image_url' => null]);
        $actor = $this->admin();
        app(PublicationService::class)->publish($actor);
        $slide->update(['media_asset_id' => null, 'image_url' => $original]);
        app(PublicationService::class)->publish($actor);
        $this->expectException(ValidationException::class);
        app(MediaService::class)->delete($asset);
    }

    public function test_missing_uploaded_file_blocks_publication(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('gone.png'));
        Storage::disk('public')->delete($asset->path);
        Slide::where('location', 'about')->first()->update(['media_asset_id' => $asset->id, 'image_url' => null]);
        $this->expectException(ValidationException::class);
        app(PublicationService::class)->publish($this->admin());
    }

    public function test_failed_pointer_update_rolls_back_new_snapshot(): void
    {
        $actor = $this->admin();
        $pointer = SiteSetting::find(1)->published_version_id;
        DB::unprepared("CREATE TRIGGER reject_pointer BEFORE UPDATE ON site_settings WHEN NEW.published_version_id != OLD.published_version_id BEGIN SELECT RAISE(ABORT, 'simulated pointer failure'); END");
        try {
            app(PublicationService::class)->publish($actor);
            $this->fail('Database trigger did not reject publication');
        } catch (QueryException $e) {
            $this->assertEquals(1, PageVersion::count());
            $this->assertEquals($pointer, SiteSetting::find(1)->published_version_id);
        }
    }

    public function test_invalid_section_and_unsafe_image_url_are_rejected(): void
    {
        $actor = $this->admin();
        $setting = SiteSetting::find(1);
        $original = $setting->draft_payload;
        $bad = $original;
        unset($bad['about']['metrics'][1]);
        $setting->update(['draft_payload' => $bad]);
        try {
            app(PublicationService::class)->publish($actor);
            $this->fail('Broken section accepted');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $setting->update(['draft_payload' => $original]);
        Slide::where('location', 'hero')->first()->update(['image_url' => "https://example.com/x');color:red;/*"]);
        try {
            app(PublicationService::class)->publish($actor);
            $this->fail('Unsafe URL accepted');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->assertEquals(1, PageVersion::count());
    }

    public function test_restore_missing_historical_media_preserves_current_version_and_draft(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('historical.png'));
        $actor = $this->admin();
        $slide = Slide::where('location', 'about')->first();
        $url = $slide->image_url;
        $slide->update(['media_asset_id' => $asset->id, 'image_url' => null]);
        $old = app(PublicationService::class)->publish($actor);
        $slide->update(['media_asset_id' => null, 'image_url' => $url]);
        $current = app(PublicationService::class)->publish($actor);
        $draft = SiteSetting::find(1)->draft_payload;
        Storage::disk('public')->delete($asset->path);
        try {
            app(PublicationService::class)->restore($old, $actor);
            $this->fail('Missing asset restored');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->assertEquals($current->id, SiteSetting::find(1)->published_version_id);
        $this->assertEquals(3, PageVersion::count());
        $this->assertSame($draft, SiteSetting::find(1)->draft_payload);
    }
}
