<?php

namespace Tests\Feature\Cms;

use App\Models\User;
use Database\Seeders\CmsSeeder;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ReleaseReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_preview_responses_are_not_indexed_even_when_redirected(): void
    {
        $this->seed(CmsSeeder::class);
        foreach (['/admin/login', '/admin', '/admin/preview'] as $url) {
            $this->get($url)->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertHeader('Cache-Control', 'no-store, private');
        }
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_login_throttles_repeated_invalid_attempts(): void
    {
        RateLimiter::clear('release-test');
        $login = Livewire::test(Login::class)->fillForm(['email' => 'invalid@example.test', 'password' => 'Incorrect-password!']);
        for ($i = 0; $i < 5; $i++) {
            $login->call('authenticate')->assertHasFormErrors(['email']);
        }
        $login->call('authenticate')->assertNotified();
        $this->assertGuest();
    }

    public function test_below_fold_images_are_lazy_and_first_hero_is_preloaded(): void
    {
        $this->seed(CmsSeeder::class);
        $html = $this->get('/')->assertOk()->getContent();
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//section[@id="layanan" or @id="portfolio" or @id="tentang"]//img') as $image) {
            $this->assertSame('lazy', $image->getAttribute('loading'));
        }
        $this->assertSame(1, $xpath->query('//link[@rel="preload" and @as="image" and @fetchpriority="high"]')->length);
    }
}
