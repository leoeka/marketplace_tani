<section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8" aria-labelledby="category-heading">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.categories.eyebrow', [], 'id') }}</p>
            <h2 id="category-heading" class="mt-2 text-2xl font-extrabold tracking-tight text-charcoal sm:text-3xl">{{ __('marketplace.categories.title', [], 'id') }}</h2>
        </div>
        <a href="#products" class="hidden text-sm font-bold text-forest underline decoration-harvest decoration-2 underline-offset-4 sm:inline">{{ __('marketplace.products.view_all', [], 'id') }}</a>
    </div>

    @php
        $categories = [
            ['label' => 'vegetables', 'glyph' => '🥬', 'tone' => 'bg-[#E8F0DF]'],
            ['label' => 'fruits', 'glyph' => '🍊', 'tone' => 'bg-[#F9E9D8]'],
            ['label' => 'spices', 'glyph' => '🌶️', 'tone' => 'bg-[#F7E4DE]'],
            ['label' => 'rice', 'glyph' => '🌾', 'tone' => 'bg-[#F3ECCD]'],
        ];
    @endphp
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
        @foreach ($categories as $category)
            <a href="#products" class="group flex min-h-28 items-center gap-3 rounded-2xl border border-forest/5 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-md sm:min-h-32 sm:flex-col sm:justify-center sm:gap-2 sm:p-5">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full {{ $category['tone'] }} text-3xl transition group-hover:scale-105" aria-hidden="true">{{ $category['glyph'] }}</span>
                <span class="font-bold text-charcoal sm:text-base">{{ __('marketplace.categories.' . $category['label'], [], 'id') }}</span>
                <span class="hidden text-xs text-charcoal/50 sm:inline">{{ __('marketplace.categories.items', [], 'id') }}</span>
            </a>
        @endforeach
    </div>
</section>
