<?php

namespace Tests\Feature\Cms;

use App\Filament\Auth\AdminProfile;
use App\Models\PortfolioProject;
use App\Models\Slide;
use App\Models\User;
use App\Services\DashboardSummary;
use Database\Seeders\CmsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CmsSeeder::class);
        $this->admin = User::factory()->create(['password' => 'Original-safe-password!']);
        $this->admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_dashboard_counts_draft_content_correctly(): void
    {
        PortfolioProject::whereIn('id', PortfolioProject::orderBy('id')->take(2)->pluck('id'))->update(['is_active' => false]);
        Slide::where('location', 'hero')->first()->update(['is_active' => false]);
        $this->get('/admin')->assertOk()->assertSee('Proyek aktif (draft)')->assertSee('3')->assertSee('Foto Hero aktif (draft)')->assertSee('Foto Tentang aktif (draft)')->assertSee('Publikasi terakhir')->assertSee('Preview Draft');
        $summary = app(DashboardSummary::class)->get();
        $this->assertSame(3, $summary['projects']);
        $this->assertSame(3, $summary['hero']);
        $this->assertSame(1, $summary['about']);
        $this->assertSame(1, $summary['published_version_id']);
    }

    public function test_password_change_requires_current_password(): void
    {
        Livewire::test(AdminProfile::class)->fillForm(['password' => 'New-safe-password!', 'passwordConfirmation' => 'New-safe-password!'])->call('save')->assertHasFormErrors(['currentPassword' => 'required']);
        Livewire::test(AdminProfile::class)->fillForm(['password' => 'New-safe-password!', 'passwordConfirmation' => 'New-safe-password!', 'currentPassword' => 'wrong-password'])->call('save')->assertHasFormErrors(['currentPassword']);
        $this->assertTrue(Hash::check('Original-safe-password!', $this->admin->fresh()->password));
    }

    public function test_correct_password_allows_change_and_short_password_is_rejected(): void
    {
        Livewire::test(AdminProfile::class)->fillForm(['password' => 'TenChars10', 'passwordConfirmation' => 'TenChars10', 'currentPassword' => 'Original-safe-password!'])->call('save')->assertHasFormErrors(['password']);
        Livewire::test(AdminProfile::class)->fillForm(['password' => 'New-safe-password!', 'passwordConfirmation' => 'New-safe-password!', 'currentPassword' => 'Original-safe-password!'])->call('save')->assertHasNoFormErrors();
        $this->assertTrue(Hash::check('New-safe-password!', $this->admin->fresh()->password));
        $this->assertFalse(Hash::check('Original-safe-password!', $this->admin->fresh()->password));
    }

    public function test_non_admin_cannot_open_profile(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/profile')->assertForbidden();
    }
}
