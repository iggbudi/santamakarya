<?php

namespace App\Filament\Resources\PortfolioProjects\Pages;

use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePortfolioProjects extends ManageRecords
{
    protected static string $resource = PortfolioProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('preview')->label('Preview Draft')->url(fn () => route('admin.preview'))->openUrlInNewTab(),
        ];
    }
}
