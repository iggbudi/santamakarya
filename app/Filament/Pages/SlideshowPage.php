<?php

namespace App\Filament\Pages;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Models\Slide;
use App\Services\SlideshowService;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

abstract class SlideshowPage extends Page
{
    protected string $view = 'filament.pages.slideshow';

    protected static string $location;

    public ?array $data = [];

    protected function getHeaderActions(): array
    {
        return [Action::make('preview')->label('Preview Draft')->url(fn () => route('admin.preview'))->openUrlInNewTab()];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $this->loadDraft();
    }

    protected function loadDraft(): void
    {
        $this->form->fill(['interval' => SiteSetting::findOrFail(1)->draft_payload[static::$location]['interval_ms'],
            'items' => Slide::where('location', static::$location)->orderBy('sort_order')->orderBy('id')->get()->toArray()]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            TextInput::make('interval')->label('Jeda pergantian (milidetik)')->numeric()->required()->minValue(3000)->maxValue(10000),
            Repeater::make('items')->label('Foto (seret untuk mengurutkan)')->defaultItems(0)->maxItems(30)->reorderable()->schema([
                Hidden::make('id'), Select::make('media_asset_id')->label('Gambar')->options(fn () => MediaAsset::pluck('original_name', 'id')->all())->searchable()->live()->nullable()->helperText('Kosong mempertahankan foto bawaan yang sudah ada. Foto baru harus dipilih dari Pustaka Media.'),
                Placeholder::make('preview')->label('Preview gambar')->content(function (Get $get) {
                    $url = MediaAsset::find($get('media_asset_id'))?->url ?? Slide::where('location', static::$location)->find($get('id'))?->image_url;
                    if (! $url) {
                        return 'Belum dipilih';
                    }
                    $x = max(0, min(100, (int) ($get('position_x') ?? 50)));
                    $y = max(0, min(100, (int) ($get('position_y') ?? 50)));

                    return new HtmlString('<img src="'.e($url).'" alt="Preview foto" style="width:100%;height:200px;object-fit:cover;object-position:'.$x.'% '.$y.'%;border-radius:12px" />');
                }),
                TextInput::make('alt_text')->label('Deskripsi gambar')->maxLength(255),
                TextInput::make('position_x')->label('Posisi horizontal (%)')->numeric()->live(onBlur: true)->default(50)->minValue(0)->maxValue(100),
                TextInput::make('position_y')->label('Posisi vertikal (%)')->numeric()->live(onBlur: true)->default(50)->minValue(0)->maxValue(100),
                Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $state = $this->form->getState();
        app(SlideshowService::class)->save(static::$location, array_values($state['items']), (int) $state['interval']);
        $this->loadDraft();
        Notification::make()->title('Slideshow tersimpan sebagai draft')->success()->send();
    }
}
