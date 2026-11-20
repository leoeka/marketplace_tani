@props(['product'])
@php
    $mt = app(\App\Support\MachineTranslator::class);
    $name = filled($product->exportDetails?->name_en) ? $product->exportDetails->name_en : $mt->content($product->name);
@endphp
<article class="overflow-hidden rounded-xl border border-forest/10 bg-white shadow-[0_12px_35px_-28px_rgba(27,57,38,0.55)] transition hover:-translate-y-0.5 hover:shadow-lg">
    <a href="{{ route('marketplace.products.show', $product->slug) }}" class="group block">
        <div class="aspect-[4/3] overflow-hidden bg-[#edf2ed]">
            @if ($product->images->first())
                <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ __('export.product_card.image_alt', ['name' => $name]) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
            @else
                <div class="flex h-full items-center justify-center px-6 text-center text-sm text-charcoal/50">{{ __('export.product_card.no_image') }}</div>
            @endif
        </div>
        <div class="space-y-4 p-5">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/65">{{ $mt->content($product->category->name) }}</p>
                <h2 class="text-lg font-extrabold leading-snug text-charcoal transition group-hover:text-forest">{{ $name }}</h2>
            </div>
            <span class="inline-flex text-sm font-bold text-forest underline decoration-forest/30 underline-offset-4 group-hover:decoration-forest">{{ __('export.product_card.view_detail') }} →</span>
        </div>
    </a>
</article>
