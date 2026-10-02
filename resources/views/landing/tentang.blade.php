<section class="py-24 sm:py-32 bg-white relative overflow-hidden" id="tentang">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
<!-- Left Column: Visual Image Accent -->
<div class="lg:col-span-5 relative">
<div class="relative rounded-2xl overflow-hidden shadow-2xl">
@if(count($slides['about']) === 1)
<img loading="lazy" decoding="async" alt="{{ $slides['about'][0]['alt_text'] }}" class="w-full h-[440px] object-cover hover:scale-105 transition-transform duration-700" style="object-position: {{ $slides['about'][0]['position_x'] ?? 50 }}% {{ $slides['about'][0]['position_y'] ?? 50 }}%" src="{{ $slides['about'][0]['image_url'] }}"/>
@else
<div id="about-slideshow" data-slideshow data-interval="{{ $settings['about']['interval_ms'] }}" class="relative h-[440px]">
@foreach($slides['about'] as $slide)
<img loading="lazy" decoding="async" data-slide alt="{{ $slide['alt_text'] }}" src="{{ $slide['image_url'] }}" class="about-slide absolute inset-0 w-full h-full object-cover {{ $loop->first ? 'slide-active' : 'slide-inactive' }}" style="object-position: {{ $slide['position_x'] ?? 50 }}% {{ $slide['position_y'] ?? 50 }}%" />
@endforeach
</div>
@endif
<div class="absolute inset-0 bg-gradient-to-t from-brand-charcoal/60 via-transparent to-transparent"></div>
<!-- Floating Location Tag Badge -->
<div class="absolute bottom-6 left-6 right-6 p-4 rounded-xl bg-white/90 backdrop-blur-md border border-white/50 text-brand-charcoal shadow-lg">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-brand-orange/10 flex items-center justify-center text-brand-orange flex-shrink-0">
<i class="w-5 h-5" data-lucide="building-2"></i>
</div>
<div>
<h4 class="font-bold text-sm">{{ $settings['about']['location_label'] }}</h4>
<p class="text-xs text-gray-500">{{ $settings['about']['location_description'] }}</p>
</div>
</div>
</div>
</div>
<!-- Aesthetic Orange Border Accent -->
<div class="absolute -bottom-4 -right-4 w-32 h-32 border-b-2 border-r-2 border-brand-orange rounded-br-2xl -z-0 hidden sm:block"></div>
</div>
<!-- Right Column: Content -->
<div class="lg:col-span-7">
<div class="inline-flex items-center gap-2 text-brand-orange font-semibold text-xs tracking-widest uppercase mb-3">
<span class="w-8 h-[2px] bg-brand-orange"></span>
<span>{{ $settings['about']['eyebrow'] }}</span>
</div>
<h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-brand-charcoal mb-6 leading-tight">{{ $settings['about']['heading'] }}</h2>
<!-- Direct Copywriting -->
<p class="text-lg sm:text-xl text-gray-700 font-normal leading-relaxed mb-6">{{ $settings['about']['description'] }}</p>
<div class="p-6 rounded-xl bg-brand-surface border-l-4 border-brand-orange mb-8">
<p class="text-base text-gray-800 font-medium italic">{{ $settings['about']['quote'] }}</p>
</div>
<!-- Key Attributes Grid -->
<div class="grid grid-cols-2 gap-6 pt-2">
<div>
<span class="block font-display text-3xl font-extrabold text-brand-charcoal mb-1">{{ $settings['about']['metrics'][0]['value'] }}</span>
<span class="text-sm text-gray-600 font-medium">{{ $settings['about']['metrics'][0]['label'] }}</span>
</div>
<div>
<span class="block font-display text-3xl font-extrabold text-brand-charcoal mb-1">{{ $settings['about']['metrics'][1]['value'] }}</span>
<span class="text-sm text-gray-600 font-medium">{{ $settings['about']['metrics'][1]['label'] }}</span>
</div>
</div>
</div>
</div>
</div>
</section>
