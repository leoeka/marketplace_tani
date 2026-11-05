<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.orders.title', [], 'id') }}</h1>
        @if ($orders->isEmpty())
            <p class="mt-7 rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center text-sm text-charcoal/65">{{ __('marketplace.orders.empty', [], 'id') }}</p>
        @else
            <div class="mt-7 overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
                <div class="divide-y divide-forest/10">
                    @foreach ($orders as $order)
                        <a href="{{ route('customer.orders.show', $order) }}" class="flex flex-wrap items-center justify-between gap-4 p-5 transition hover:bg-cream/60">
                            <div>
                                <h2 class="font-extrabold text-charcoal">{{ __('marketplace.orders.order_number', ['id' => $order->id], 'id') }}</h2>
                                <p class="mt-1 text-xs text-charcoal/60">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items_count }} {{ __('marketplace.orders.items', [], 'id') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-extrabold text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($order->total, 0, ',', '.') }}</p>
                                <p class="mt-1 text-xs font-semibold text-charcoal/60">{{ __('marketplace.orders.'.$order->payment_status, [], 'id') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </section>
</x-marketplace.layout>