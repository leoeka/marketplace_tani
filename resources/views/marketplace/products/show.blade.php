<x-marketplace.layout>
    @php
    $mt = app(\App\Support\MachineTranslator::class);
    $mt->primeContent([$product->name, $product->description, $product->category->name], app()->getLocale());
    @endphp
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('marketplace.products.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('marketplace.product_detail.back_to_products') }}</a>
        <div class="mt-6 grid gap-8 lg:grid-cols-2">
            <div class="grid content-start gap-3 sm:grid-cols-2">
                @forelse ($product->images as $image)
                    <img src="{{ asset('storage/'.$image->path) }}" alt="{{ __('marketplace.product_detail.image_alt', ['name' => $mt->content($product->name)]) }}" class="aspect-[4/3] w-full rounded-3xl bg-white object-cover">
                @empty
                    <div class="flex aspect-[4/3] items-center justify-center rounded-3xl bg-white text-6xl sm:col-span-2" role="img" aria-label="{{ __('marketplace.product_detail.no_image') }}">🥬</div>
                @endforelse
            </div>
            <article class="rounded-3xl border border-forest/10 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-bold text-forest/65">{{ $mt->content($product->category->name) }}</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ $mt->content($product->name) }}</h1>
                @if ($product->isPreorder())
                    <div class="mt-4 rounded-2xl bg-cream p-4">
                        <span class="inline-flex rounded-full bg-harvest px-3 py-1 text-xs font-extrabold text-forest">{{ __('marketplace.products.preorder_badge') }}</span>
                        <p class="mt-2 text-sm font-bold text-charcoal">{{ __('marketplace.products.harvest_date', ['date' => $product->harvest_date->format('d/m/Y')]) }}</p>
                        <p class="mt-1 text-sm text-charcoal/65">{{ __('marketplace.products.preorder_shipping_note') }}</p>
                    </div>
                @endif
                <p class="mt-5 text-2xl font-extrabold text-forest">{{ __('marketplace.products.currency_prefix') }} {{ number_format($product->price_idr, 0, ',', '.') }} <span class="text-base font-semibold text-charcoal/55">/ {{ $product->unit }}</span></p>
                <p class="mt-3 text-sm text-charcoal/65">{{ __('marketplace.product_detail.stock') }}: {{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}</p>
                <div class="mt-7 border-t border-forest/10 pt-6">
                    <h2 class="font-extrabold text-charcoal">{{ __('marketplace.product_detail.description') }}</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-charcoal/70">{{ $mt->content($product->description) }}</p>
                </div>
                @php
                    $spec = $product->exportDetails;
                    $hasCapacity = $spec && $spec->monthly_capacity !== null && filled($spec->monthly_capacity_unit);
                    $hasSpecs = $spec && (filled($spec->hs_code) || $spec->moisture_percent !== null || filled($spec->size_spec) || $hasCapacity);
                @endphp
                @if ($hasSpecs)
                    <section class="mt-7 border-t border-forest/10 pt-6" aria-labelledby="export-specs">
                        <h2 id="export-specs" class="font-extrabold text-charcoal">{{ __('marketplace.product_detail.specs.title') }}</h2>
                        <dl class="mt-3 divide-y divide-forest/10 rounded-2xl border border-forest/10 px-4 text-sm">
                            @if (filled($spec->hs_code))
                                <div class="flex justify-between gap-4 py-3"><dt class="text-charcoal/60">{{ __('marketplace.product_detail.specs.hs_code') }}</dt><dd class="text-right font-semibold text-charcoal">{{ $spec->hs_code }}</dd></div>
                            @endif
                            @if ($spec->moisture_percent !== null)
                                <div class="flex justify-between gap-4 py-3"><dt class="text-charcoal/60">{{ __('marketplace.product_detail.specs.moisture') }}</dt><dd class="text-right font-semibold text-charcoal">{{ $spec->moisture_percent }}%</dd></div>
                            @endif
                            @if (filled($spec->size_spec))
                                <div class="flex justify-between gap-4 py-3"><dt class="text-charcoal/60">{{ __('marketplace.product_detail.specs.size') }}</dt><dd class="text-right font-semibold text-charcoal">{{ $spec->size_spec }}</dd></div>
                            @endif
                            @if ($hasCapacity)
                                <div class="flex justify-between gap-4 py-3"><dt class="text-charcoal/60">{{ __('marketplace.product_detail.specs.capacity') }}</dt><dd class="text-right font-semibold text-charcoal">{{ number_format($spec->monthly_capacity, 0, ',', '.') }} {{ $spec->monthly_capacity_unit }} / {{ __('marketplace.product_detail.specs.per_month') }}</dd></div>
                            @endif
                        </dl>
                    </section>
                @endif
                <div class="mt-7 border-t border-forest/10 pt-6">
                    <p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('marketplace.product_detail.farmer') }}</p>
                    <a href="{{ route('marketplace.farmers.show', $product->farmer->farmerProfile->slug) }}" class="mt-2 inline-flex items-center gap-3 font-bold text-charcoal hover:text-forest">
                        @if ($product->farmer->farmerProfile->photo)
                            <img src="{{ asset('storage/'.$product->farmer->farmerProfile->photo) }}" alt="" class="h-12 w-12 rounded-xl object-cover">
                        @endif
                        <span>{{ $product->farmer->farmerProfile->shop_name }} · {{ $product->farmer->farmerProfile->region }}</span>
                    </a>
                </div>
                <form method="POST" action="{{ route('cart.items.store', $product->id) }}" class="mt-7">
                    @csrf
                    <button type="submit" @disabled($product->stock < 1) class="w-full rounded-xl bg-harvest px-5 py-3.5 text-sm font-extrabold text-forest transition hover:bg-yellow-400 disabled:cursor-not-allowed disabled:opacity-50">{{ __('marketplace.product_detail.add_to_cart') }}</button>
                </form>
            </article>
        </div>
    </section>
</x-marketplace.layout>