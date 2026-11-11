<x-dashboard-layout :menu="$menu" :title="$title">
    <a href="{{ route('admin.rfqs.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('admin.rfqs.back', [], 'id') }}</a>

    <div class="mt-5 grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <section class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            <div class="border-b border-forest/10 px-5 py-4 sm:px-6">
                <p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.rfqs.rfq_id', [], 'id') }} #{{ $rfq->id }}</p>
                <p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.rfqs.date', [], 'id') }}</p>
                <h2 class="mt-1 text-xl font-extrabold text-charcoal">{{ $rfq->created_at->format('d/m/Y H:i') }}</h2>
            </div>
            <dl class="divide-y divide-forest/10 px-5 sm:px-6">
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.buyer_name', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->buyer_name }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.company', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->company }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.country', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->country }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.email', [], 'id') }}</dt><dd><a href="mailto:{{ $rfq->email }}" class="font-semibold text-forest underline decoration-forest/30">{{ $rfq->email }}</a></dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.phone', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->phone ?: '—' }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.product', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->product_name ?: __('admin.rfqs.general_inquiry', [], 'id') }} @if ($rfq->product_id)<span class="text-sm font-normal text-charcoal/55">(#{{ $rfq->product_id }})</span>@endif</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.quantity', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->quantity !== null ? number_format($rfq->quantity, 0, ',', '.').' '.$rfq->quantity_unit : '—' }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.wants_sample', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ __('admin.rfqs.'.($rfq->wants_sample ? 'yes' : 'no'), [], 'id') }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.asks_moq', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ __('admin.rfqs.'.($rfq->asks_moq ? 'yes' : 'no'), [], 'id') }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.incoterms', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->incoterms }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.message', [], 'id') }}</dt><dd class="whitespace-pre-line font-semibold text-charcoal">{{ $rfq->message ?: '—' }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.status', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ __('admin.rfqs.'.$rfq->status, [], 'id') }}</dd></div>
                <div class="grid gap-1 py-4 sm:grid-cols-[12rem_minmax(0,1fr)] sm:gap-5"><dt class="text-sm text-charcoal/55">{{ __('admin.rfqs.handled_at', [], 'id') }}</dt><dd class="font-semibold text-charcoal">{{ $rfq->handled_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
            </dl>
        </section>

        <aside class="h-fit rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
            @if ($rfq->status === 'new')
                <form method="POST" action="{{ route('admin.rfqs.mark-handled', $rfq) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-forest px-4 py-3 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('admin.rfqs.mark_handled', [], 'id') }}</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.rfqs.mark-new', $rfq) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-forest/20 px-4 py-3 text-sm font-bold text-forest transition hover:bg-cream">{{ __('admin.rfqs.mark_new', [], 'id') }}</button>
                </form>
            @endif
        </aside>
    </div>
</x-dashboard-layout>