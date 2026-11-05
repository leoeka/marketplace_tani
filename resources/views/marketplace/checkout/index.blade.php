<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.checkout.title', [], 'id') }}</h1>
            <p class="mt-2 text-sm leading-6 text-charcoal/65">{{ __('marketplace.checkout.description', [], 'id') }}</p>
        </div>
        <form method="POST" action="{{ route('marketplace.checkout.store') }}" class="mt-7 grid gap-6 lg:grid-cols-[1fr_20rem]">
            @csrf
            <div class="space-y-6">
                <section class="space-y-4 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
                    <div>
                        <label for="recipient_name" class="mb-1.5 block text-sm font-bold">{{ __('marketplace.checkout.recipient_name', [], 'id') }}</label>
                        <input id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required maxlength="255" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                        @error('recipient_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-bold">{{ __('marketplace.checkout.phone', [], 'id') }}</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', auth()->user()->phone) }}" required maxlength="50" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
                        @error('phone')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="address" class="mb-1.5 block text-sm font-bold">{{ __('marketplace.checkout.address', [], 'id') }}</label>
                        <textarea id="address" name="address" rows="4" required maxlength="2000" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="notes" class="mb-1.5 block text-sm font-bold">{{ __('marketplace.checkout.notes', [], 'id') }}</label>
                        <textarea id="notes" name="notes" rows="2" maxlength="1000" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                </section>

                <section class="space-y-4 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
                    <fieldset>
                        <legend class="text-sm font-extrabold text-charcoal">{{ __('marketplace.checkout.payment_method', [], 'id') }}</legend>
                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                            @foreach (['transfer', 'ewallet', 'qris'] as $method)
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-forest/15 p-3 text-sm font-semibold">
                                    <input type="radio" name="payment_method" value="{{ $method }}" required @checked(old('payment_method', 'transfer') === $method) class="border-forest text-forest focus:ring-forest">
                                    {{ __('marketplace.checkout.'.$method, [], 'id') }}
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                        <p class="mt-3 text-xs text-charcoal/60">{{ __('marketplace.checkout.payment_instruction', [], 'id') }}</p>
                    </fieldset>
                    <fieldset>
                        <legend class="text-sm font-extrabold text-charcoal">{{ __('marketplace.checkout.payment_term', [], 'id') }}</legend>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-forest/15 px-4 py-3 text-sm font-semibold">
                                <input type="radio" name="payment_term" value="cash" required @checked(old('payment_term', 'cash') === 'cash') class="border-forest text-forest focus:ring-forest">
                                {{ __('marketplace.checkout.cash', [], 'id') }}
                            </label>
                            @if (auth()->user()->customer_type === 'business')
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-forest/15 px-4 py-3 text-sm font-semibold">
                                    <input type="radio" name="payment_term" value="tempo" required @checked(old('payment_term') === 'tempo') class="border-forest text-forest focus:ring-forest">
                                    {{ __('marketplace.checkout.tempo', [], 'id') }}
                                </label>
                            @endif
                        </div>
                        @error('payment_term')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </fieldset>
                </section>
            </div>

            <aside class="h-fit rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
                <h2 class="font-extrabold text-charcoal">{{ __('marketplace.orders.items', [], 'id') }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($items as $item)
                        <div class="flex justify-between gap-3 text-sm">
                            <span class="text-charcoal/70">
                                {{ $item['product']?->name }} × {{ $item['qty'] }}
                                @if ($item['is_preorder'] && $item['product'])
                                    <span class="mt-1 block text-xs font-semibold text-forest">{{ __('marketplace.products.preorder_badge', [], 'id') }} · {{ __('marketplace.products.harvest_date', ['date' => $item['product']->harvest_date->format('d/m/Y')], 'id') }}</span>
                                @endif
                            </span>
                            <span class="shrink-0 font-semibold">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($item['line_total'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-between border-t border-forest/10 pt-4 font-extrabold">
                    <span>{{ __('marketplace.cart.total', [], 'id') }}</span>
                    <span class="text-forest">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <button type="submit" class="mt-5 w-full rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white">{{ __('marketplace.checkout.place_order', [], 'id') }}</button>
            </aside>
        </form>
    </section>
</x-marketplace.layout>