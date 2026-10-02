<section class="py-24 relative bg-brand-charcoal text-white overflow-hidden" id="kontak">
<!-- Background Image with Overlay -->
<div class="absolute inset-0 z-0 bg-cover bg-center opacity-25" style="background-image: url('{{ $settings['contact']['background_url'] }}');"></div>
<div class="absolute inset-0 bg-gradient-to-r from-brand-charcoal via-brand-charcoal/90 to-brand-charcoal/80 z-0"></div>
<div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
<h2 class="font-display text-3xl sm:text-5xl font-bold mb-6 text-white leading-tight">{{ $settings['contact']['cta_heading'] }}</h2>
<p class="text-lg sm:text-xl text-gray-300 font-light max-w-2xl mx-auto mb-10">{{ $settings['contact']['cta_description'] }}</p>
<div class="flex flex-col sm:flex-row items-center justify-center gap-4">
<a class="w-full sm:w-auto px-8 py-4 rounded-lg bg-brand-orange text-white font-semibold hover:bg-brand-orangeHover transition-all duration-300 shadow-lg shadow-brand-orange/20 flex items-center justify-center gap-3" href="{{ 'https://wa.me/'.$settings['contact']['whatsapp_number'].'?text='.rawurlencode($settings['contact']['messages']['cta_3']) }}" rel="noopener" target="_blank">
<i class="w-5 h-5" data-lucide="calendar"></i>
<span>{{ $settings['contact']['schedule_label'] }}</span>
</a>
<a class="w-full sm:w-auto px-8 py-4 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold backdrop-blur-md border border-white/20 transition-all duration-300 flex items-center justify-center gap-2" href="#lokasi">
<i class="w-4 h-4 text-brand-orange" data-lucide="map-pin"></i>
<span>{{ $settings['contact']['visit_label'] }}</span>
</a>
</div>
</div>
</section>
