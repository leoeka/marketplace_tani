<x-dashboard-layout :menu="$menu" :title="$title">
    <form method="POST" action="{{ $action }}" class="max-w-3xl space-y-5 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-7">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div>
            <label for="commodity" class="mb-1.5 block text-sm font-bold">{{ __('admin.market_prices.commodity', [], 'id') }}</label>
            <input id="commodity" name="commodity" value="{{ old('commodity', $price->commodity) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('commodity')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="price" class="mb-1.5 block text-sm font-bold">{{ __('admin.market_prices.price', [], 'id') }}</label>
                <input id="price" name="price" type="number" min="1" step="1" value="{{ old('price', $price->price) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('price')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="unit" class="mb-1.5 block text-sm font-bold">{{ __('admin.market_prices.unit', [], 'id') }}</label>
                <select id="unit" name="unit" required class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                    @foreach (['kg', 'ton'] as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', $price->unit ?? 'kg') === $unit)>{{ __('admin.market_prices.'.$unit, [], 'id') }}</option>
                    @endforeach
                </select>
                @error('unit')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label for="source" class="mb-1.5 block text-sm font-bold">{{ __('admin.market_prices.source', [], 'id') }}</label>
            <input id="source" name="source" value="{{ old('source', $price->source) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('source')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="recorded_on" class="mb-1.5 block text-sm font-bold">{{ __('admin.market_prices.recorded_on', [], 'id') }}</label>
            <input id="recorded_on" name="recorded_on" type="date" max="{{ today()->toDateString() }}" value="{{ old('recorded_on', $price->recorded_on?->format('Y-m-d') ?? today()->toDateString()) }}" required class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            @error('recorded_on')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('admin.market_prices.save', [], 'id') }}</button>
            <a href="{{ route('admin.market-prices.index') }}" class="rounded-xl border border-forest/20 px-5 py-3 text-sm font-bold text-forest">{{ __('admin.market_prices.back', [], 'id') }}</a>
        </div>
    </form>
</x-dashboard-layout>
