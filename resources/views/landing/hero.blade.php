<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
<!-- Background Slideshow Images -->
<div class="absolute inset-0 z-0" data-interval="{{ $settings['hero']['interval_ms'] }}" data-slideshow id="hero-slideshow">@foreach ($slides['hero'] as $slide)
<div data-slide aria-label="{{ $slide['alt_text'] }}" class="hero-slide slide-fade {{ $loop->first ? 'slide-active' : 'slide-inactive' }} absolute inset-0 bg-cover bg-center" role="img" style="background-image: url('{{ $slide['image_url'] }}'); background-position: {{ $slide['position_x'] ?? 50 }}% {{ $slide['position_y'] ?? 50 }}%;"></div>
@endforeach</div>
<!-- Classy Dark Gradients and Overlay -->
<div class="absolute inset-0 z-10 bg-gradient-to-r from-brand-charcoal/90 via-brand-charcoal/70 to-brand-charcoal/50"></div>
<div class="absolute inset-0 z-10 bg-gradient-to-t from-brand-charcoal via-transparent to-black/30"></div>
<!-- Hero Content Container -->
<div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white py-24">
<!-- Subtle Badge -->
<div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs sm:text-sm font-medium tracking-wide text-white/90 mb-6">
<span class="w-2 h-2 rounded-full bg-brand-orange animate-pulse"></span>
<span>{{ $settings['hero']['badge'] }}</span>
</div>
<!-- Tagline Headline -->
<h1 class="font-display text-4xl sm:text-6xl md:text-7xl font-bold tracking-tight text-white mb-6 leading-tight">{{ $settings['hero']['headline'] }}</h1>
<!-- Copywriting -->
<p class="max-w-2xl mx-auto text-lg sm:text-xl text-gray-300 font-light leading-relaxed mb-10">{{ $settings['hero']['description'] }}</p>
<!-- Hero Action Buttons -->
<div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-5">
<a class="w-full sm:w-auto px-8 py-4 rounded-lg bg-brand-orange text-white font-semibold text-base hover:bg-brand-orangeHover transition-all duration-300 shadow-lg shadow-brand-orange/20 flex items-center justify-center gap-3" href="{{ 'https://wa.me/'.$settings['contact']['whatsapp_number'].'?text='.rawurlencode($settings['contact']['messages']['cta_2']) }}" rel="noopener" target="_blank">
<span>{{ $settings['hero']['consultation_label'] }}</span>
<i class="w-5 h-5" data-lucide="arrow-right"></i>
</a>
<a class="w-full sm:w-auto px-8 py-4 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-base backdrop-blur-md border border-white/20 transition-all duration-300 flex items-center justify-center gap-2" href="#lokasi">
<i class="w-4 h-4 text-brand-orange" data-lucide="map-pin"></i>
<span>{{ $settings['hero']['visit_label'] }}</span>
</a>
</div>
<!-- Slideshow Dots Indicator -->
@if(count($slides['hero']) > 1)
<div class="flex items-center justify-center gap-2 mt-16">@foreach ($slides['hero'] as $slide)
<button aria-label="Slide {{ $loop->iteration }}" class="slide-dot {{ $loop->first ? 'w-8 bg-brand-orange' : 'w-2 bg-white/40 hover:bg-white' }} h-1.5 rounded-full transition-all duration-300" data-slide-for="hero-slideshow" data-slide-index="{{ $loop->index }}"></button>
@endforeach</div>
@endif
</div>
</section>


