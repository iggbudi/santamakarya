<section class="py-24 bg-brand-surface relative bg-grid-pattern" id="layanan">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<!-- Section Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-16">
<div>
<span class="text-brand-orange font-semibold text-xs tracking-widest uppercase block mb-2">{{ $settings['services']['eyebrow'] }}</span>
<h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-brand-charcoal">{{ $settings['services']['heading'] }}</h2>
</div>
<p class="mt-4 md:mt-0 text-gray-600 max-w-md text-sm sm:text-base">{{ $settings['services']['description'] }}</p>
</div>
<!-- 6 Services Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">@foreach ($settings['services']['items'] as $item)
<div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col border border-gray-100">
<div class="relative h-52 overflow-hidden">
<img loading="lazy" decoding="async" alt="{{ $item['alt_text'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="{{ $item['image_url'] }}"/>
<div class="absolute top-4 left-4 w-10 h-10 rounded-lg bg-white/90 backdrop-blur-md flex items-center justify-center text-brand-orange font-bold text-sm">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
</div>
<div class="p-6 flex-1 flex flex-col justify-between">
<div>
<h3 class="font-display text-xl font-bold text-brand-charcoal mb-2 group-hover:text-brand-orange transition-colors">{{ $item['title'] }}</h3>
<p class="text-gray-600 text-sm leading-relaxed">{{ $item['description'] }}</p>
</div>
</div>
</div>
@endforeach</div>
</div>
</section>
