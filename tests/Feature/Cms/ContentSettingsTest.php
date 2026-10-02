<?php

namespace Tests\Feature\Cms;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\CmsDraftService;
use App\Services\ContentSettingsService;
use App\Services\MediaService;
use App\Services\PublicationService;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContentSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        Storage::fake('public');
    }

    public function test_contact_update_reaches_all_contact_links_only_after_publish(): void
    {
        $contact = SiteSetting::find(1)->draft_payload['contact'];
        $contact['whatsapp_number'] = '6281234567890';
        $contact['consultation_message'] = 'Halo, proyek A & B?';
        $contact['address'] = 'Alamat kantor baru';
        app(ContentSettingsService::class)->updateContact($contact);
        $this->get('/')->assertDontSee('6281234567890');
        $actor = User::factory()->create();
        $actor->forceFill(['is_admin' => true])->save();
        app(PublicationService::class)->publish($actor);
        $html = $this->get('/')->assertSee('6281234567890')->assertSee('Alamat kantor baru')->getContent();
        preg_match_all('/href="(https:\/\/wa\.me\/[^\"]+)"/', $html, $matches);
        $this->assertCount(6, $matches[1]);
        foreach ($matches[1] as $url) {
            $this->assertStringContainsString('6281234567890', $url);
            $this->assertStringContainsString(rawurlencode('Halo, proyek A & B?'), $url);
        }
        $this->assertStringContainsString('tel:+6281234567890', $html);
    }

    public function test_admin_text_is_escaped_in_preview_and_metadata(): void
    {
        $hero = SiteSetting::find(1)->draft_payload['hero'];
        $hero['headline'] = '<script>alert(1)</script>';
        app(ContentSettingsService::class)->updateContent(['hero' => $hero]);
        $seo = ['title' => '<b>Judul</b>', 'description' => 'Deskripsi "aman" <script>x</script>', 'og_image_media_id' => null];
        app(ContentSettingsService::class)->updateSeo($seo);
        $p = app(CmsDraftService::class)->build();
        $html = view('landing', ['settings' => $p['settings'], 'slides' => $p['slides'], 'portfolio' => $p['portfolio']])->render();
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;b&gt;Judul&lt;/b&gt;', $html);
    }

    public function test_maps_urls_reject_unsafe_or_unrelated_hosts(): void
    {
        $original = SiteSetting::find(1)->draft_payload['contact'];
        foreach (['javascript:alert(1)', 'https://google.com.evil.test/maps/embed', 'https://evil.test/maps/embed', 'http://www.google.com/maps/embed', 'https://www.google.com/other', 'https://user@www.google.com/maps/embed', 'https://www.google.com:8443/maps/embed'] as $url) {
            try {
                app(ContentSettingsService::class)->updateContact([...$original, 'maps_embed_url' => $url]);
                $this->fail('Unsafe embed accepted');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('maps_embed_url', $e->errors());
            }
        }
        $this->assertSame($original, SiteSetting::find(1)->draft_payload['contact']);
    }

    public function test_text_limits_and_card_counts_are_enforced(): void
    {
        $settings = SiteSetting::find(1)->draft_payload;
        foreach ([['hero', 'headline', 101], ['hero', 'description', 801], ['services', 'items.0.title', 81], ['services', 'items.0.description', 301]] as [$group,$key,$length]) {
            $value = $settings[$group];
            data_set($value, $key, str_repeat('x', $length));
            try {
                app(ContentSettingsService::class)->updateContent([$group => $value]);
                $this->fail('Long field accepted');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        foreach ([['title', 71], ['description', 161]] as [$key,$length]) {
            try {
                app(ContentSettingsService::class)->updateSeo([...$settings['seo'], $key => str_repeat('x', $length)]);
                $this->fail('Long SEO accepted');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
        $services = $settings['services'];
        array_pop($services['items']);
        $this->expectException(ValidationException::class);
        app(ContentSettingsService::class)->updateContent(['services' => $services]);
    }

    public function test_new_images_are_resolved_in_draft_and_og_metadata(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('company.png', 600, 900));
        $s = SiteSetting::find(1)->draft_payload;
        $services = $s['services'];
        $services['items'][0]['image_media_id'] = $asset->id;
        app(ContentSettingsService::class)->updateContent(['services' => $services]);
        app(ContentSettingsService::class)->updateSeo([...$s['seo'], 'og_image_media_id' => $asset->id]);
        $p = app(CmsDraftService::class)->build();
        $this->assertSame($asset->url, $p['settings']['services']['items'][0]['image_url']);
        $this->assertSame($asset->url, $p['settings']['seo']['og_image_url']);
        $html = view('landing', ['settings' => $p['settings'], 'slides' => $p['slides'], 'portfolio' => $p['portfolio']])->render();
        $this->assertStringContainsString('property="og:image" content="'.$asset->url.'"', $html);
        $this->assertTrue(app(MediaService::class)->isReferenced($asset));
    }

    public function test_media_validation_serializes_with_deletion_before_saving_content_and_seo(): void
    {
        $asset = app(MediaService::class)->upload(UploadedFile::fake()->image('selected.png'));
        $settings = SiteSetting::find(1)->draft_payload;
        $services = $settings['services'];
        $services['items'][0]['image_media_id'] = $asset->id;
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        foreach (['content', 'seo'] as $editor) {
            $queries = [];
            if ($editor === 'content') {
                app(ContentSettingsService::class)->updateContent(['services' => $services]);
            } else {
                app(ContentSettingsService::class)->updateSeo([...$settings['seo'], 'og_image_media_id' => $asset->id]);
            }
            $lock = array_key_first(array_filter($queries, fn ($sql) => str_contains($sql, 'select') && str_contains($sql, 'site_settings')));
            $exists = array_key_first(array_filter($queries, fn ($sql) => str_contains($sql, 'count') && str_contains($sql, 'media_assets')));
            $this->assertNotNull($lock);
            $this->assertNotNull($exists);
            $this->assertLessThan($exists, $lock, $editor.' must acquire the shared deletion lock before checking the media selection');
        }
    }
}
