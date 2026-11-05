@props(['product'])

<article class="group overflow-hidden rounded-2xl border border-forest/5 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg sm:rounded-3xl">
    @php($image = $product->images->first())
    <a href="{{ route('marketplace.products.show', $product->slug) }}" class="block">
        <div class="relative flex aspect-[1/0.82] items-center justify-center overflow-hidden bg-cream">
            @if ($image)
                <img src="{{ asset('storage/'.$image->path) }}" alt="{{ __('marketplace.product_detail.image_alt', ['name' => $product->name], 'id') }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <div class="flex h-full w-full items-center justify-center text-5xl" role="img" aria-label="{{ __('marketplace.product_detail.no_image', [], 'id') }}">🥬</div>
            @endif
        <div class="absolute left-2 top-2 flex flex-col gap-1 sm:left-3 sm:top-3">
            <span class="inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-1 text-[9px] font-bold text-forest shadow-sm sm:text-[10px]">
                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.5 12.2 3l2.7-.1.9 2.5 2.2 1.6-.9 2.5.5 2.7-2.5 1-1.2 2.4-2.6-.5L9 16.4l-2.4 1.2-1.7-2.1-2.6-.7.1-2.7L1 9.8l1.8-2 0-2.7 2.6-.6L7 2.2l2.5.7L10 1.5Zm3.2 6.2a.8.8 0 0 0-1.2-1.1L8.7 10.4 7.5 9.2a.8.8 0 0 0-1.1 1.1l1.8 1.8a.8.8 0 0 0 1.2 0l3.8-4.4Z" clip-rule="evenodd" /></svg>
                {{ __('marketplace.products.verified', [], 'id') }}
            </span>
        </div>
        </div>
    </a>
    <div class="p-3 sm:p-4">
        <h3 class="line-clamp-2 min-h-10 text-sm font-extrabold leading-5 text-charcoal sm:text-base"><a href="{{ route('marketplace.products.show', $product->slug) }}">{{ $product->name }}</a></h3>
        <p class="mt-1 truncate text-[11px] text-charcoal/55 sm:text-xs"><a href="{{ route('marketplace.farmers.show', $product->farmer->farmerProfile->slug) }}">{{ $product->farmer->farmerProfile->shop_name }}</a> <span aria-hidden="true">·</span> {{ $product->farmer->farmerProfile->region }}</p>
        <div class="mt-3 flex flex-wrap items-baseline gap-x-1.5 gap-y-1">
            <span class="text-base font-extrabold text-forest sm:text-lg">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($product->price_idr, 0, ',', '.') }}</span>
            <span class="text-[10px] text-charcoal/50 sm:text-xs">/ {{ $product->unit }}</span>
        </div>
        <form method="POST" action="{{ route('cart.items.store', $product->id) }}" class="mt-3">
            @csrf
            <button type="submit" @disabled($product->stock < 1) class="w-full rounded-xl bg-harvest px-3 py-2.5 text-xs font-extrabold text-forest transition hover:bg-yellow-400 disabled:cursor-not-allowed disabled:opacity-50">{{ __('marketplace.products.add_to_cart', [], 'id') }}</button>
        </form>
    </div>
</article>
