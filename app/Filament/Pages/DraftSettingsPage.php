<?php

namespace App\Filament\Pages;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;

abstract class DraftSettingsPage extends Page
{
    protected string $view = 'filament.pages.draft-settings';

    public ?array $data = [];

    abstract protected function initialState(): array;

    abstract protected function persist(array $data): void;

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->form->fill($this->initialState());
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('preview')->label('Preview Draft')->url(fn () => route('admin.preview'))->openUrlInNewTab()];
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->persist($this->form->getState());
        Notification::make()->title('Konten tersimpan sebagai draft')->success()->send();
    }

    protected function mediaFields(string $path, string $label, ?string $legacyPath = null): array
    {
        return [Select::make($path)->label($label)->options(fn () => MediaAsset::pluck('original_name', 'id')->all())->searchable()->live()->nullable()->exists('media_assets', 'id')->helperText('Unggah gambar di Pustaka Media. Kosong menggunakan foto bawaan bila tersedia.'),
            Placeholder::make('preview_'.str_replace('.', '_', $path))->label('Preview '.$label)->content(function (Get $get) use ($path, $legacyPath) {
                $url = MediaAsset::find($get($path))?->url;
                if (! $url && $legacyPath) {
                    $url = data_get(SiteSetting::findOrFail(1)->draft_payload, $legacyPath);
                }

                return $url ? new HtmlString('<img src="'.e($url).'" alt="Preview gambar" style="width:100%;height:180px;object-fit:cover;border-radius:12px" />') : 'Belum dipilih';
            }),
        ];
    }
}
