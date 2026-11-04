@props(['farmers'])

<section id="farmers" class="mx-auto max-w-7xl scroll-mt-8 px-4 pb-20 sm:px-6 sm:pb-24 lg:px-8" aria-labelledby="farmers-heading">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.farmers.eyebrow', [], 'id') }}</p>
            <h2 id="farmers-heading" class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.farmers.title', [], 'id') }}</h2>
            <p class="mt-2 text-sm leading-6 text-charcoal/65 sm:text-base">{{ __('marketplace.farmers.description', [], 'id') }}</p>
        </div>
        <a href="#farmers" class="inline-flex w-fit items-center gap-2 text-sm font-bold text-forest underline decoration-harvest decoration-2 underline-offset-4">{{ __('marketplace.farmers.view_all', [], 'id') }} <span aria-hidden="true">→</span></a>
    </div>

    @if ($farmers->isNotEmpty())
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
            @foreach ($farmers as $farmer)
                <x-marketplace.farmer-card :farmer="$farmer" />
            @endforeach
        </div>
    @else
        <p class="rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center text-sm text-charcoal/65">{{ __('marketplace.farmers.empty', [], 'id') }}</p>
    @endif
</section>
