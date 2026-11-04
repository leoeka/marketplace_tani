<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6">
        <p class="max-w-2xl text-sm leading-6 text-charcoal/65">{{ __('farmer.dashboard.welcome', [], 'id') }}</p>
    </div>

    @unless ($isVerified)
        <section class="mb-6 flex gap-4 rounded-2xl border border-harvest/50 bg-amber-50 p-5 sm:p-6" role="status">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-harvest/30 text-xl" aria-hidden="true">⏳</span>
            <div>
                <h2 class="font-extrabold text-charcoal">{{ __('farmer.dashboard.verification_pending', [], 'id') }}</h2>
                <p class="mt-1 text-sm leading-6 text-charcoal/70">{{ __('farmer.dashboard.verification_description', [], 'id') }}</p>
            </div>
        </section>
    @endunless

    <section aria-labelledby="product-status-title">
        <div class="mb-4">
            <h2 id="product-status-title" class="text-lg font-extrabold text-charcoal">{{ __('farmer.dashboard.product_status', [], 'id') }}</h2>
            <p class="mt-1 text-sm text-charcoal/60">{{ __('farmer.dashboard.status_hint', [], 'id') }}</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach (['draft' => 'bg-stone-100 text-stone-700', 'pending' => 'bg-amber-100 text-amber-900', 'approved' => 'bg-green-100 text-green-900', 'rejected' => 'bg-red-100 text-red-900'] as $status => $tone)
                <article class="rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
                    <p class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $tone }}">{{ __('farmer.statuses.'.$status, [], 'id') }}</p>
                    <p class="mt-3 text-3xl font-extrabold text-forest">{{ $counts[$status] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if ($rejectedProducts->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="rejected-products-title">
            <div class="mb-4">
                <h2 id="rejected-products-title" class="text-lg font-extrabold text-charcoal">{{ __('farmer.dashboard.rejected_products', [], 'id') }}</h2>
            </div>
            <div class="divide-y divide-red-100">
                @foreach ($rejectedProducts as $product)
                    <article class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-bold text-charcoal">{{ $product->name }}</p>
                            <p class="mt-1 inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">{{ __('farmer.statuses.rejected', [], 'id') }}</p>
                            @if ($product->rejection_note)
                                <p class="mt-2 text-sm leading-5 text-red-700"><strong>{{ __('farmer.products.rejection_note', [], 'id') }}:</strong> {{ $product->rejection_note }}</p>
                            @endif
                        </div>
                        <a href="{{ route('farmer.products.edit', $product) }}" class="shrink-0 rounded-xl border border-forest/20 px-4 py-2.5 text-center text-sm font-bold text-forest hover:bg-cream">{{ __('farmer.dashboard.edit_rejected', [], 'id') }}</a>
                    </article>
                @endforeach
            </div>
            <div class="mt-4">{{ $rejectedProducts->links() }}</div>
        </section>
    @endif

    <section class="mt-8 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-charcoal">{{ __('farmer.dashboard.recent_products', [], 'id') }}</h2>
                <p class="mt-1 text-sm text-charcoal/60">{{ __('farmer.dashboard.status_hint', [], 'id') }}</p>
            </div>
            <a href="{{ route('farmer.products.index') }}" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white transition hover:bg-forest/90">
                {{ __('farmer.dashboard.view_products', [], 'id') }}
            </a>
        </div>
        @if ($recentProducts->isNotEmpty())
            <div class="mt-5 divide-y divide-forest/10">
                @foreach ($recentProducts as $product)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-4 first:pt-0 last:pb-0">
                        <div>
                            <p class="font-bold text-charcoal">{{ $product->name }}</p>
                            <p class="mt-1 text-xs text-charcoal/55">{{ $product->category->name }}</p>
                            @if ($product->status === 'rejected' && $product->rejection_note)
                                <p class="mt-2 text-sm leading-5 text-red-700"><strong>{{ __('farmer.products.rejection_note', [], 'id') }}:</strong> {{ $product->rejection_note }}</p>
                            @endif
                        </div>
                        <span class="rounded-full bg-cream px-3 py-1 text-xs font-bold text-forest">{{ __('farmer.statuses.'.$product->status, [], 'id') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-5 rounded-xl bg-cream px-4 py-5 text-sm text-charcoal/65">{{ __('farmer.dashboard.no_products', [], 'id') }}</p>
        @endif
    </section>
</x-dashboard-layout>
