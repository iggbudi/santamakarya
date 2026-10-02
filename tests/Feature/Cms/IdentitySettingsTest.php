<?php

namespace Tests\Feature\Cms;

use App\Filament\Pages\IdentitySettings;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IdentitySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_saves_identity_as_draft_without_changing_public_snapshot(): void
    {
        $this->seed(CmsSeeder::class);
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $version = SiteSetting::findOrFail(1)->published_version_id;
        Livewire::test(IdentitySettings::class)
            ->fillForm(['company_name' => 'Nama perusahaan draft', 'tagline' => 'Tagline draft'])
            ->call('save')->assertHasNoFormErrors();
        $settings = SiteSetting::findOrFail(1);
        $this->assertSame('Nama perusahaan draft', $settings->draft_payload['identity']['company_name']);
        $this->assertSame($version, $settings->published_version_id);
        $this->withoutVite()->get('/')->assertDontSee('Nama perusahaan draft');
    }

    public function test_non_admin_cannot_open_identity_editor(): void
    {
        $this->seed(CmsSeeder::class);
        $this->actingAs(User::factory()->create())->get('/admin/identity-settings')->assertForbidden();
    }
}
