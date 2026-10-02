<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>{{ $settings['seo']['title'] }}</title>
<meta name="description" content="{{ $settings['seo']['description'] }}">
@if(!empty($slides['hero'][0]['image_url']))
<link rel="preload" as="image" href="{{ $slides['hero'][0]['image_url'] }}" fetchpriority="high">
@endif
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $settings['seo']['title'] }}">
<meta property="og:description" content="{{ $settings['seo']['description'] }}">
<meta property="og:url" content="{{ route('landing') }}">
@if(!empty($settings['seo']['og_image_url']))
<meta property="og:image" content="{{ $settings['seo']['og_image_url'] }}">
@endif
@if(!empty($settings['identity']['favicon_url']))
<link rel="icon" href="{{ $settings['identity']['favicon_url'] }}" />
@endif
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;family=Space+Grotesk:wght@500;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS CDN -->

<!-- Lucide Icons CDN -->

<!-- Custom Tailwind Configuration -->


@vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body class="antialiased selection:bg-brand-orange selection:text-white">
@if($isPreview ?? false)
<aside class="fixed bottom-0 inset-x-0 z-[60] bg-brand-orange text-white text-center text-sm font-semibold px-4 py-3" role="status">
Preview Draft — perubahan ini belum dipublikasikan.
<a class="underline ml-3" href="{{ route('filament.admin.pages.publication') }}">Kembali ke Publikasi</a>
</aside>
@endif
@include('landing.navbar')
@include('landing.hero')
@include('landing.tentang')
@include('landing.layanan')
@include('landing.keunggulan')
@include('landing.alur')
@include('landing.portfolio')
@include('landing.kontak')
@include('landing.lokasi')
@include('landing.footer')
@include('landing.whatsapp')
</body>
</html>
