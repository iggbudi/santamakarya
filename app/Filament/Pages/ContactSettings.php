<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Rules\GoogleMapsUrl;
use App\Services\ContentSettingsService;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSettings extends DraftSettingsPage
{
    protected static ?string $title = 'Kontak & Maps';

    protected static ?string $navigationLabel = 'Kontak & Maps';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static ?int $navigationSort = 13;

    protected function initialState(): array
    {
        return SiteSetting::findOrFail(1)->draft_payload['contact'];
    }

    protected function persist(array $data): void
    {
        app(ContentSettingsService::class)->updateContact($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            TextInput::make('whatsapp_number')->label('Nomor WhatsApp / telepon')->required()->regex('/^[1-9][0-9]{7,14}$/')->helperText('Format internasional angka saja, contoh 6281234567890. Nomor ini digunakan di seluruh halaman.'),
            Textarea::make('consultation_message')->label('Pesan konsultasi default')->required()->maxLength(800)->rows(3)->helperText('Digunakan untuk seluruh tombol WhatsApp.'),
            Textarea::make('address')->label('Alamat kantor')->required()->maxLength(800)->rows(3),
            TextInput::make('opening_hours')->label('Jam operasional')->required()->maxLength(100),
            TextInput::make('maps_url')->label('Tautan Google Maps')->required()->maxLength(2048)->rule(new GoogleMapsUrl),
            Textarea::make('maps_embed_url')->label('URL embed Google Maps')->required()->maxLength(2048)->rows(3)->rule(new GoogleMapsUrl(true))->helperText('Dari Share → Embed a map, salin hanya URL pada src. Jangan tempel HTML iframe.'),
        ])->columns(2);
    }
}
