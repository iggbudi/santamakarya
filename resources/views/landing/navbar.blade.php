<header class="fixed top-0 left-0 w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm" id="navbar">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
<!-- LOGO BRANDING -->
<a class="flex items-center gap-3 group" href="#">
<!-- Exact Stylized SVG Logo based on Santama Karya Official Logo -->
<div class="w-10 h-10 flex-shrink-0 relative flex items-center justify-center">
@if(!empty($settings['identity']['logo_light_url']))
<img class="w-full h-full object-contain" src="{{ $settings['identity']['logo_light_url'] }}" alt="{{ $settings['identity']['brand_name'] }}" />
@else
<svg class="w-full h-full drop-shadow-sm" fill="none" viewbox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
<!-- Black Gear Element -->
<path d="M 60 115 A 45 45 0 0 0 140 115 L 155 110 L 150 125 L 160 135 L 142 142 L 138 158 L 122 156 L 112 170 L 98 162 L 88 170 L 78 156 L 62 158 L 58 142 L 40 135 L 50 125 Z" fill="#121619"></path>
<circle cx="100" cy="115" fill="#FAFAFA" r="28"></circle>
<!-- Orange ST Monogram Symbol -->
<path d="M 70 45 H 130 V 62 H 88 V 82 H 112 C 122 82 128 88 128 98 V 110 C 128 124 116 135 100 135 C 84 135 72 124 72 110 V 82 H 88 V 110 C 88 116 93 120 100 120 C 107 120 112 116 112 110 V 98 C 112 96 110 94 106 94 H 70 V 45 Z" fill="#D96B27"></path>
</svg>
@endif
</div>
<div class="flex flex-col">
<span class="font-display tracking-wider font-extrabold text-lg sm:text-xl text-brand-charcoal uppercase leading-none">{{ $settings['identity']['brand_name'] }}</span>
<span class="text-[10px] tracking-[0.2em] text-gray-500 font-semibold uppercase mt-0.5">{{ $settings['identity']['brand_suffix'] }}</span>
</div>
</a>
<!-- DESKTOP NAVIGATION -->
<nav class="hidden lg:flex items-center space-x-8 text-sm font-medium text-gray-700">
<a class="hover:text-brand-orange transition-colors duration-200" href="#tentang">Tentang</a>
<a class="hover:text-brand-orange transition-colors duration-200" href="#layanan">Yang Bisa Kami Kerjakan</a>
<a class="hover:text-brand-orange transition-colors duration-200" href="#keunggulan">Kenapa Kami</a>
<a class="hover:text-brand-orange transition-colors duration-200" href="#alur">Alur Kerja</a>
<a class="hover:text-brand-orange transition-colors duration-200" href="#portfolio">Portfolio</a>
<a class="hover:text-brand-orange transition-colors duration-200" href="#kontak">Kontak</a>
</nav>
<!-- CTA BUTTON & MOBILE TOGGLE -->
<div class="flex items-center gap-4">
<a class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-brand-charcoal text-white text-sm font-semibold hover:bg-brand-orange transition-all duration-300 shadow-sm" href="{{ 'https://wa.me/'.$settings['contact']['whatsapp_number'].'?text='.rawurlencode($settings['contact']['messages']['cta_0']) }}" rel="noopener" target="_blank">
<span>Konsultasi</span>
<i class="w-4 h-4 text-brand-orange group-hover:text-white transition-colors" data-lucide="arrow-up-right"></i>
</a>
<!-- Mobile Menu Button -->
<button aria-label="Toggle Navigation" aria-controls="mobile-menu" aria-expanded="false" class="lg:hidden p-2 text-gray-700 hover:text-brand-orange focus:outline-none" id="mobile-menu-btn">
<i class="w-6 h-6" data-lucide="menu"></i>
</button>
</div>
</div>
<!-- MOBILE MENU DRAWER -->
<div class="hidden lg:hidden bg-white border-b border-gray-200 px-6 pt-4 pb-6 space-y-4 transition-all duration-300" id="mobile-menu">
<a class="block text-base font-medium text-gray-800 hover:text-brand-orange py-1" href="#tentang">Tentang</a>
<a class="block text-base font-medium text-gray-800 hover:text-brand-orange py-1" href="#layanan">Yang Bisa Kami Kerjakan</a>
<a class="block text-base font-medium text-gray-800 hover:text-brand-orange py-1" href="#keunggulan">Kenapa Kami</a>
<a class="block text-base font-medium text-gray-800 hover:text-brand-orange py-1" href="#alur">Alur Kerja</a>
<a class="block text-base font-medium text-gray-800 hover:text-brand-orange py-1" href="#portfolio">Portfolio</a>
<a class="block text-base font-medium text-gray-800 hover:text-brand-orange py-1" href="#kontak">Kontak</a>
<div class="pt-2">
<a class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-brand-orange text-white text-sm font-semibold hover:bg-brand-orangeHover transition-all" href="{{ 'https://wa.me/'.$settings['contact']['whatsapp_number'].'?text='.rawurlencode($settings['contact']['messages']['cta_1']) }}" rel="noopener" target="_blank">
<span>Konsultasi Proyek</span>
<i class="w-4 h-4" data-lucide="phone-call"></i>
</a>
</div>
</div>
</header>

