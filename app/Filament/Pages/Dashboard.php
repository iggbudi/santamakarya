<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DraftStats;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dasbor';

    public function getWidgets(): array
    {
        return [DraftStats::class];
    }
}
