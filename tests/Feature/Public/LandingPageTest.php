<?php

namespace Tests\Feature\Public;

use App\Models\PageVersion;
use App\Models\SiteSetting;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_seeded_page_shows_requested_portfolio_without_removing_service(): void
    {
        $this->seed(CmsSeeder::class);
        $this->get('/')->assertOk()->assertSee('Berkarya untuk Maju')
            ->assertSee('Hunian Tropis Modern')->assertSee('Rumah Dinas')
            ->assertDontSee('Rumah Dinas &amp; Fasilitas', false)
            ->assertDontSee('data-filter="lainnya"', false);
    }

    public function test_draft_changes_do_not_affect_public_page(): void
    {
        $this->seed(CmsSeeder::class);
        $settings = SiteSetting::firstOrFail();
        $draft = $settings->draft_payload;
        $draft['hero']['headline'] = 'DRAFT RAHASIA';
        $settings->update(['draft_payload' => $draft]);
        $this->get('/')->assertOk()->assertSee('Berkarya untuk Maju')->assertDontSee('DRAFT RAHASIA');
    }

    public function test_public_page_reads_selected_snapshot_not_latest_version(): void
    {
        $this->seed(CmsSeeder::class);
        $payload = SiteSetting::firstOrFail()->publishedVersion->payload;
        $payload['settings']['hero']['headline'] = 'VERSI BELUM DIAKTIFKAN';
        PageVersion::create(['payload' => $payload]);
        $this->get('/')->assertOk()->assertSee('Berkarya untuk Maju')->assertDontSee('VERSI BELUM DIAKTIFKAN');
    }

    public function test_public_page_escapes_snapshot_text(): void
    {
        $this->seed(CmsSeeder::class);
        $settings = SiteSetting::firstOrFail();
        $payload = $settings->publishedVersion->payload;
        $payload['settings']['hero']['headline'] = '<script>alert("x")</script>';
        $version = PageVersion::create(['payload' => $payload]);
        $settings->update(['published_version_id' => $version->id]);
        $this->get('/')->assertOk()->assertDontSee('<script>alert("x")</script>', false)->assertSee('&lt;script&gt;', false);
    }

    public function test_page_without_published_version_returns_unavailable(): void
    {
        $this->get('/')->assertStatus(503);
    }

    public function test_seed_can_be_repeated_without_resetting_edits_or_publication(): void
    {
        $this->seed(CmsSeeder::class);
        $settings = SiteSetting::firstOrFail();
        $draft = $settings->draft_payload;
        $draft['hero']['headline'] = 'Edit yang harus dipertahankan';
        $settings->update(['draft_payload' => $draft]);
        $this->seed(CmsSeeder::class);
        $this->assertSame(1, PageVersion::count());
        $this->assertSame('Edit yang harus dipertahankan', $settings->fresh()->draft_payload['hero']['headline']);
    }
}
