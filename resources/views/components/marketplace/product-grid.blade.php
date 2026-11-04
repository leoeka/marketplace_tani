@props(['products'])

<section id="products" class="mx-auto max-w-7xl scroll-mt-8 px-4 pb-16 sm:px-6 sm:pb-20 lg:px-8" aria-labelledby="products-heading">
    <div class="mb-7 flex flex-col gap-4 sm:mb-9 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.products.eyebrow', [], 'id') }}</p>
            <h2 id="products-heading" class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.products.title', [], 'id') }}</h2>
            <p class="mt-2 text-sm leading-6 text-charcoal/65 sm:text-base">{{ __('marketplace.products.description', [], 'id') }}</p>
        </div>
        <a href="{{ route('marketplace.products.index') }}" class="inline-flex w-fit items-center gap-2 rounded-full border border-forest/15 px-5 py-3 text-sm font-bold text-forest transition hover:border-forest hover:bg-forest hover:text-white">
            {{ __('marketplace.products.view_all', [], 'id') }} <span aria-hidden="true">→</span>
        </a>
    </div>

    @if ($products->isNotEmpty())
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
            @foreach ($products as $product)
                <x-marketplace.product-card :product="$product" />
            @endforeach
        </div>
    @else
        <p class="rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center text-sm text-charcoal/65">{{ __('marketplace.catalog.empty', [], 'id') }}</p>
    @endif
</section>
