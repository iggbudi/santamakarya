<?php

namespace App\Filament\Resources\PortfolioProjects;

use App\Filament\Resources\PortfolioProjects\Pages\ManagePortfolioProjects;
use App\Models\MediaAsset;
use App\Models\PortfolioProject;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PortfolioProjectResource extends Resource
{
    protected static ?string $model = PortfolioProject::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Proyek Portofolio';

    protected static ?string $modelLabel = 'proyek';

    protected static ?string $pluralModelLabel = 'Proyek Portofolio';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Judul')->required()->maxLength(80),
            Select::make('category_id')->label('Kategori')->relationship('category', 'name')->searchable()->required()->exists('portfolio_categories', 'id'),
            Select::make('cover_media_asset_id')->label('Foto sampul')->options(fn () => MediaAsset::pluck('original_name', 'id')->all())->searchable()->exists('media_assets', 'id')->required(fn (?PortfolioProject $record) => ! $record?->image_url)->helperText('Foto bawaan tetap digunakan bila belum diganti. Proyek baru wajib memilih foto.'),
            TextInput::make('alt_text')->label('Deskripsi gambar')->maxLength(255)->dehydrateStateUsing(fn ($state) => $state ?? ''),
            TextInput::make('caption')->label('Keterangan')->maxLength(100)->dehydrateStateUsing(fn ($state) => $state ?? ''),
            TextInput::make('sort_order')->label('Urutan')->numeric()->required()->minValue(0)->maxValue(9999)->default(0),
            Toggle::make('is_active')->label('Aktif (matikan untuk arsip)')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->label('Judul')->searchable(), TextColumn::make('category.name')->label('Kategori'),
            IconColumn::make('is_active')->label('Aktif')->boolean(), TextColumn::make('sort_order')->label('Urutan')->sortable(),
        ])->defaultSort('sort_order')->filters([
            SelectFilter::make('category_id')->label('Kategori')->relationship('category', 'name'), TernaryFilter::make('is_active')->label('Aktif'),
        ])->recordActions([EditAction::make()->label('Edit draft'), DeleteAction::make()->label('Hapus draft')])->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePortfolioProjects::route('/')];
    }
}
