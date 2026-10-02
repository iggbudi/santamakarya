<?php

namespace Tests\Feature\Cms;

use App\Filament\Pages\Publication;
use App\Models\PageVersion;
use App\Models\SiteSetting;
use App\Models\Slide;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PublicationPageTest extends TestCase
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

    public function test_admin_publishes_then_restores_from_history(): void
    {
        $old = PageVersion::first();
        $setting = SiteSetting::find(1);
        $draft = $setting->draft_payload;
        $draft['hero']['headline'] = 'Draft dari halaman publikasi';
        $setting->update(['draft_payload' => $draft]);
        Livewire::test(Publication::class)->callAction('publish')->assertNotified('Draft berhasil dipublikasikan');
        $this->get('/')->assertSee('Draft dari halaman publikasi');
        Livewire::test(Publication::class)->callAction(TestAction::make('restore')->table($old))->assertNotified('Versi berhasil dipulihkan');
        $this->get('/')->assertDontSee('Draft dari halaman publikasi');
        $this->assertSame($draft, $setting->fresh()->draft_payload);
        $this->assertEquals(3, PageVersion::count());
    }

    public function test_invalid_draft_shows_error_without_publication(): void
    {
        Slide::where('location', 'about')->update(['is_active' => false]);
        Livewire::test(Publication::class)->callAction('publish')->assertNotified('Publikasi ditolak');
        $this->assertEquals(1, PageVersion::count());
    }

    public function test_guest_and_non_admin_cannot_access_publication_page(): void
    {
        auth()->logout();
        $this->get('/admin/publication')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/publication')->assertForbidden();
    }
}
