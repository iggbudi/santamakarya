<?php

namespace Tests\Feature\Cms;

use App\Filament\Pages\ContactSettings;
use App\Filament\Pages\ContentSettings;
use App\Filament\Pages\SeoSettings;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ContentSettingsService;
use Database\Seeders\CmsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentEditorsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        $u = User::factory()->create();
        $u->forceFill(['is_admin' => true])->save();
        $this->actingAs($u);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_all_editors_save_as_draft_and_preserve_other_sections(): void
    {
        Livewire::test(ContentSettings::class)->fillForm(['hero.headline' => 'Judul melalui admin'])->call('save')->assertHasNoFormErrors();
        Livewire::test(ContactSettings::class)->fillForm(['whatsapp_number' => '628111222333'])->call('save')->assertHasNoFormErrors();
        Livewire::test(SeoSettings::class)->fillForm(['title' => 'Judul SEO admin'])->call('save')->assertHasNoFormErrors();
        $s = SiteSetting::find(1);
        $this->assertSame('Judul melalui admin', $s->draft_payload['hero']['headline']);
        $this->assertSame('628111222333', $s->draft_payload['contact']['whatsapp_number']);
        $this->assertSame('Judul SEO admin', $s->draft_payload['seo']['title']);
        $this->get('/')->assertDontSee('Judul melalui admin')->assertDontSee('628111222333')->assertDontSee('Judul SEO admin');
    }

    public function test_service_url_and_unedited_contact_cannot_be_overwritten_by_form_payload(): void
    {
        $s = SiteSetting::find(1);
        $before = $s->draft_payload;
        $data = $before;
        $data['services']['items'][0]['image_url'] = 'javascript:alert(1)';
        $data['contact']['whatsapp_number'] = '123';
        app(ContentSettingsService::class)->updateContent($data);
        $this->assertSame($before['services']['items'][0]['image_url'], $s->fresh()->draft_payload['services']['items'][0]['image_url']);
        $this->assertSame($before['contact']['whatsapp_number'], $s->fresh()->draft_payload['contact']['whatsapp_number']);
    }

    public function test_non_admin_is_denied_on_all_editors(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['content-settings', 'contact-settings', 'seo-settings'] as $slug) {
            $this->get('/admin/'.$slug)->assertForbidden();
        }
    }
}
