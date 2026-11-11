@props(['product'])
<article class="overflow-hidden rounded-xl border border-forest/10 bg-white shadow-[0_12px_35px_-28px_rgba(27,57,38,0.55)] transition hover:-translate-y-0.5 hover:shadow-lg">
    <a href="{{ route('export.products.show', $product->slug) }}" class="group block">
        <div class="aspect-[4/3] overflow-hidden bg-[#edf2ed]">
            @if ($product->images->first())
                <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ __('export.product_card.image_alt', ['name' => $product->exportDetails->name_en]) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
            @else
                <div class="flex h-full items-center justify-center px-6 text-center text-sm text-charcoal/50">{{ __('export.product_card.no_image') }}</div>
            @endif
        </div>
        <div class="space-y-4 p-5">
            <div class="space-y-1">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/65"><x-export.category-label :category="$product->category" /></p>
                <h2 class="text-lg font-extrabold leading-snug text-charcoal transition group-hover:text-forest">{{ $product->exportDetails->name_en }}</h2>
            </div>
            <dl class="grid gap-3 border-t border-forest/10 pt-4 text-sm">
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-charcoal/55">{{ __('export.product_card.hs_code') }}</dt>
                    <dd class="text-right font-semibold text-charcoal">{{ $product->exportDetails->hs_code ?: __('export.product_card.not_provided') }}</dd>
                </div>
                @if ($product->exportDetails->monthly_capacity !== null && $product->exportDetails->monthly_capacity_unit)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-charcoal/55">{{ __('export.product_card.monthly_capacity') }}</dt>
                        <dd class="text-right font-semibold text-forest">{{ number_format($product->exportDetails->monthly_capacity) }} {{ __('export.units.'.$product->exportDetails->monthly_capacity_unit) }} / {{ __('export.units.month') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </a>
</article>