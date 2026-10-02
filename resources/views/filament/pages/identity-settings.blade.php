<x-filament-panels::page>
    <p>Perubahan belum tampil di website sampai dipublikasikan.</p>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit">Simpan Draft</x-filament::button>
    </form>
</x-filament-panels::page>
