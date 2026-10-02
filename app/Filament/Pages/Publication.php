<?php

namespace App\Filament\Pages;

use App\Models\PageVersion;
use App\Models\SiteSetting;
use App\Services\PublicationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;

class Publication extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.publication';

    protected static ?string $title = 'Publikasi & Riwayat';

    protected static ?string $navigationLabel = 'Publikasi & Riwayat';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 50;

    public function mount(): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('preview')->label('Preview Draft')->url(fn () => route('admin.preview'))->openUrlInNewTab(),
            Action::make('publish')->label('Publikasikan')->requiresConfirmation()->modalHeading('Publikasikan draft?')->modalDescription('Seluruh draft tersimpan akan tampil di halaman publik. Versi sebelumnya tetap tersedia untuk dipulihkan.')->modalSubmitActionLabel('Publikasikan')
                ->action(fn () => $this->runPublication()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->query(PageVersion::query()->with('creator'))->heading('Riwayat Versi')->modelLabel('versi')->pluralModelLabel('versi')->defaultSort('id', 'desc')->columns([
            TextColumn::make('id')->label('Versi')->formatStateUsing(fn ($state) => '#'.$state),
            TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i:s')->timezone('Asia/Jakarta'),
            TextColumn::make('creator.name')->label('Oleh')->placeholder('Konten awal'),
            TextColumn::make('status')->label('Status')->getStateUsing(fn (PageVersion $record) => $record->id === SiteSetting::find(1)?->published_version_id ? 'Aktif di publik' : 'Tersimpan'),
        ])->recordActions([
            Action::make('restore')->label('Pulihkan')->requiresConfirmation()->modalHeading('Pulihkan versi ini?')->modalDescription('Salinan versi ini akan menjadi publikasi baru. Draft yang sedang dikerjakan tetap tersimpan.')->modalSubmitActionLabel('Pulihkan')
                ->action(fn (PageVersion $record) => $this->runPublication($record)),
        ])->toolbarActions([]);
    }

    public function publishedVersion(): ?PageVersion
    {
        return SiteSetting::with('publishedVersion')->find(1)?->publishedVersion;
    }

    private function runPublication(?PageVersion $source = null): void
    {
        abort_unless(auth()->user()?->is_admin, 403);
        try {
            $service = app(PublicationService::class);
            $source ? $service->restore($source, auth()->user()) : $service->publish(auth()->user());
            Notification::make()->title($source ? 'Versi berhasil dipulihkan' : 'Draft berhasil dipublikasikan')->success()->send();
        } catch (ValidationException $error) {
            Notification::make()->title($source ? 'Pemulihan ditolak' : 'Publikasi ditolak')->body(e(implode(' ', array_merge(...array_values($error->errors())))))->danger()->persistent()->send();
        }
    }
}
