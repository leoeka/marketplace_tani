<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route('admin.freight-estimates.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="mode" class="mb-1.5 block text-xs font-bold text-charcoal/70">{{ __('admin.logistics.estimates.filter_mode', [], 'id') }}</label>
                <select id="mode" name="mode" class="rounded-xl border-forest/15 bg-white text-sm focus:border-forest focus:ring-forest">
                    <option value="">{{ __('admin.logistics.estimates.all_modes', [], 'id') }}</option>
                    @foreach (['sea', 'air'] as $mode)
                        <option value="{{ $mode }}" @selected($selectedMode === $mode)>{{ __('admin.logistics.estimates.'.$mode, [], 'id') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white">{{ __('admin.logistics.estimates.filter', [], 'id') }}</button>
        </form>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.loading-ports.index') }}" class="rounded-xl border border-forest/20 px-4 py-2.5 text-sm font-bold text-forest">{{ __('admin.logistics.ports_link', [], 'id') }}</a>
            <a href="{{ route('admin.freight-estimates.create') }}" class="rounded-xl bg-harvest px-4 py-2.5 text-center text-sm font-bold text-forest">{{ __('admin.logistics.estimates.add', [], 'id') }}</a>
        </div>
    </div>

    @if ($freightEstimates->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.logistics.estimates.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[62rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60"><tr>
                    <th class="px-5 py-4">{{ __('admin.logistics.estimates.mode', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.estimates.destination', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.estimates.loading_port', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.estimates.transit_days_min', [], 'id') }} / {{ __('admin.logistics.estimates.transit_days_max', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.estimates.rate_usd_per_kg', [], 'id') }}</th>
                    <th class="px-5 py-4">{{ __('admin.logistics.estimates.status', [], 'id') }}</th>
                    <th class="px-5 py-4"><span class="sr-only">{{ __('admin.logistics.estimates.edit', [], 'id') }}</span></th>
                </tr></thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($freightEstimates as $freightEstimate)
                        <tr>
                            <td class="px-5 py-4">{{ __('admin.logistics.estimates.'.$freightEstimate->mode, [], 'id') }}</td>
                            <td class="px-5 py-4 font-bold">{{ $freightEstimate->destination }}</td>
                            <td class="px-5 py-4">{{ $freightEstimate->loadingPort?->name ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $freightEstimate->transit_days_min }}–{{ $freightEstimate->transit_days_max }}</td>
                            <td class="px-5 py-4">{{ $freightEstimate->rate_usd_per_kg === null ? '—' : __('admin.logistics.currency', [], 'id').' '.number_format((float) $freightEstimate->rate_usd_per_kg, 2, '.', ',') }}</td>
                            <td class="px-5 py-4">{{ __('admin.logistics.estimates.'.($freightEstimate->is_active ? 'active' : 'inactive'), [], 'id') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.freight-estimates.edit', $freightEstimate) }}" class="font-bold text-forest underline">{{ __('admin.logistics.estimates.edit', [], 'id') }}</a>
                                    <form method="POST" action="{{ route('admin.freight-estimates.destroy', $freightEstimate) }}" onsubmit="return confirm(@js(__('admin.logistics.estimates.confirm_delete', [], 'id')))" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-700 underline">{{ __('admin.logistics.estimates.delete', [], 'id') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $freightEstimates->links() }}</div>
    @endif
</x-dashboard-layout>