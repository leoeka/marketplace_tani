<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <p class="text-sm leading-6 text-charcoal/65">{{ __('admin.products.description', [], 'id') }}</p>
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="status" class="mb-1.5 block text-xs font-bold text-charcoal/70">{{ __('admin.products.filter_status', [], 'id') }}</label>
                <select id="status" name="status" class="rounded-xl border-forest/15 bg-white text-sm focus:border-forest focus:ring-forest">
                    @foreach (['pending', 'draft', 'approved', 'rejected', 'all'] as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ $option === 'all' ? __('admin.products.all_statuses', [], 'id') : __('farmer.statuses.'.$option, [], 'id') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.products.filter_status', [], 'id') }}</button>
        </form>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.products.empty', [], 'id') }}</div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <article class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
                    <div class="aspect-[4/3] bg-cream">
                        @if ($product->images->first())
                            <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ __('marketplace.product_detail.image_alt', ['name' => $product->name], 'id') }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-5xl" aria-hidden="true">🥬</div>
                        @endif
                    </div>
                    <div class="space-y-3 p-5">
                        <div class="space-y-2">
                            <div><p class="text-xs text-forest/65">{{ $product->category->name }}</p><h2 class="mt-1 truncate whitespace-nowrap font-extrabold text-charcoal">{{ $product->name }}</h2></div>
                            <x-product-status-badge :status="$product->status" />
                        </div>
                        <p class="text-sm text-charcoal/65">{{ __('admin.products.farmer', [], 'id') }}: {{ $product->farmer->farmerProfile?->shop_name ?: $product->farmer->name }}</p>
                        <div class="flex items-center justify-between text-sm"><span class="font-extrabold text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($product->price_idr, 0, ',', '.') }} / {{ $product->unit }}</span><span class="text-charcoal/60">{{ __('admin.products.stock', [], 'id') }}: {{ number_format($product->stock, 0, ',', '.') }}</span></div>
                        <a href="{{ route('admin.products.show', $product) }}" class="inline-flex w-full justify-center rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.farmers.detail', [], 'id') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @endif
</x-dashboard-layout>