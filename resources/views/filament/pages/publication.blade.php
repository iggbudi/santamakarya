<x-filament-panels::page>
 <div class="space-y-3">
  <p>Simpan perubahan di halaman editor terlebih dahulu, lalu periksa Preview Draft sebelum memublikasikan.</p>
  @php($published = $this->publishedVersion())
  @if($published)
   <p>Versi publik aktif: <strong>#{{ $published->id }}</strong> — {{ $published->created_at->timezone('Asia/Jakarta')->format('d M Y H:i:s') }} WIB.</p>
  @else
   <p>Belum ada versi yang dipublikasikan.</p>
  @endif
  <p>Pemulihan membuat versi publikasi baru dan mempertahankan draft saat ini.</p>
 </div>
 {{ $this->table }}
</x-filament-panels::page>
