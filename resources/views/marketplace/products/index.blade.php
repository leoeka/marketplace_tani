<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.products.eyebrow', [], 'id') }}</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ __('marketplace.catalog.title', [], 'id') }}</h1>
            <p class="mt-2 text-sm leading-6 text-charcoal/65 sm:text-base">{{ __('marketplace.catalog.description', [], 'id') }}</p>
        </div>

        <form method="GET" action="{{ route('marketplace.products.index') }}" class="mt-7 grid gap-4 rounded-2xl border border-forest/10 bg-white p-4 shadow-sm sm:grid-cols-[1fr_16rem_auto] sm:items-end sm:p-5">
            <div>
                <label for="q" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('marketplace.catalog.search_label', [], 'id') }}</label>
                <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="{{ __('marketplace.catalog.search_placeholder', [], 'id') }}" class="w-full rounded-xl border-forest/15 focus:border-forest focus:ring-forest">
            </div>
            <div>
                <label for="category" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('marketplace.catalog.category_label', [], 'id') }}</label>
                <select id="category" name="category" class="w-full rounded-xl border-forest/15 bg-white focus:border-forest focus:ring-forest">
                    <option value="">{{ __('marketplace.catalog.all_categories', [], 'id') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-forest px-5 py-3 text-sm font-bold text-white hover:bg-forest/90">{{ __('marketplace.catalog.filter', [], 'id') }}</button>
        </form>

        @if ($products->isNotEmpty())
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-marketplace.product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <p class="mt-8 rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-14 text-center text-sm text-charcoal/65">{{ __('marketplace.catalog.empty', [], 'id') }}</p>
        @endif
    </section>
</x-marketplace.layout>