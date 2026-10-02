<section class="py-24 sm:py-32 bg-white" id="portfolio">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
<div>
<span class="text-brand-orange font-semibold text-xs tracking-widest uppercase block mb-2">{{ $settings['portfolio']['eyebrow'] }}</span>
<h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-brand-charcoal">{{ $settings['portfolio']['heading'] }}</h2>
</div>
<!-- Category Filter Buttons -->
@if(count($portfolio['projects']))
<div class="mt-6 md:mt-0 flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar"><button class="port-filter-btn px-4 py-2 rounded-full text-xs font-semibold bg-brand-charcoal text-white transition-all whitespace-nowrap" data-filter="all">Semua</button>
@foreach ($portfolio['categories'] as $category)
<button class="port-filter-btn px-4 py-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all whitespace-nowrap" data-filter="{{ $category['slug'] }}">{{ $category['name'] }}</button>
@endforeach</div>
@endif
</div>
<!-- Masonry / Grid Layout -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">@foreach ($portfolio['projects'] as $project)
<div class="port-item group relative rounded-2xl overflow-hidden shadow-sm h-80 border border-gray-100" data-category="{{ $project['category_slug'] }}">
<img loading="lazy" decoding="async" alt="{{ $project['alt_text'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="{{ $project['image_url'] }}"/>
<div class="absolute inset-0 bg-gradient-to-t from-brand-charcoal/90 via-brand-charcoal/30 to-transparent opacity-80 group-hover:opacity-95 transition-opacity"></div>
<div class="absolute bottom-6 left-6 right-6 text-white">
<span class="inline-block px-2.5 py-1 rounded {{ $project['category_slug'] === 'commercial' ? 'bg-brand-orange/80' : 'bg-white/20' }} text-[10px] font-bold tracking-wider uppercase mb-2 backdrop-blur-sm">{{ $project['category_name'] }}</span>
<h4 class="font-display font-bold text-lg mb-1">{{ $project['title'] }}</h4>
<span class="text-xs text-gray-300 font-light block">{{ $project['caption'] }}</span>
</div>
</div>
@endforeach</div>
@if(!count($portfolio['projects']))<p class="text-gray-500">Belum ada proyek yang ditampilkan.</p>@endif
</div>
</section>
