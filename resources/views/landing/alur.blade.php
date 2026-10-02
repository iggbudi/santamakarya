<section class="py-24 bg-brand-charcoal text-white relative overflow-hidden bg-grid-dark" id="alur">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center max-w-3xl mx-auto mb-20">
<span class="text-brand-orange font-semibold text-xs tracking-widest uppercase block mb-2">{{ $settings['workflow']['eyebrow'] }}</span>
<h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-white">{{ $settings['workflow']['heading'] }}</h2>
</div>
<!-- TIMELINE DESKTOP & MOBILE -->
<div class="relative">
<!-- Desktop Horizontal Connecting Line -->
<div class="hidden lg:block absolute top-1/2 left-10 right-10 h-[2px] bg-white/10 -translate-y-6 z-0"></div>
<div class="grid grid-cols-1 lg:grid-cols-5 gap-8 relative z-10">@foreach ($settings['workflow']['items'] as $item)
<div class="bg-brand-slate p-6 rounded-2xl border border-white/10 relative hover:border-brand-orange/50 transition-all duration-300">
<div class="w-12 h-12 rounded-xl font-display font-bold text-lg flex items-center justify-center mb-6 {{ $loop->first ? 'bg-brand-orange text-white shadow-lg shadow-brand-orange/20' : 'bg-white/10 text-white border border-white/20' }}">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
<h3 class="font-display text-lg font-bold text-white mb-2">{{ $item['title'] }}</h3>
<p class="text-gray-400 text-sm leading-relaxed">{{ $item['description'] }}</p>
</div>
@endforeach</div>
</div>
</div>
</section>
