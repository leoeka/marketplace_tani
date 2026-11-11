<x-export.layout :title="__('export.home.meta_title')">
    <section class="relative isolate overflow-hidden bg-[#f4f7f3]">
        <div class="absolute inset-y-0 right-0 -z-10 hidden w-1/2 bg-forest lg:block" aria-hidden="true"></div>
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-[1.1fr_0.9fr] lg:px-10 lg:py-28">
            <div class="max-w-2xl">
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-forest/70">{{ __('export.home.eyebrow') }}</p>
                <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight text-forest sm:text-5xl lg:text-6xl">{{ __('export.home.title') }}</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-charcoal/65">{{ __('export.home.description') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('export.products.index') }}" class="inline-flex items-center justify-center rounded-lg bg-forest px-6 py-3.5 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('export.home.browse_products') }}</a>
                    <a href="{{ route('export.rfq.create') }}" class="inline-flex items-center justify-center rounded-lg border border-forest/20 bg-white px-6 py-3.5 text-sm font-bold text-forest transition hover:bg-[#f4f7f3]">{{ __('export.navigation.rfq') }}</a>
                </div>
            </div>
            <div class="relative hidden min-h-[22rem] items-center justify-center lg:flex" aria-hidden="true">
                <div class="absolute h-72 w-72 rounded-full border border-white/20"></div>
                <div class="absolute h-52 w-52 rounded-full border border-white/20"></div>
                <div class="grid h-40 w-40 place-items-center rounded-full bg-white/10 text-center text-sm font-semibold leading-6 text-white">
                    {{ __('export.home.visual_label') }}
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20 lg:px-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-forest/65">{{ __('export.home.featured_eyebrow') }}</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal">{{ __('export.home.featured_title') }}</h2>
            </div>
            <a href="{{ route('export.products.index') }}" class="text-sm font-bold text-forest underline decoration-forest/30 underline-offset-4 hover:decoration-forest">{{ __('export.home.view_catalog') }}</a>
        </div>

        @if ($featuredProducts->isEmpty())
            <p class="mt-8 rounded-xl border border-dashed border-forest/20 bg-[#f8faf8] px-5 py-10 text-center text-sm text-charcoal/60">{{ __('export.home.featured_empty') }}</p>
        @else
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredProducts as $product)
                    <x-export.product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </section>
</x-export.layout>