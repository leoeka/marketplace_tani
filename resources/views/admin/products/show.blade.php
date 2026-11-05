<x-dashboard-layout :menu="$menu" :title="$title">
    <a href="{{ route('admin.products.index') }}" class="mb-6 inline-flex text-sm font-bold text-forest underline underline-offset-4">← {{ __('admin.products.back', [], 'id') }}</a>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(20rem,0.7fr)]">
        <article class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            @if ($product->images->isNotEmpty())
                <div class="grid gap-2 p-3 sm:grid-cols-2">
                    @foreach ($product->images as $image)
                        <img src="{{ asset('storage/'.$image->path) }}" alt="{{ __('marketplace.product_detail.image_alt', ['name' => $product->name], 'id') }}" class="aspect-[4/3] w-full rounded-xl object-cover">
                    @endforeach
                </div>
            @else
                <div class="flex aspect-[4/3] items-center justify-center bg-cream text-6xl" aria-hidden="true">🥬</div>
            @endif
            <div class="space-y-4 p-5 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3"><p class="text-sm font-semibold text-forest/65">{{ $product->category->name }}</p><x-product-status-badge :status="$product->status" /></div>
                <h2 class="text-2xl font-extrabold text-charcoal">{{ $product->name }}</h2>
                <p class="text-sm leading-6 text-charcoal/70">{{ $product->description }}</p>
                @if ($product->rejection_note)
                    <p class="rounded-xl bg-red-50 p-4 text-sm text-red-800"><strong>{{ __('admin.products.rejection_note', [], 'id') }}:</strong> {{ $product->rejection_note }}</p>
                @endif
                <dl class="grid gap-4 border-t border-forest/10 pt-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.products.price', [], 'id') }}</dt><dd class="mt-1 font-extrabold text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($product->price_idr, 0, ',', '.') }} / {{ $product->unit }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.products.stock', [], 'id') }}</dt><dd class="mt-1 font-semibold text-charcoal">{{ number_format($product->stock, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.products.farmer', [], 'id') }}</dt><dd class="mt-1 font-semibold text-charcoal">{{ $product->farmer->farmerProfile?->shop_name ?: $product->farmer->name }}</dd></div>
                    @if ($product->harvest_date)
                        <div>
                            <dt class="text-xs font-bold uppercase text-charcoal/55">{{ __('admin.products.harvest_date', [], 'id') }}</dt>
                            <dd class="mt-1 font-semibold text-charcoal">
                                @if ($product->isPreorder())
                                    <span class="mr-2 rounded-full bg-harvest/30 px-2.5 py-1 text-xs font-extrabold text-forest">{{ __('admin.products.preorder_badge', [], 'id') }}</span>
                                @endif
                                {{ $product->harvest_date->format('d/m/Y') }}
                            </dd>
                        </div>
                    @endif
                </dl>
                @if ($product->farmer->farmerProfile)
                    <a href="{{ route('admin.farmers.show', $product->farmer->farmerProfile) }}" class="text-sm font-bold text-forest underline underline-offset-4">{{ __('admin.products.view_farmer', [], 'id') }}</a>
                @endif
            </div>
        </article>

        <div class="space-y-5">
            <form method="POST" action="{{ $product->status === 'approved' ? route('admin.products.channels.update', $product) : route('admin.products.approve', $product) }}" class="space-y-4 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
                @csrf
                <h2 class="text-lg font-extrabold text-charcoal">{{ $product->status === 'approved' ? __('admin.products.save_channels', [], 'id') : __('admin.products.approve', [], 'id') }}</h2>
                <label class="flex items-start gap-3 text-sm font-medium text-charcoal"><input type="checkbox" name="show_in_marketplace" value="1" @checked(old('show_in_marketplace', $product->show_in_marketplace)) class="mt-0.5 rounded border-forest/25 text-forest focus:ring-forest"><span>{{ __('admin.products.marketplace_channel', [], 'id') }}</span></label>
                <label class="flex items-start gap-3 text-sm font-medium text-charcoal"><input type="checkbox" name="show_in_export" value="1" @checked(old('show_in_export', $product->show_in_export)) class="mt-0.5 rounded border-forest/25 text-forest focus:ring-forest"><span>{{ __('admin.products.export_channel', [], 'id') }}</span></label>
                @error('channels') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
                @error('show_in_marketplace') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
                @error('show_in_export') <p class="text-sm text-red-700">{{ $message }}</p> @enderror
                <button type="submit" class="w-full rounded-xl bg-forest px-4 py-3 text-sm font-bold text-white hover:bg-forest/90">{{ $product->status === 'approved' ? __('admin.products.save_channels', [], 'id') : __('admin.products.approve', [], 'id') }}</button>
            </form>

            <form method="POST" action="{{ route('admin.products.reject', $product) }}" class="space-y-4 rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-6">
                @csrf
                <h2 class="text-lg font-extrabold text-charcoal">{{ __('admin.products.reject', [], 'id') }}</h2>
                <div>
                    <label for="rejection_note" class="mb-2 block text-sm font-bold text-charcoal">{{ __('admin.products.rejection_note', [], 'id') }}</label>
                    <textarea id="rejection_note" name="rejection_note" rows="4" required maxlength="2000" placeholder="{{ __('admin.products.rejection_placeholder', [], 'id') }}" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">{{ old('rejection_note') }}</textarea>
                    @error('rejection_note') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-xl bg-red-700 px-4 py-3 text-sm font-bold text-white hover:bg-red-800">{{ __('admin.products.reject', [], 'id') }}</button>
            </form>
        </div>
    </div>
</x-dashboard-layout>