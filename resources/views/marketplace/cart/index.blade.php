<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.cart.title') }}</h1>

        @if ($items === [])
            <div class="mt-8 rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center">
                <p class="text-sm text-charcoal/65">{{ __('marketplace.cart.empty') }}</p>
                <a href="{{ route('marketplace.products.index') }}" class="mt-5 inline-flex rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('marketplace.cart.empty_action') }}</a>
            </div>
        @else
            <div class="mt-7 grid gap-6 lg:grid-cols-[1fr_20rem]">
                <div class="space-y-4">
                    @foreach ($items as $productId => $item)
                        @php($product = $item['product'])
                        <article class="grid gap-4 rounded-2xl border border-forest/10 bg-white p-4 shadow-sm sm:grid-cols-[8rem_1fr] sm:p-5">
                            @if ($product?->images->first())
                                <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ __('marketplace.product_detail.image_alt', ['name' => app(\App\Support\MachineTranslator::class)->content($product->name)]) }}" class="aspect-square w-full rounded-xl object-cover sm:w-32">
                            @else
                                <div class="flex aspect-square items-center justify-center rounded-xl bg-cream text-4xl sm:w-32" aria-hidden="true">🥬</div>
                            @endif
                            <div class="flex min-w-0 flex-col justify-between gap-4">
                                <div>
                                <h2 class="font-extrabold text-charcoal">{{ $product ? app(\App\Support\MachineTranslator::class)->content($product->name) : __('marketplace.cart.item_missing') }}</h2>                                    @if ($product)
                                        <p class="mt-1 text-sm text-charcoal/60">{{ __('marketplace.products.currency_prefix') }} {{ number_format($product->price_idr, 0, ',', '.') }} / {{ $product->unit }}</p>
                                        @if ($item['is_preorder'])
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                                <span class="rounded-full bg-harvest px-2.5 py-1 font-extrabold text-forest">{{ __('marketplace.products.preorder_badge') }}</span>
                                                <span class="font-semibold text-charcoal/70">{{ __('marketplace.products.harvest_date', ['date' => $product->harvest_date->format('d/m/Y')]) }}</span>
                                            </div>
                                        @endif
                                    @endif
                                    @unless ($item['available'])
                                        <p class="mt-2 text-sm font-semibold text-red-700" role="alert">{{ __('marketplace.cart.item_unavailable') }}</p>
                                    @endunless
                                </div>
                                <div class="flex flex-wrap items-end justify-between gap-3">
                                    @if ($product && $item['eligible'])
                                        <form method="POST" action="{{ route('cart.items.update', $productId) }}" class="flex items-end gap-2">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label for="qty-{{ $productId }}" class="mb-1 block text-xs font-bold text-charcoal/70">{{ __('marketplace.cart.quantity') }}</label>
                                                <input id="qty-{{ $productId }}" name="qty" type="number" min="1" max="{{ $product->stock }}" value="{{ $item['qty'] }}" required class="w-24 rounded-lg border-forest/15 text-sm focus:border-forest focus:ring-forest">
                                            </div>
                                            <button type="submit" class="rounded-lg border border-forest/20 px-3 py-2.5 text-xs font-bold text-forest">{{ __('marketplace.cart.update') }}</button>
                                        </form>
                                    @else
                                        <span class="text-sm text-charcoal/60">{{ __('marketplace.cart.quantity') }}: {{ $item['qty'] }}</span>
                                    @endif
                                    <div class="flex items-center gap-3">
                                        <strong class="text-sm font-extrabold text-forest">{{ __('marketplace.products.currency_prefix') }} {{ number_format($item['line_total'], 0, ',', '.') }}</strong>
                                        <form method="POST" action="{{ route('cart.items.destroy', $productId) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-700 underline underline-offset-2">{{ __('marketplace.cart.remove') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="h-fit rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between font-extrabold text-charcoal">
                        <span>{{ __('marketplace.cart.total') }}</span>
                        <span class="text-forest">{{ __('marketplace.products.currency_prefix') }} {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('marketplace.checkout.index') }}" class="mt-5 inline-flex w-full justify-center rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('marketplace.cart.checkout') }}</a>
                    <a href="{{ route('marketplace.products.index') }}" class="mt-3 inline-flex w-full justify-center text-sm font-bold text-forest underline underline-offset-4">{{ __('marketplace.cart.continue_shopping') }}</a>
                </aside>
            </div>
        @endif
    </section>
</x-marketplace.layout>