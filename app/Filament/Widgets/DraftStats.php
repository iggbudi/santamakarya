<?php

namespace App\Filament\Widgets;

use App\Services\DashboardSummary;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DraftStats extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan konten draft';

    protected ?string $description = 'Jumlah berikut berasal dari draft, bukan versi yang sedang tampil di publik.';

    protected function getStats(): array
    {
        $data = app(DashboardSummary::class)->get();

        return [
            Stat::make('Proyek aktif (draft)', $data['projects'])->url(route('filament.admin.resources.portfolio-projects.index')),
            Stat::make('Foto Hero aktif (draft)', $data['hero'])->url(route('filament.admin.pages.hero-slideshow')),
            Stat::make('Foto Tentang aktif (draft)', $data['about'])->url(route('filament.admin.pages.about-slideshow')),
            Stat::make('Publikasi terakhir', $data['published_at'] ? $data['published_at']->timezone('Asia/Jakarta')->format('d M Y H:i').' WIB' : 'Belum ada')->description($data['published_version_id'] ? 'Versi #'.$data['published_version_id'] : 'Belum dipublikasikan')->url(route('filament.admin.pages.publication')),
        ];
    }
}
