<section class="py-24 sm:py-32 bg-white relative" id="keunggulan">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-3xl mx-auto mb-20">
<span class="text-brand-orange font-semibold text-xs tracking-widest uppercase block mb-2">{{ $settings['advantages']['eyebrow'] }}</span>
<h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-brand-charcoal">{{ $settings['advantages']['heading'] }}</h2>
</div>
<!-- 4 Visual Points Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">@foreach ($settings['advantages']['items'] as $item)
<div class="p-8 rounded-2xl bg-brand-offwhite border border-gray-100 hover:border-brand-orange/40 transition-all duration-300 hover:shadow-lg group">
<div class="font-display text-4xl font-extrabold text-brand-orange mb-6 group-hover:scale-110 transition-transform origin-left">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
<h3 class="font-display text-xl font-bold text-brand-charcoal mb-3">{{ $item['title'] }}</h3>
<p class="text-gray-600 text-sm leading-relaxed">{{ $item['description'] }}</p>
</div>
@endforeach</div>
</div>
</section>
