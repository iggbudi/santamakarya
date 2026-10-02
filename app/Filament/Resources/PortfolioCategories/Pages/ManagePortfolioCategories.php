<?php

namespace App\Filament\Resources\PortfolioCategories\Pages;

use App\Filament\Resources\PortfolioCategories\PortfolioCategoryResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePortfolioCategories extends ManageRecords
{
    protected static string $resource = PortfolioCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('preview')->label('Preview Draft')->url(fn () => route('admin.preview'))->openUrlInNewTab(),
        ];
    }
}
