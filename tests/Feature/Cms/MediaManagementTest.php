<?php

namespace Tests\Feature\Cms;

use App\Models\MediaAsset;
use App\Models\PageVersion;
use App\Models\SiteSetting;
use App\Models\Slide;
use App\Services\MediaService;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MediaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(CmsSeeder::class);
    }

    public function test_stores_valid_image_and_thumbnail_with_random_name(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('project.png', 800, 600));
        $this->assertSame('image/png', $asset->mime_type);
        $this->assertSame(800, $asset->width);
        $this->assertSame(600, $asset->height);
        $this->assertStringNotContainsString('project.png', $asset->path);
        Storage::disk('public')->assertExists([$asset->path, $asset->thumbnail_path]);
        $this->assertSame(1, MediaAsset::count());
    }

    public function test_rejects_invalid_image_even_with_png_extension(): void
    {
        $this->expectException(ValidationException::class);
        app(MediaService::class)->upload(UploadedFile::fake()->createWithContent('fake.png', '<?php echo 1; ?>'));
    }

    public function test_rejects_oversized_upload(): void
    {
        $this->expectException(ValidationException::class);
        app(MediaService::class)->upload(UploadedFile::fake()->image('large.jpg')->size(5121));
    }

    public function test_preserves_png_transparency(): void
    {
        $image = imagecreatetruecolor(40, 40);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->createWithContent('logo.png', $bytes));
        $stored = imagecreatefromstring(Storage::disk('public')->get($asset->path));
        $this->assertSame(127, imagecolorsforindex($stored, imagecolorat($stored, 0, 0))['alpha']);
        imagedestroy($stored);
    }

    public function test_cannot_delete_media_referenced_by_draft_identity(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('logo.png'));
        $setting = SiteSetting::findOrFail(1);
        $draft = $setting->draft_payload;
        $draft['identity']['logo_light_media_id'] = $asset->id;
        $setting->update(['draft_payload' => $draft]);
        $this->assertTrue(app(MediaService::class)->isReferenced($asset));
        $this->expectException(ValidationException::class);
        app(MediaService::class)->delete($asset);
    }

    public function test_cannot_delete_media_referenced_by_historical_snapshot(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('photo.png'));
        PageVersion::create(['payload' => ['settings' => [], 'slides' => ['about' => [['media_asset_id' => $asset->id]]]]]);
        $this->assertTrue(app(MediaService::class)->isReferenced($asset));
        $this->expectException(ValidationException::class);
        app(MediaService::class)->delete($asset);
    }

    public function test_cannot_delete_media_referenced_by_inactive_slide(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('photo.png'));
        Slide::create(['location' => 'about', 'media_asset_id' => $asset->id, 'is_active' => false]);
        $this->expectException(ValidationException::class);
        app(MediaService::class)->delete($asset);
    }

    public function test_deletes_unreferenced_asset_and_its_files(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('unused.png'));
        $paths = [$asset->path, $asset->thumbnail_path];
        app(MediaService::class)->delete($asset);
        Storage::disk('public')->assertMissing($paths);
        $this->assertDatabaseCount('media_assets', 0);
    }
}
