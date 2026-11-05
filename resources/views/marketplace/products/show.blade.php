<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('marketplace.products.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('marketplace.product_detail.back_to_products', [], 'id') }}</a>
        <div class="mt-6 grid gap-8 lg:grid-cols-2">
            <div class="grid content-start gap-3 sm:grid-cols-2">
                @forelse ($product->images as $image)
                    <img src="{{ asset('storage/'.$image->path) }}" alt="{{ __('marketplace.product_detail.image_alt', ['name' => $product->name], 'id') }}" class="aspect-[4/3] w-full rounded-3xl bg-white object-cover">
                @empty
                    <div class="flex aspect-[4/3] items-center justify-center rounded-3xl bg-white text-6xl sm:col-span-2" role="img" aria-label="{{ __('marketplace.product_detail.no_image', [], 'id') }}">🥬</div>
                @endforelse
            </div>
            <article class="rounded-3xl border border-forest/10 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-bold text-forest/65">{{ $product->category->name }}</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ $product->name }}</h1>
                <p class="mt-5 text-2xl font-extrabold text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($product->price_idr, 0, ',', '.') }} <span class="text-base font-semibold text-charcoal/55">/ {{ $product->unit }}</span></p>
                <p class="mt-3 text-sm text-charcoal/65">{{ __('marketplace.product_detail.stock', [], 'id') }}: {{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}</p>
                <div class="mt-7 border-t border-forest/10 pt-6">
                    <h2 class="font-extrabold text-charcoal">{{ __('marketplace.product_detail.description', [], 'id') }}</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-charcoal/70">{{ $product->description }}</p>
                </div>
                <div class="mt-7 border-t border-forest/10 pt-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('marketplace.product_detail.farmer', [], 'id') }}</p>
                    <a href="{{ route('marketplace.farmers.show', $product->farmer->farmerProfile->slug) }}" class="mt-2 inline-flex items-center gap-3 font-bold text-charcoal hover:text-forest">
                        @if ($product->farmer->farmerProfile->photo)
                            <img src="{{ asset('storage/'.$product->farmer->farmerProfile->photo) }}" alt="" class="h-12 w-12 rounded-xl object-cover">
                        @endif
                        <span>{{ $product->farmer->farmerProfile->shop_name }} · {{ $product->farmer->farmerProfile->region }}</span>
                    </a>
                </div>
                <form method="POST" action="{{ route('cart.items.store', $product->id) }}" class="mt-7">
                    @csrf
                    <button type="submit" @disabled($product->stock < 1) class="w-full rounded-xl bg-harvest px-5 py-3.5 text-sm font-extrabold text-forest transition hover:bg-yellow-400 disabled:cursor-not-allowed disabled:opacity-50">{{ __('marketplace.product_detail.add_to_cart', [], 'id') }}</button>
                </form>
            </article>
        </div>
    </section>
</x-marketplace.layout>