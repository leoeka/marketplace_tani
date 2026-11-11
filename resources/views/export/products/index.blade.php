<x-export.layout :title="__('export.catalog.title')">
    <section class="bg-[#f4f7f3]">
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-forest/65">{{ __('export.catalog.eyebrow') }}</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-forest sm:text-4xl">{{ __('export.catalog.title') }}</h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-charcoal/65">{{ __('export.catalog.description') }}</p>

            <form method="GET" action="{{ route('export.products.index') }}" class="mt-8 grid gap-4 rounded-xl border border-forest/10 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_minmax(12rem,0.55fr)_auto] sm:items-end sm:p-5">
                <div>
                    <label for="q" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('export.catalog.search_label') }}</label>
                    <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="{{ __('export.catalog.search_placeholder') }}" class="w-full rounded-lg border-forest/15 focus:border-forest focus:ring-forest">
                </div>
                <div>
                    <label for="category" class="mb-1.5 block text-sm font-bold text-charcoal">{{ __('export.catalog.category_label') }}</label>
                    <select id="category" name="category" class="w-full rounded-lg border-forest/15 bg-white focus:border-forest focus:ring-forest">
                        <option value="">{{ __('export.catalog.all_categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)><x-export.category-label :category="$category" /></option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-lg bg-forest px-5 py-3 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('export.catalog.filter') }}</button>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
        @if ($products->isEmpty())
            <p class="rounded-xl border border-dashed border-forest/20 bg-[#f8faf8] px-5 py-12 text-center text-sm text-charcoal/60">{{ __('export.catalog.empty') }}</p>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-export.product-card :product="$product" />
                @endforeach
            </div>
            <x-export.pagination :paginator="$products" />
        @endif
    </section>
</x-export.layout>