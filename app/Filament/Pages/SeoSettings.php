<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\ContentSettingsService;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeoSettings extends DraftSettingsPage
{
    protected static ?string $title = 'SEO & Open Graph';

    protected static ?string $navigationLabel = 'SEO & Open Graph';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?int $navigationSort = 14;

    protected function initialState(): array
    {
        return SiteSetting::findOrFail(1)->draft_payload['seo'];
    }

    protected function persist(array $data): void
    {
        app(ContentSettingsService::class)->updateSeo($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            TextInput::make('title')->label('Judul SEO')->required()->maxLength(70)->helperText('Maksimal 70 karakter.'),
            Textarea::make('description')->label('Deskripsi SEO')->required()->maxLength(160)->rows(3)->helperText('Maksimal 160 karakter.'),
            ...$this->mediaFields('og_image_media_id', 'Gambar Open Graph'),
        ])->columns(2);
    }
}
