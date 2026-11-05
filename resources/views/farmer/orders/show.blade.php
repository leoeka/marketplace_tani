<x-dashboard-layout :menu="$menu" :title="$title">
    <a href="{{ route('farmer.orders.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('farmer.orders.back', [], 'id') }}</a>
    <section class="mt-5 overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
        <h2 class="border-b border-forest/10 px-5 py-4 font-extrabold">{{ __('farmer.orders.order_number', ['id' => $order->id], 'id') }} · {{ $order->created_at->format('d/m/Y H:i') }}</h2>
        <div class="divide-y divide-forest/10">
            @foreach ($order->items as $item)
                <div class="flex flex-wrap justify-between gap-3 px-5 py-4">
                    <div>
                        <p class="font-bold text-charcoal">{{ $item->product_name }}</p>
                        <p class="mt-1 text-sm text-charcoal/60">{{ $item->qty }} {{ $item->unit }} × {{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($item->price, 0, ',', '.') }}</p>
                        @if ($item->is_preorder)
                            <p class="mt-2 text-xs font-semibold text-forest"><span class="rounded-full bg-harvest px-2.5 py-1 font-extrabold">{{ __('marketplace.products.preorder_badge', [], 'id') }}</span> <span class="ml-1">{{ __('marketplace.products.harvest_date', ['date' => $item->harvest_date->format('d/m/Y')], 'id') }}</span></p>
                        @endif
                    </div>
                    <strong class="text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($item->price * $item->qty, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>
    </section>
</x-dashboard-layout>