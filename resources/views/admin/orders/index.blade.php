<x-dashboard-layout :menu="$menu" :title="$title">
    @if ($orders->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center text-sm text-charcoal/65">{{ __('admin.orders.empty', [], 'id') }}</p>
    @else
        <div class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            <div class="divide-y divide-forest/10">
                @foreach ($orders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-4 p-5 transition hover:bg-cream/60">
                        <div><h2 class="font-extrabold text-charcoal">{{ __('admin.orders.order_number', ['id' => $order->id], 'id') }}</h2><p class="mt-1 text-xs text-charcoal/60">{{ $order->customer->name }} · {{ $order->created_at->format('d/m/Y H:i') }}</p></div>
                        <div class="text-right"><p class="font-extrabold text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($order->total, 0, ',', '.') }}</p><p class="mt-1 text-xs text-charcoal/60">{{ __('marketplace.orders.'.$order->payment_status, [], 'id') }} · {{ $order->items_count }}</p></div>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-dashboard-layout>