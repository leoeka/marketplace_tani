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

            <section class="space-y-4 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
                <div>
                    <h2 class="text-lg font-extrabold text-charcoal">{{ __('admin.products.export_details_title', [], 'id') }}</h2>
                    @if (count($exportReasons) === 0)
                        <p class="mt-2 rounded-xl bg-green-50 px-3 py-2 text-sm font-semibold text-green-800">{{ __('admin.products.export_visible', [], 'id') }}</p>
                    @else
                        <div class="mt-2 rounded-xl bg-amber-50 px-3 py-2 text-sm text-amber-900">
                            <p class="font-semibold">{{ __('admin.products.export_not_visible', [], 'id') }}</p>
                            <ul class="mt-1 list-inside list-disc">
                                @foreach ($exportReasons as $reason)
                                    <li>{{ __('admin.products.export_reasons.'.$reason, [], 'id') }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.products.export-details.update', $product) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name_en" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_name_en', [], 'id') }}</label>
                        <input id="name_en" name="name_en" value="{{ old('name_en', $product->exportDetails?->name_en) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        @error('name_en') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="description_en" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_description_en', [], 'id') }}</label>
                        <textarea id="description_en" name="description_en" rows="5" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">{{ old('description_en', $product->exportDetails?->description_en) }}</textarea>
                        @error('description_en') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="hs_code" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_hs_code', [], 'id') }}</label>
                        <input id="hs_code" name="hs_code" value="{{ old('hs_code', $product->exportDetails?->hs_code) }}" inputmode="decimal" maxlength="13" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        @error('hs_code') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="moisture_percent" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_moisture', [], 'id') }}</label>
                            <input id="moisture_percent" name="moisture_percent" type="number" min="0" max="100" step="0.01" value="{{ old('moisture_percent', $product->exportDetails?->moisture_percent) }}" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                            @error('moisture_percent') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="size_spec" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_size_spec', [], 'id') }}</label>
                            <input id="size_spec" name="size_spec" value="{{ old('size_spec', $product->exportDetails?->size_spec) }}" maxlength="255" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                            @error('size_spec') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="monthly_capacity" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_monthly_capacity', [], 'id') }}</label>
                            <input id="monthly_capacity" name="monthly_capacity" type="number" min="0" step="1" value="{{ old('monthly_capacity', $product->exportDetails?->monthly_capacity) }}" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                            @error('monthly_capacity') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="monthly_capacity_unit" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('admin.products.export_capacity_unit', [], 'id') }}</label>
                            <select id="monthly_capacity_unit" name="monthly_capacity_unit" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                                <option value="">{{ __('admin.products.export_optional', [], 'id') }}</option>
                                @foreach (['kg', 'ton'] as $unit)
                                    <option value="{{ $unit }}" @selected(old('monthly_capacity_unit', $product->exportDetails?->monthly_capacity_unit) === $unit)>{{ __('admin.products.export_units.'.$unit, [], 'id') }}</option>
                                @endforeach
                            </select>
                            @error('monthly_capacity_unit') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-forest px-4 py-3 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.products.export_save', [], 'id') }}</button>
                </form>
            </section>
        </div>
    </div>
</x-dashboard-layout>