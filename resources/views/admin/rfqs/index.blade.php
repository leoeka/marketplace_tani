<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6">
        <p class="text-sm leading-6 text-charcoal/65">{{ __('admin.rfqs.description', [], 'id') }}</p>
        <form method="GET" action="{{ route('admin.rfqs.index') }}" class="mt-5 grid gap-3 rounded-2xl border border-forest/10 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_minmax(12rem,0.5fr)_auto] sm:items-end">
            <div>
                <label for="search" class="text-sm font-semibold text-charcoal">{{ __('admin.rfqs.search', [], 'id') }}</label>
                <input id="search" name="search" type="search" maxlength="100" value="{{ $filters['search'] }}" placeholder="{{ __('admin.rfqs.search_placeholder', [], 'id') }}" class="mt-2 w-full rounded-lg border border-forest/20 px-4 py-2.5 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
            </div>
            <div>
                <label for="status" class="text-sm font-semibold text-charcoal">{{ __('admin.rfqs.status', [], 'id') }}</label>
                <select id="status" name="status" class="mt-2 w-full rounded-lg border border-forest/20 bg-white px-4 py-2.5 text-sm focus:border-forest focus:outline-none focus:ring-2 focus:ring-forest/20">
                    <option value="">{{ __('admin.rfqs.all_statuses', [], 'id') }}</option>
                    @foreach (['new', 'handled'] as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('admin.rfqs.'.$status, [], 'id') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-forest px-5 py-2.5 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('admin.rfqs.filter', [], 'id') }}</button>
        </form>
    </div>

    @if ($rfqs->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('admin.rfqs.empty', [], 'id') }}</p>
    @else
        <div class="overflow-x-auto rounded-2xl border border-forest/10 bg-white shadow-sm">
            <table class="w-full min-w-[68rem] text-left text-sm">
                <thead class="bg-cream text-xs uppercase tracking-wide text-charcoal/60">
                    <tr>
                        <th class="px-4 py-4">{{ __('admin.rfqs.date', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.buyer_name', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.company', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.country', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.product', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.incoterms', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.wants_sample', [], 'id') }} / {{ __('admin.rfqs.asks_moq', [], 'id') }}</th>
                        <th class="px-4 py-4">{{ __('admin.rfqs.status', [], 'id') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-forest/10">
                    @foreach ($rfqs as $rfq)
                        <tr class="align-top hover:bg-cream/50">
                            <td class="whitespace-nowrap px-4 py-4 text-charcoal/65">{{ $rfq->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4"><a href="{{ route('admin.rfqs.show', $rfq) }}" class="font-bold text-forest underline decoration-forest/30 underline-offset-2">{{ $rfq->buyer_name }}</a></td>
                            <td class="px-4 py-4 font-semibold text-charcoal">{{ $rfq->company }}</td>
                            <td class="px-4 py-4 text-charcoal/65">{{ $rfq->country }}</td>
                            <td class="max-w-56 px-4 py-4 text-charcoal/65">{{ $rfq->product_name ?: __('admin.rfqs.general_inquiry', [], 'id') }}</td>
                            <td class="px-4 py-4 font-semibold text-charcoal">{{ $rfq->incoterms }}</td>
                            <td class="px-4 py-4 text-charcoal/65">{{ __('admin.rfqs.wants_sample', [], 'id') }}: {{ __('admin.rfqs.'.($rfq->wants_sample ? 'yes' : 'no'), [], 'id') }}<br>{{ __('admin.rfqs.asks_moq', [], 'id') }}: {{ __('admin.rfqs.'.($rfq->asks_moq ? 'yes' : 'no'), [], 'id') }}</td>
                            <td class="px-4 py-4"><span @class(['inline-flex rounded-full px-3 py-1 text-xs font-bold', 'bg-amber-100 text-amber-900' => $rfq->status === 'new', 'bg-green-100 text-green-900' => $rfq->status === 'handled'])>{{ __('admin.rfqs.'.$rfq->status, [], 'id') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $rfqs->links() }}</div>
    @endif
</x-dashboard-layout>