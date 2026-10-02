<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\ContentSchema;
use App\Services\ContentSettingsService;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ContentSettings extends DraftSettingsPage
{
    protected static ?string $title = 'Konten Landing Page';

    protected static ?string $navigationLabel = 'Konten Landing Page';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 12;

    protected function initialState(): array
    {
        return SiteSetting::findOrFail(1)->draft_payload;
    }

    protected function persist(array $data): void
    {
        app(ContentSettingsService::class)->updateContent($data);
    }

    public function form(Schema $schema): Schema
    {
        $tabs = [];
        foreach (ContentSchema::groups() as $key => $group) {
            $fields = [];
            foreach ($group['fields'] as $path => $field) {
                $name = $key.'.'.$path;
                if ($field['type'] === 'media') {
                    $legacy = $key === 'contact' ? 'contact.background_url' : str_replace('image_media_id', 'image_url', $name);
                    array_push($fields, ...$this->mediaFields($name, $field['label'], $legacy));
                } else {
                    $component = $field['type'] === 'textarea' ? Textarea::make($name)->rows(3) : TextInput::make($name);
                    $fields[] = $component->label($field['label'])->required()->maxLength($field['max'])->helperText('Maksimal '.$field['max'].' karakter.');
                }
            }
            $tabs[] = Tab::make($group['label'])->schema([Section::make($group['label'])->schema($fields)->columns(2)]);
        }

        return $schema->statePath('data')->components([Tabs::make('Bagian')->tabs($tabs)->columnSpanFull()]);
    }
}
