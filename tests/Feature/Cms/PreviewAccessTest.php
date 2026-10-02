<?php

namespace Tests\Feature\Cms;

use App\Models\SiteSetting;
use App\Models\Slide;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreviewAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
    }

    public function test_guest_cannot_preview(): void
    {
        $this->get('/admin/preview')->assertRedirect('/admin/login');
    }

    public function test_non_admin_cannot_preview(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/preview')->assertForbidden();
    }

    public function test_preview_shows_draft_only_to_admin_with_private_headers(): void
    {
        $setting = SiteSetting::find(1);
        $draft = $setting->draft_payload;
        $draft['hero']['headline'] = 'Judul draft privat';
        $setting->update(['draft_payload' => $draft]);
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get('/admin/preview')->assertOk()->assertSee('Judul draft privat')->assertSee('Preview Draft')->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertHeader('Cache-Control', 'no-store, private');
        $this->get('/')->assertDontSee('Judul draft privat')->assertDontSee('Preview Draft');
    }

    public function test_empty_slideshow_is_safe_to_preview(): void
    {
        Slide::query()->delete();
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get('/admin/preview')->assertOk()->assertSee('Preview Draft');
    }
}
