<?php

namespace App\Filament\Resources\PortfolioCategories;

use App\Filament\Resources\PortfolioCategories\Pages\ManagePortfolioCategories;
use App\Models\PortfolioCategory;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PortfolioCategoryResource extends Resource
{
    protected static ?string $model = PortfolioCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Kategori Portofolio';

    protected static ?string $modelLabel = 'kategori';

    protected static ?string $pluralModelLabel = 'Kategori Portofolio';

    protected static ?int $navigationSort = 31;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(80),
            TextInput::make('slug')->required()->maxLength(80)->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')->unique(ignoreRecord: true)->helperText('Huruf kecil, angka, dan tanda hubung.'),
            TextInput::make('sort_order')->label('Urutan')->numeric()->required()->minValue(0)->maxValue(9999)->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama')->searchable(), TextColumn::make('slug'), TextColumn::make('sort_order')->label('Urutan')->sortable(),
            TextColumn::make('projects_count')->counts('projects')->label('Jumlah proyek')->description('Pindahkan/hapus seluruh proyek sebelum menghapus kategori.'),
        ])->defaultSort('sort_order')->recordActions([EditAction::make()->label('Edit draft'), DeleteAction::make()->label('Hapus draft')->visible(fn (PortfolioCategory $record) => ! $record->projects()->exists())])->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePortfolioCategories::route('/')];
    }
}
