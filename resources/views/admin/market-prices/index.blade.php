<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route('admin.market-prices.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="commodity" class="mb-1.5 block text-xs font-bold text-charcoal/70">{{ __('admin.market_prices.search', [], 'id') }}</label>
                <input id="commodity" name="commodity" value="{{ $filters['commodity'] ?? '' }}" maxlength="255" class="rounded-xl border-forest/15 bg-white text-sm focus:border-forest focus:ring-forest">
            </div>
            <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white">{{ __('admin.market_prices.filter', [], 'id') }}</button>
        </form>
        <a href="{{ route('admin.market-prices.create') }}" class="rounded-xl bg-harvest px-4 py-2.5 text-center text-sm font-bold text-forest">{{ __('admin.market_prices.add', [], 'id') }}</a>
    </div>

    @if ($prices->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.market_prices.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[46rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60"><tr>
                    <th class="px-5 py-4">{{ __('admin.market_prices.commodity', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.market_prices.price', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.market_prices.source', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.market_prices.recorded_on', [], 'id') }}</th>
                    <th class="px-5 py-4"><span class="sr-only">{{ __('admin.market_prices.edit', [], 'id') }}</span></th>
                </tr></thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($prices as $price)
                        <tr>
                            <td class="px-5 py-4 font-bold">{{ $price->commodity }} <span class="font-normal text-charcoal/60">/ {{ $price->unit }}</span></td>
                            <td class="px-5 py-4">Rp {{ number_format($price->price, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">{{ $price->source }}</td>
                            <td class="px-5 py-4">{{ $price->recorded_on->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.market-prices.edit', $price) }}" class="font-bold text-forest underline">{{ __('admin.market_prices.edit', [], 'id') }}</a>
                                    <form method="POST" action="{{ route('admin.market-prices.destroy', $price) }}" onsubmit="return confirm(@js(__('admin.market_prices.confirm_delete', [], 'id')))" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-700 underline">{{ __('admin.market_prices.delete', [], 'id') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $prices->links() }}</div>
    @endif
</x-dashboard-layout>
