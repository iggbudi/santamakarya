<?php

namespace App\Filament\Resources\MediaAssets;

use App\Filament\Resources\MediaAssets\Pages\ManageMediaAssets;
use App\Models\MediaAsset;
use App\Services\MediaService;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Pustaka Media';

    protected static ?string $modelLabel = 'gambar';

    protected static ?string $pluralModelLabel = 'Pustaka Media';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('original_name')->label('Nama berkas')->disabled()->dehydrated(false),
                TextInput::make('alt_text')->label('Deskripsi gambar (alt text)')->maxLength(255)->dehydrateStateUsing(fn ($state) => $state ?? ''),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')->label('Preview')->disk('public')->imageHeight(64),
                TextColumn::make('original_name')->label('Nama')->searchable()->wrap(),
                TextColumn::make('dimensions')->label('Dimensi')->getStateUsing(fn (MediaAsset $record) => $record->width.' × '.$record->height),
                TextColumn::make('size_bytes')->label('Ukuran')->formatStateUsing(fn ($state) => number_format($state / 1024, 1).' KB'),
                TextColumn::make('usage')->label('Digunakan di')->getStateUsing(fn (MediaAsset $record) => implode(', ', app(MediaService::class)->usage($record)) ?: 'Belum digunakan')->wrap(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn (MediaAsset $record) => ! app(MediaService::class)->isReferenced($record))
                    ->using(function (MediaAsset $record) {
                        app(MediaService::class)->delete($record);

                        return true;
                    }),
            ])
            ->toolbarActions([
            ])->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMediaAssets::route('/'),
        ];
    }
}
