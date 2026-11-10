<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.market_prices.eyebrow', [], 'id') }}</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.market_prices.title', [], 'id') }}</h1>
            <p class="mt-2 text-sm leading-6 text-charcoal/65 sm:text-base">{{ __('marketplace.market_prices.description', [], 'id') }}</p>
        </div>

        <form method="GET" action="{{ route('marketplace.market-prices.index') }}" class="mt-7 flex flex-col gap-3 rounded-2xl border border-forest/10 bg-white p-4 shadow-sm sm:flex-row sm:items-end sm:p-5">
            <div class="flex-1">
                <label for="q" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('marketplace.market_prices.search_label', [], 'id') }}</label>
                <input id="q" name="q" type="search" value="{{ $search }}" placeholder="{{ __('marketplace.market_prices.search_placeholder', [], 'id') }}" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            </div>
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('marketplace.market_prices.search', [], 'id') }}</button>
        </form>

        @if ($rows->isEmpty())
            <p class="mt-7 rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ $search === '' ? __('marketplace.market_prices.empty', [], 'id') : __('marketplace.market_prices.empty_search', [], 'id') }}</p>
        @else
            <div class="mt-7 overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
                <table class="w-full min-w-[58rem] text-left text-sm">
                    <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60"><tr>
                        <th class="px-5 py-4">{{ __('marketplace.market_prices.commodity', [], 'id') }}</th>
                        <th class="px-5 py-4">{{ __('marketplace.market_prices.latest_price', [], 'id') }}</th>
                        <th class="px-5 py-4">{{ __('marketplace.market_prices.change', [], 'id') }}</th>
                        <th class="px-5 py-4">{{ __('marketplace.market_prices.source', [], 'id') }}</th>
                        <th class="px-5 py-4">{{ __('marketplace.market_prices.date', [], 'id') }}</th>
                    </tr></thead>
                    @foreach ($rows as $row)
                        <tbody x-data="{ open: false }" class="divide-y divide-forest/10">
                            @php
                                $change = $row['change_percent'];
                                $direction = $change === null ? null : ($change > 0 ? 'up' : ($change < 0 ? 'down' : 'same'));
                            @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <button type="button" @click="open = !open" :aria-expanded="open" class="inline-flex items-center gap-2 text-left font-extrabold text-forest underline decoration-forest/30 underline-offset-4">
                                        <span aria-hidden="true" x-text="open ? '−' : '+'"></span>
                                        {{ $row['latest']->commodity }} <span class="font-semibold text-charcoal/55">/ {{ $row['latest']->unit }}</span>
                                    </button>
                                </td>
                                <td class="px-5 py-4 font-bold">Rp {{ number_format($row['latest']->price, 0, ',', '.') }} / {{ $row['latest']->unit }}</td>
                                <td class="px-5 py-4">
                                    @if ($change === null)
                                        {{ __('marketplace.market_prices.no_comparison', [], 'id') }}
                                    @else
                                        <span @class(['font-bold text-green-800' => $direction === 'up', 'font-bold text-red-700' => $direction === 'down', 'font-bold text-charcoal/70' => $direction === 'same'])>
                                            {{ $direction === 'up' ? '↑' : ($direction === 'down' ? '↓' : '→') }} {{ number_format(abs($change), 2, ',', '.') }}%
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">{{ $row['latest']->source }}</td>
                                <td class="px-5 py-4">{{ $row['latest']->recorded_on->format('d/m/Y') }}</td>
                            </tr>
                            <tr x-show="open" x-cloak>
                                <td colspan="5" class="bg-cream/60 px-5 py-4">
                                    <h2 class="mb-3 text-sm font-extrabold text-charcoal">{{ __('marketplace.market_prices.history', [], 'id') }} · {{ $row['latest']->commodity }} / {{ $row['latest']->unit }}</h2>
                                    <div class="overflow-x-auto rounded-xl border border-forest/10 bg-white">
                                        <table class="w-full min-w-[32rem] text-left text-xs">
                                            <thead class="text-charcoal/60"><tr>
                                                <th class="px-4 py-3">{{ __('marketplace.market_prices.date', [], 'id') }}</th>
                                                <th class="px-4 py-3">{{ __('marketplace.market_prices.latest_price', [], 'id') }}</th>
                                                <th class="px-4 py-3">{{ __('marketplace.market_prices.source', [], 'id') }}</th>
                                            </tr></thead>
                                            <tbody class="divide-y divide-forest/10">
                                                @foreach ($row['history'] as $entry)
                                                    <tr>
                                                        <td class="px-4 py-3">{{ $entry->recorded_on->format('d/m/Y') }}</td>
                                                        <td class="px-4 py-3">Rp {{ number_format($entry->price, 0, ',', '.') }} / {{ $entry->unit }}</td>
                                                        <td class="px-4 py-3">{{ $entry->source }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endforeach
                </table>
            </div>
        @endif
    </section>
</x-marketplace.layout>
