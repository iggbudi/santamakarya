<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Services\MediaService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;

class ManageMediaAssets extends ManageRecords
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')->label('Unggah Gambar')->modalSubmitActionLabel('Unggah')->schema([
                FileUpload::make('file')->label('Gambar')->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)->required()->storeFiles(false),
                TextInput::make('alt_text')->label('Deskripsi gambar (alt text)')->maxLength(255),
            ])->action(function (array $data) {
                $asset = app(MediaService::class)->upload($data['file']);
                $asset->update(['alt_text' => $data['alt_text'] ?? '']);
            })->successNotificationTitle('Gambar berhasil diunggah'),
        ];
    }
}
