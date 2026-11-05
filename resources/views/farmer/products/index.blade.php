<x-dashboard-layout :menu="$menu" :title="$title">
    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm text-charcoal/65">{{ __('farmer.products.description', [], 'id') }}</p>
        </div>
        <a href="{{ route('farmer.products.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white transition hover:bg-forest/90">
            <span aria-hidden="true">+</span>{{ __('farmer.products.create', [], 'id') }}
        </a>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center">
            <span class="text-4xl" aria-hidden="true">🥬</span>
            <p class="mt-4 font-bold text-charcoal">{{ __('farmer.products.empty', [], 'id') }}</p>
            <a href="{{ route('farmer.products.create') }}" class="mt-3 inline-flex font-bold text-forest underline underline-offset-4">{{ __('farmer.products.add_first', [], 'id') }}</a>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                @php
                    $image = $product->images->first();
                @endphp
                <article class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
                    <div class="aspect-[4/3] bg-cream">
                        @if ($image)
                            <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-5xl" aria-label="{{ __('farmer.products.images', [], 'id') }}">🥬</div>
                        @endif
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-forest/60">{{ $product->category->name }}</p>
                                <h2 class="mt-1 truncate font-extrabold text-charcoal">{{ $product->name }}</h2>
                            </div>
                            <x-product-status-badge :status="$product->status" />
                        </div>
                        @if ($product->isPreorder())
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="rounded-full bg-harvest/30 px-2.5 py-1 font-extrabold text-forest">{{ __('farmer.products.preorder_badge', [], 'id') }}</span>
                                <span class="font-semibold text-charcoal/70">{{ __('marketplace.products.harvest_date', ['date' => $product->harvest_date->format('d/m/Y')], 'id') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between border-t border-forest/10 pt-4 text-sm">
                            <span class="font-extrabold text-forest">{{ __('farmer.products.currency', [], 'id') }} {{ number_format($product->price_idr, 0, ',', '.') }} / {{ $product->unit }}</span>
                            <span class="text-charcoal/60">{{ __('farmer.fields.stock', [], 'id') }}: {{ number_format($product->stock, 0, ',', '.') }}</span>
                        </div>
                        @if ($product->rejection_note)
                            <p class="rounded-xl bg-red-50 px-3 py-2 text-xs leading-5 text-red-800"><strong>{{ __('farmer.products.rejection_note', [], 'id') }}:</strong> {{ $product->rejection_note }}</p>
                        @endif
                        <div class="flex gap-2">
                            <a href="{{ route('farmer.products.edit', $product) }}" class="flex-1 rounded-xl border border-forest/20 px-4 py-2.5 text-center text-sm font-bold text-forest hover:bg-cream">{{ __('farmer.products.edit', [], 'id') }}</a>
                            <form method="POST" action="{{ route('farmer.products.destroy', $product) }}" onsubmit="return confirm(@js(__('farmer.products.delete_confirmation', [], 'id')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50">{{ __('farmer.products.delete', [], 'id') }}</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @endif
</x-dashboard-layout>