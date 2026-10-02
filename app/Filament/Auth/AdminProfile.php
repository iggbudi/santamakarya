<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\EditProfile;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\Rules\Password;

class AdminProfile extends EditProfile
{
    protected static ?string $title = 'Profil & Password';

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        parent::mount();
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        parent::save();
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()->rule(Password::min(12))->helperText('Password baru minimal 12 karakter. Kosongkan bila tidak ingin mengganti.');
    }
}
