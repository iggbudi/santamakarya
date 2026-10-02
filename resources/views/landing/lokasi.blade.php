<section class="py-24 bg-brand-surface relative" id="lokasi">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-2xl mx-auto mb-16">
<span class="text-brand-orange font-semibold text-xs tracking-widest uppercase block mb-2">{{ $settings['contact']['location_eyebrow'] }}</span>
<h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-charcoal">{{ $settings['contact']['location_heading'] }}</h2>
<p class="mt-2 text-gray-600 text-sm">{{ $settings['contact']['location_description'] }}</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
<!-- Address & Info Card -->
<div class="lg:col-span-5 bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
<div>
<div class="flex items-center gap-3 mb-6">
<!-- Logo Mini Mark -->
<div class="w-10 h-10 flex-shrink-0">
@if(!empty($settings['identity']['logo_light_url']))
<img class="w-full h-full object-contain" src="{{ $settings['identity']['logo_light_url'] }}" alt="{{ $settings['identity']['brand_name'] }}" />
@else
<svg class="w-full h-full" fill="none" viewbox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
<path d="M 60 115 A 45 45 0 0 0 140 115 L 155 110 L 150 125 L 160 135 L 142 142 L 138 158 L 122 156 L 112 170 L 98 162 L 88 170 L 78 156 L 62 158 L 58 142 L 40 135 L 50 125 Z" fill="#121619"></path>
<circle cx="100" cy="115" fill="#FFFFFF" r="28"></circle>
<path d="M 70 45 H 130 V 62 H 88 V 82 H 112 C 122 82 128 88 128 98 V 110 C 128 124 116 135 100 135 C 84 135 72 124 72 110 V 82 H 88 V 110 C 88 116 93 120 100 120 C 107 120 112 116 112 110 V 98 C 112 96 110 94 106 94 H 70 V 45 Z" fill="#D96B27"></path>
</svg>
@endif
</div>
<div>
<h3 class="font-display font-bold text-lg text-brand-charcoal">{{ $settings['identity']['company_name'] }}</h3>
<p class="text-xs text-brand-orange font-semibold">{{ $settings['contact']['business_label'] }}</p>
</div>
</div>
<!-- Address -->
<div class="space-y-4 text-sm text-gray-700 mb-8">
<div class="flex items-start gap-3">
<i class="w-5 h-5 text-brand-orange flex-shrink-0 mt-0.5" data-lucide="map-pin"></i>
<span>{{ $settings['contact']['address'] }}</span>
</div>
<div class="flex items-center gap-3">
<i class="w-5 h-5 text-brand-orange flex-shrink-0" data-lucide="phone"></i>
<a class="hover:text-brand-orange transition-colors" href="{{ 'tel:+'.$settings['contact']['whatsapp_number'] }}">{{ $settings['contact']['phone_display'] }}</a>
</div>
<div class="flex items-center gap-3">
<i class="w-5 h-5 text-brand-orange flex-shrink-0" data-lucide="clock"></i>
<span>{{ $settings['contact']['opening_hours'] }}</span>
</div>
</div>
</div>
<!-- Open in Maps Button -->
<a class="w-full py-3.5 px-6 rounded-lg bg-brand-charcoal hover:bg-brand-orange text-white text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2" href="{{ $settings['contact']['maps_url'] }}" rel="noopener" target="_blank">
<i class="w-4 h-4" data-lucide="navigation"></i>
<span>{{ $settings['contact']['maps_label'] }}</span>
</a>
</div>
<!-- Google Maps Embedded View -->
<div class="lg:col-span-7 rounded-2xl overflow-hidden shadow-sm border border-gray-200 min-h-[360px]">
<iframe allowfullscreen="" class="w-full h-full min-h-[380px] border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ $settings['contact']['maps_embed_url'] }}" title="Peta Lokasi CV Santama Karya Indonesia">
</iframe>
</div>
</div>
</div>
</section>

