<footer class="bg-brand-charcoal text-white pt-16 pb-12 border-t border-white/10">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 pb-12 border-b border-white/10">
<!-- Col 1: Brand Info -->
<div class="lg:col-span-5">
<div class="flex items-center gap-3 mb-4">
<div class="w-9 h-9 flex-shrink-0">
@if(!empty($settings['identity']['logo_dark_url']))
<img class="w-full h-full object-contain" src="{{ $settings['identity']['logo_dark_url'] }}" alt="{{ $settings['identity']['brand_name'] }}" />
@else
<svg class="w-full h-full" fill="none" viewbox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
<path d="M 60 115 A 45 45 0 0 0 140 115 L 155 110 L 150 125 L 160 135 L 142 142 L 138 158 L 122 156 L 112 170 L 98 162 L 88 170 L 78 156 L 62 158 L 58 142 L 40 135 L 50 125 Z" fill="#FFFFFF"></path>
<circle cx="100" cy="115" fill="#121619" r="28"></circle>
<path d="M 70 45 H 130 V 62 H 88 V 82 H 112 C 122 82 128 88 128 98 V 110 C 128 124 116 135 100 135 C 84 135 72 124 72 110 V 82 H 88 V 110 C 88 116 93 120 100 120 C 107 120 112 116 112 110 V 98 C 112 96 110 94 106 94 H 70 V 45 Z" fill="#D96B27"></path>
</svg>
@endif
</div>
<span class="font-display font-extrabold text-lg text-white uppercase tracking-wider">{{ $settings['identity']['brand_name'] }}</span>
</div>
<p class="text-brand-orange font-medium text-sm mb-4">{{ $settings['footer']['tagline'] }}</p>
<p class="text-gray-400 text-sm max-w-sm leading-relaxed mb-6">{{ $settings['footer']['description'] }}</p>
</div>
<!-- Col 2: Navigation Links -->
<div class="lg:col-span-3">
<h4 class="font-display font-bold text-sm text-white uppercase tracking-wider mb-4">Navigasi</h4>
<ul class="space-y-2 text-sm text-gray-400">
<li><a class="hover:text-brand-orange transition-colors" href="#tentang">Tentang Kami</a></li>
<li><a class="hover:text-brand-orange transition-colors" href="#layanan">Yang Bisa Kami Kerjakan</a></li>
<li><a class="hover:text-brand-orange transition-colors" href="#keunggulan">Kenapa Kami</a></li>
<li><a class="hover:text-brand-orange transition-colors" href="#alur">Alur Kerja</a></li>
<li><a class="hover:text-brand-orange transition-colors" href="#portfolio">Portfolio</a></li>
</ul>
</div>
<!-- Col 3: Contact & Address -->
<div class="lg:col-span-4">
<h4 class="font-display font-bold text-sm text-white uppercase tracking-wider mb-4">Kontak &amp; Alamat</h4>
<p class="text-sm text-gray-400 leading-relaxed mb-3">{{ $settings['contact']['footer_address'] }}</p>
<p class="text-sm text-gray-300 font-medium mb-1">
                        WhatsApp: <a class="hover:text-brand-orange transition-colors" href="{{ 'https://wa.me/'.$settings['contact']['whatsapp_number'].(!empty($settings['contact']['consultation_message_enabled']) ? '?text='.rawurlencode($settings['contact']['consultation_message']) : '') }}">{{ $settings['contact']['phone_display'] }}</a>
</p>
<p class="text-sm text-gray-400">
                        Google Maps: <a class="text-brand-orange hover:underline" href="{{ $settings['contact']['maps_url'] }}" rel="noopener" target="_blank">{{ $settings['footer']['maps_name'] }}</a>
</p>
</div>
</div>
<!-- Bottom Copyright -->
<div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500">
<p>{{ $settings['footer']['copyright'] }}</p>
<p class="mt-2 sm:mt-0">{{ $settings['identity']['tagline'] }}</p>
</div>
</div>
</footer>

