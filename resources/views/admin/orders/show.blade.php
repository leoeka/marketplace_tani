<x-dashboard-layout :menu="$menu" :title="$title">
    <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-forest underline underline-offset-4">← {{ __('admin.orders.back', [], 'id') }}</a>
    <div class="mt-5 grid gap-6 lg:grid-cols-[1fr_20rem]">
        <section class="overflow-hidden rounded-2xl border border-forest/10 bg-white shadow-sm">
            <h2 class="border-b border-forest/10 px-5 py-4 font-extrabold">{{ __('admin.orders.order_number', ['id' => $order->id], 'id') }} · {{ $order->created_at->format('d/m/Y H:i') }}</h2>
            <div class="divide-y divide-forest/10">
                @foreach ($order->items as $item)
                    <div class="flex flex-wrap justify-between gap-3 px-5 py-4">
                        <div>
                            <p class="font-bold">{{ $item->product_name }}</p>
                            <p class="mt-1 text-sm text-charcoal/60">{{ $item->qty }} {{ $item->unit }} × {{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($item->price, 0, ',', '.') }}</p>
                            @if ($item->is_preorder)
                                <p class="mt-2 text-xs font-semibold text-forest"><span class="rounded-full bg-harvest px-2.5 py-1 font-extrabold">{{ __('marketplace.products.preorder_badge', [], 'id') }}</span> <span class="ml-1">{{ __('marketplace.products.harvest_date', ['date' => $item->harvest_date->format('d/m/Y')], 'id') }}</span></p>
                            @endif
                            <p class="mt-1 text-xs text-charcoal/55">{{ __('admin.products.farmer', [], 'id') }}: {{ $item->farmer->name }}</p>
                        </div>
                        <strong class="text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($item->price * $item->qty, 0, ',', '.') }}</strong>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between border-t border-forest/10 px-5 py-4 font-extrabold"><span>{{ __('marketplace.orders.total', [], 'id') }}</span><span class="text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($order->total, 0, ',', '.') }}</span></div>
        </section>
        <aside class="h-fit space-y-4 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
            <div><p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.orders.recipient', [], 'id') }}</p><p class="mt-1 font-bold">{{ $order->customer->name }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.orders.payment_method', [], 'id') }}</p><p class="mt-1 font-bold">{{ __('marketplace.checkout.'.$order->payment_method, [], 'id') }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.orders.payment_term', [], 'id') }}</p><p class="mt-1 font-bold">{{ __('marketplace.checkout.'.$order->payment_term, [], 'id') }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.orders.payment_status', [], 'id') }}</p><p class="mt-1 font-bold">{{ __('marketplace.orders.'.$order->payment_status, [], 'id') }}</p></div>
            <div><p class="text-xs font-bold uppercase tracking-wide text-forest/60">{{ __('admin.orders.shipping_address', [], 'id') }}</p><p class="mt-1 whitespace-pre-line text-sm leading-6">{{ $order->shipping_address }}</p></div>
            @if ($order->payment_status === 'unpaid')
                <form method="POST" action="{{ route('admin.orders.mark-paid', $order) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-forest px-4 py-3 text-sm font-bold text-white">{{ __('admin.orders.mark_paid', [], 'id') }}</button>
                </form>
            @endif
        </aside>
    </div>
</x-dashboard-layout>