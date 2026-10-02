<?php

namespace Tests\Feature\Cms;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_interactive_command_creates_authorized_admin_with_hashed_password(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nama admin', 'Santama Admin')
            ->expectsQuestion('Email admin', 'admin@example.test')
            ->expectsQuestion('Password (minimal 12 karakter)', 'Test-password-123!')
            ->assertSuccessful();

        $admin = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('Test-password-123!', $admin->password));
    }

    public function test_invalid_password_does_not_create_account(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nama admin', 'Santama Admin')
            ->expectsQuestion('Email admin', 'admin@example.test')
            ->expectsQuestion('Password (minimal 12 karakter)', 'short')
            ->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_does_not_overwrite_existing_user(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.test']);
        $this->artisan('admin:create')
            ->expectsQuestion('Nama admin', 'Different Name')
            ->expectsQuestion('Email admin', 'existing@example.test')
            ->expectsQuestion('Password (minimal 12 karakter)', 'Test-password-123!')
            ->assertFailed();
        $this->assertFalse($user->fresh()->is_admin);
        $this->assertDatabaseCount('users', 1);
    }
}
