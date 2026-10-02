<?php

namespace Tests\Feature\Cms;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_authorized_admin_can_access_panel(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_authenticated_non_admin_cannot_access_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_registration_is_not_public(): void
    {
        $this->get('/admin/register')->assertNotFound();
    }
}
