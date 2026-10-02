<?php

namespace App\Filament\Pages;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Services\DraftSettingsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class IdentitySettings extends Page
{
    protected string $view = 'filament.pages.identity-settings';

    protected static ?string $title = 'Identitas Perusahaan';

    protected static ?string $navigationLabel = 'Identitas Perusahaan';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    protected function getHeaderActions(): array
    {
        return [Action::make('preview')->label('Preview Draft')->url(fn () => route('admin.preview'))->openUrlInNewTab()];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->form->fill(SiteSetting::findOrFail(1)->draft_payload['identity']);
    }

    public function form(Schema $schema): Schema
    {
        $fields = [
            TextInput::make('company_name')->label('Nama perusahaan')->required()->maxLength(255),
            TextInput::make('brand_name')->label('Nama pada logo')->required()->maxLength(100),
            TextInput::make('brand_suffix')->label('Teks di bawah nama')->maxLength(100),
            TextInput::make('tagline')->label('Tagline')->required()->maxLength(300),
        ];
        foreach (['logo_light_media_id' => 'Logo untuk latar terang', 'logo_dark_media_id' => 'Logo untuk latar gelap', 'favicon_media_id' => 'Favicon'] as $key => $label) {
            $fields[] = Select::make($key)->label($label)->options(fn () => MediaAsset::pluck('original_name', 'id')->all())
                ->searchable()->live()->nullable()->exists('media_assets', 'id')->helperText('Unggah gambar di Pustaka Media terlebih dahulu.');
            $fields[] = Placeholder::make('preview_'.$key)->label('Preview '.$label)->content(function (Get $get) use ($key) {
                $asset = MediaAsset::find($get($key));
                if (! $asset) {
                    return 'Belum dipilih';
                }
                $background = $key === 'logo_dark_media_id' ? '#121619' : '#f3f4f6';

                return new HtmlString('<div style="padding:12px;border-radius:8px;background:'.$background.'"><img src="'.e($asset->url).'" alt="Preview logo" style="height:80px;max-width:100%;object-fit:contain"></div>');
            });
        }

        return $schema->components($fields)->columns(2)->statePath('data');
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        app(DraftSettingsService::class)->updateIdentity($this->form->getState());
        Notification::make()->title('Identitas tersimpan sebagai draft')->success()->send();
    }
}
