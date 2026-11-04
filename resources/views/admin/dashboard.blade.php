<x-dashboard-layout :menu="$menu" :title="$title">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <p class="max-w-2xl text-sm leading-6 text-charcoal/65">{{ __('admin.dashboard.description', [], 'id') }}</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.farmers.index') }}" class="rounded-xl border border-forest/20 px-4 py-2.5 text-sm font-bold text-forest hover:bg-white">{{ __('admin.dashboard.view_farmers', [], 'id') }}</a>
            <a href="{{ route('admin.products.index') }}" class="rounded-xl bg-forest px-4 py-2.5 text-sm font-bold text-white hover:bg-forest/90">{{ __('admin.dashboard.view_products', [], 'id') }}</a>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="{{ __('admin.dashboard.title', [], 'id') }}">
        @foreach ([
            ['label' => 'verified_farmers', 'count' => $verifiedFarmers, 'tone' => 'bg-green-100 text-green-900'],
            ['label' => 'pending_farmers', 'count' => $pendingFarmers, 'tone' => 'bg-amber-100 text-amber-900'],
            ['label' => 'pending_products', 'count' => $pendingProducts, 'tone' => 'bg-sky-100 text-sky-900'],
        ] as $stat)
            <article class="rounded-2xl border border-forest/10 bg-white p-5 shadow-sm">
                <p class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $stat['tone'] }}">{{ __('admin.dashboard.'.$stat['label'], [], 'id') }}</p>
                <p class="mt-4 text-3xl font-extrabold text-forest">{{ number_format($stat['count'], 0, ',', '.') }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-8 rounded-2xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-extrabold text-charcoal">{{ __('admin.dashboard.product_status', [], 'id') }}</h2>
        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach (['draft', 'pending', 'approved', 'rejected'] as $status)
                <article class="rounded-xl bg-cream p-4">
                    <p class="text-sm font-semibold text-charcoal/70">{{ __('farmer.statuses.'.$status, [], 'id') }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-forest">{{ number_format($productCounts[$status], 0, ',', '.') }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-dashboard-layout>
