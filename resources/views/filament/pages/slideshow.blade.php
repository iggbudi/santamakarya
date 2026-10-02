<x-filament-panels::page>
 <p>Perubahan tersimpan sebagai draft. Maksimal 10 foto aktif; satu foto tampil statis.</p>
 <form wire:submit="save" class="space-y-6">
  {{ $this->form }}
  <x-filament::button type="submit">Simpan Draft</x-filament::button>
 </form>
</x-filament-panels::page>
