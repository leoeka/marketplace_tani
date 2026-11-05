<x-dashboard-layout :menu="$menu" :title="$title">
    @if ($orders->isEmpty())
        <p class="rounded-2xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center text-sm text-charcoal/65">{{ __('farmer.orders.empty', [], 'id') }}</p>
    @else
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($orders as $order)
                <article class="rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
                    <h2 class="font-extrabold text-charcoal">{{ __('farmer.orders.order_number', ['id' => $order->id], 'id') }}</h2>
                    <p class="mt-1 text-xs text-charcoal/60">{{ __('farmer.orders.date', [], 'id') }}: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <ul class="mt-4 space-y-2 border-t border-forest/10 pt-4 text-sm">
                        @foreach ($order->items as $item)
                            <li class="flex justify-between gap-3"><span>{{ $item->product_name }} × {{ $item->qty }} {{ $item->unit }}</span><span class="shrink-0 text-charcoal/60">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span></li>
                        @endforeach
                    </ul>
                    <a href="{{ route('farmer.orders.show', $order) }}" class="mt-4 inline-flex w-full justify-center rounded-xl border border-forest/20 px-4 py-2.5 text-sm font-bold text-forest">{{ __('marketplace.orders.view', [], 'id') }}</a>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-dashboard-layout>