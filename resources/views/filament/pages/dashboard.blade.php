<x-filament-panels::page>
 {{ $this->content }}
 <x-filament::section heading="Pintasan pengelolaan">
  <div class="flex flex-wrap gap-3">
   <x-filament::button tag="a" :href="route('filament.admin.pages.content-settings')">Edit Konten</x-filament::button>
   <x-filament::button tag="a" :href="route('filament.admin.pages.identity-settings')">Logo & Identitas</x-filament::button>
   <x-filament::button tag="a" :href="route('filament.admin.pages.contact-settings')">Kontak & Maps</x-filament::button>
   <x-filament::button tag="a" :href="route('filament.admin.pages.seo-settings')">SEO & Open Graph</x-filament::button>
   <x-filament::button tag="a" :href="route('admin.preview')" target="_blank">Preview Draft</x-filament::button>
   <x-filament::button tag="a" :href="route('filament.admin.pages.publication')">Publikasi & Riwayat</x-filament::button>
   <x-filament::button tag="a" :href="route('filament.admin.auth.profile')" color="gray">Profil & Password</x-filament::button>
  </div>
 </x-filament::section>
 <p>Simpan Draft → Preview Draft → Publikasikan. Pemulihan versi tidak menimpa draft yang sedang dikerjakan.</p>
</x-filament-panels::page>
