<x-marketplace.layout>
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <article class="overflow-hidden rounded-3xl border border-forest/10 bg-white shadow-sm">
            <div class="grid lg:grid-cols-[minmax(18rem,0.8fr)_minmax(0,1.2fr)]">
                @if ($farmerProfile->photo)
                    <img src="{{ asset('storage/'.$farmerProfile->photo) }}" alt="{{ __('marketplace.farmers.avatar', [], 'id') }}: {{ $farmerProfile->shop_name }}" class="h-full max-h-[34rem] min-h-72 w-full object-cover">
                @else
                    <div class="flex min-h-72 items-center justify-center bg-cream text-7xl" role="img" aria-label="{{ __('marketplace.farmers.no_photo', [], 'id') }}">🌱</div>
                @endif
                <div class="flex flex-col justify-center p-6 sm:p-10">
                    <span class="w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-900">{{ __('marketplace.farmers.verified', [], 'id') }}</span>
                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.18em] text-forest/60">{{ __('marketplace.farmers.profile_title', [], 'id') }}</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ $farmerProfile->shop_name }}</h1>
                    <p class="mt-2 font-semibold text-charcoal/65">{{ $farmerProfile->user->name }} · {{ $farmerProfile->region }}</p>
                    @if ($farmerProfile->group_name)
                        <p class="mt-2 text-sm text-charcoal/65">{{ __('marketplace.farmers.group', [], 'id') }}: {{ $farmerProfile->group_name }}</p>
                    @endif
                    <div class="mt-6">
                        <h2 class="font-extrabold text-charcoal">{{ __('marketplace.farmers.shop_story', [], 'id') }}</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-charcoal/70">{{ $farmerProfile->story }}</p>
                    </div>
                </div>
            </div>
        </article>

        <section class="mt-12" aria-labelledby="farmer-products-title">
            <h2 id="farmer-products-title" class="text-2xl font-extrabold text-charcoal sm:text-3xl">{{ __('marketplace.farmers.products_title', [], 'id') }}</h2>
            @if ($products->isNotEmpty())
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($products as $product)
                        <x-marketplace.product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @else
                <p class="mt-5 rounded-3xl border border-dashed border-forest/20 bg-white px-6 py-12 text-center text-sm text-charcoal/65">{{ __('marketplace.farmers.products_empty', [], 'id') }}</p>
            @endif
        </section>
    </section>
</x-marketplace.layout>