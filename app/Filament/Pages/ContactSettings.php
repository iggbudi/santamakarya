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
            TextInput::make('whatsapp_name')->label('Nama kontak 1')->maxLength(50)->helperText('Contoh: Dian. Ditampilkan di depan nomor, contoh Dian (0812345678).'),
            TextInput::make('whatsapp_number')->label('Nomor WhatsApp / telepon 1')->required()->regex('/^[1-9][0-9]{7,14}$/')->helperText('Format internasional angka saja, contoh 6281234567890. Nomor utama, digunakan di seluruh tombol WhatsApp.'),
            TextInput::make('whatsapp_name_2')->label('Nama kontak 2 (opsional)')->maxLength(50)->requiredWith('whatsapp_number_2')->helperText('Wajib diisi bila nomor kedua diisi.'),
            TextInput::make('whatsapp_number_2')->label('Nomor WhatsApp / telepon 2 (opsional)')->regex('/^[1-9][0-9]{7,14}$/')->requiredWith('whatsapp_name_2')->helperText('Format internasional angka saja, contoh 6281234567890. Kosongkan bila hanya memakai satu nomor.'),
            Textarea::make('consultation_message')->label('Pesan konsultasi default')->required()->maxLength(800)->rows(3)->helperText('Digunakan untuk seluruh tombol WhatsApp.'),
            Textarea::make('address')->label('Alamat kantor')->required()->maxLength(800)->rows(3),
            TextInput::make('opening_hours')->label('Jam operasional')->required()->maxLength(100),
            TextInput::make('maps_url')->label('Tautan Google Maps')->required()->maxLength(2048)->rule(new GoogleMapsUrl),
            Textarea::make('maps_embed_url')->label('URL embed Google Maps')->required()->maxLength(2048)->rows(3)->rule(new GoogleMapsUrl(true))->helperText('Dari Share → Embed a map, salin hanya URL pada src. Jangan tempel HTML iframe.'),
        ])->columns(2);
    }
}
