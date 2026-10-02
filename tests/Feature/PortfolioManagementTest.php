<?php

namespace Tests\Feature;

use App\Models\PortfolioCategory;
use App\Models\PortfolioProject;
use App\Services\CmsDraftService;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PortfolioManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_compilation_excludes_inactive_projects_and_empty_categories(): void
    {
        $this->seed(CmsSeeder::class);
        PortfolioCategory::create(['name' => 'Kosong', 'slug' => 'kosong', 'sort_order' => 99]);
        $project = PortfolioProject::first();
        $title = $project->title;
        $project->update(['is_active' => false]);
        $payload = app(CmsDraftService::class)->build();
        $this->assertNotContains($title, array_column($payload['portfolio']['projects'], 'title'));
        $this->assertNotContains('kosong', array_column($payload['portfolio']['categories'], 'slug'));
        $this->assertNotContains('Rumah Dinas & Fasilitas', array_column($payload['portfolio']['projects'], 'title'));
        $this->get('/')->assertSee($title)->assertSee('Rumah Dinas');
    }

    public function test_used_category_cannot_be_deleted(): void
    {
        $this->seed(CmsSeeder::class);
        $this->expectException(ValidationException::class);
        PortfolioProject::first()->category->delete();
    }

    public function test_empty_portfolio_compiles_and_renders(): void
    {
        $this->seed(CmsSeeder::class);
        PortfolioProject::query()->update(['is_active' => false]);
        $payload = app(CmsDraftService::class)->build();
        $this->assertSame([], $payload['portfolio']['categories']);
        $html = view('landing', ['settings' => $payload['settings'], 'slides' => $payload['slides'], 'portfolio' => $payload['portfolio']])->render();
        $this->assertStringContainsString('Belum ada proyek', $html);
        $this->assertStringNotContainsString('data-filter="all"', $html);
    }
}
