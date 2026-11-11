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
    @php($shipment = $order->shipments->first())
    <section class="mt-6 space-y-4 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('farmer.orders.shipping_service', [], 'id') }}</p>
            <p class="mt-1 font-bold">{{ $order->shipping_service_name ?? __('marketplace.checkout.shipping_not_selected', [], 'id') }}</p>
            @if ($order->shipping_service_type)
                <p class="mt-1 text-sm text-charcoal/65">{{ __('farmer.orders.shipping_type_'.$order->shipping_service_type, [], 'id') }}</p>
            @endif
        </div>
        <div>
            <h2 class="font-extrabold">{{ __('farmer.orders.shipment', [], 'id') }}</h2>
            @if ($shipment)
                <p class="mt-2 text-sm"><span class="font-semibold">{{ __('farmer.orders.tracking_number', [], 'id') }}:</span> {{ $shipment->tracking_number }}</p>
                <p class="mt-1 text-sm"><span class="font-semibold">{{ __('farmer.orders.shipped_at', [], 'id') }}:</span> {{ $shipment->shipped_at->format('d/m/Y') }}</p>
            @else
                <p class="mt-2 text-sm text-charcoal/65">{{ __('farmer.orders.not_shipped', [], 'id') }}</p>
            @endif
        </div>
        <form method="POST" action="{{ route('farmer.orders.shipments.store', $order) }}" class="space-y-3">
            @csrf
            <div>
                <label for="tracking_number" class="mb-1.5 block text-sm font-bold">{{ __('farmer.orders.tracking_number', [], 'id') }}</label>
                <input id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $shipment?->tracking_number) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                @error('tracking_number')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('farmer.orders.save_tracking_number', [], 'id') }}</button>
        </form>
    </section>
</x-dashboard-layout>