@props(['product'])

@php
    $artTones = [
        'tomato' => 'bg-[#F6E8D5]',
        'leaf' => 'bg-[#E7EFDF]',
        'orange' => 'bg-[#F8E8D6]',
        'rice' => 'bg-[#F1EBD3]',
    ];
@endphp

<article class="group overflow-hidden rounded-2xl border border-forest/5 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg sm:rounded-3xl">
    <div class="relative flex aspect-[1/0.82] items-center justify-center overflow-hidden {{ $artTones[$product['art']] ?? 'bg-cream' }}">
        <span class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/40" aria-hidden="true"></span>
        <span class="absolute -bottom-8 -left-5 h-24 w-24 rounded-full bg-white/35" aria-hidden="true"></span>
        <svg class="relative h-[72%] w-[72%] transition duration-500 group-hover:scale-105" viewBox="0 0 180 150" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{{ __('marketplace.products.' . $product['art'] . '_art', [], 'id') }}">
            @switch($product['art'])
                @case('tomato')
                    <ellipse cx="90" cy="128" rx="60" ry="8" fill="#6A4D32" fill-opacity=".12" />
                    <circle cx="73" cy="82" r="34" fill="#E65D42" /><circle cx="111" cy="87" r="31" fill="#D94737" /><circle cx="93" cy="56" r="27" fill="#F0784D" />
                    <path d="M91 33c3-13 11-18 24-17-2 12-10 18-24 19M74 47c-9-11-18-13-29-7 6 10 16 13 29 9M96 38c-2-12 2-21 13-27 7 12 5 22-10 31" fill="#4F8D43" />
                    <path d="M75 61c4 2 6 5 6 9" stroke="#FFA17C" stroke-width="4" stroke-linecap="round" />
                    @break
                @case('leaf')
                    <ellipse cx="91" cy="128" rx="61" ry="8" fill="#456E3C" fill-opacity=".12" />
                    <path d="M87 121C48 109 33 79 43 38c35 6 57 26 63 60 8-30 27-48 59-51 6 42-16 68-67 79" fill="#4B913F" />
                    <path d="M90 123c1-31 13-57 37-78M70 68l24 23M133 69l-32 31M81 98l15 12" stroke="#B1D476" stroke-width="5" stroke-linecap="round" />
                    <path d="M54 91c-9-16-9-31 1-45 19 11 26 26 21 45M111 57c5-18 17-28 37-31 5 20-2 35-21 46" fill="#79AF4E" />
                    @break
                @case('orange')
                    <ellipse cx="91" cy="128" rx="58" ry="8" fill="#805330" fill-opacity=".12" />
                    <circle cx="75" cy="84" r="34" fill="#F39B28" /><circle cx="112" cy="88" r="30" fill="#E9821F" /><circle cx="96" cy="59" r="27" fill="#F8B33D" />
                    <path d="M95 35c4-14 14-19 28-17-3 13-11 20-26 20M73 50c-6-10-16-14-28-9 4 12 13 17 28 13" fill="#548D3B" />
                    <path d="M99 48c7 3 12 8 14 15" stroke="#FFD079" stroke-width="4" stroke-linecap="round" />
                    @break
                @case('rice')
                    <ellipse cx="90" cy="127" rx="62" ry="8" fill="#785D36" fill-opacity=".12" />
                    <path d="M90 121V40M90 67 62 48M91 83l30-28M90 99l-32-20M91 109l34-22" stroke="#8CA54B" stroke-width="5" stroke-linecap="round" />
                    <path d="M58 48c-1-12 4-19 15-22 6 11 3 19-9 25M117 56c3-12 10-17 22-15 2 12-4 19-17 21M53 79c-1-12 4-19 15-21 7 10 4 19-8 25M122 88c2-12 9-18 21-17 4 11-2 19-15 23M78 40c2-12 9-18 21-17 4 12-2 20-15 24" fill="#E9C868" stroke="#F5D98E" stroke-width="2" />
                    <path d="M58 119c19-20 45-20 66 0-21 17-45 17-66 0Z" fill="#F5F0DC" stroke="#D9C98C" stroke-width="4" />
                    @break
            @endswitch
        </svg>
        <div class="absolute left-2 top-2 flex flex-col gap-1 sm:left-3 sm:top-3">
            <span class="inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-1 text-[9px] font-bold text-forest shadow-sm sm:text-[10px]">
                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1.5 12.2 3l2.7-.1.9 2.5 2.2 1.6-.9 2.5.5 2.7-2.5 1-1.2 2.4-2.6-.5L9 16.4l-2.4 1.2-1.7-2.1-2.6-.7.1-2.7L1 9.8l1.8-2 0-2.7 2.6-.6L7 2.2l2.5.7L10 1.5Zm3.2 6.2a.8.8 0 0 0-1.2-1.1L8.7 10.4 7.5 9.2a.8.8 0 0 0-1.1 1.1l1.8 1.8a.8.8 0 0 0 1.2 0l3.8-4.4Z" clip-rule="evenodd" /></svg>
                {{ __('marketplace.products.verified', [], 'id') }}
            </span>
            @if ($product['preorder'])
                <span class="w-fit rounded-full bg-harvest px-2 py-1 text-[9px] font-extrabold text-forest sm:text-[10px]">{{ __('marketplace.products.preorder_badge', [], 'id') }}</span>
            @endif
        </div>
        <button type="button" aria-label="{{ __('marketplace.products.add_to_cart', [], 'id') }}: {{ __('marketplace.products.' . $product['name'], [], 'id') }}" class="absolute bottom-2 right-2 flex h-9 w-9 items-center justify-center rounded-full bg-harvest text-forest shadow-md transition hover:scale-110 sm:bottom-3 sm:right-3 sm:h-10 sm:w-10">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" /></svg>
        </button>
    </div>
    <div class="p-3 sm:p-4">
        <h3 class="line-clamp-2 min-h-10 text-sm font-extrabold leading-5 text-charcoal sm:text-base">{{ __('marketplace.products.' . $product['name'], [], 'id') }}</h3>
        <p class="mt-1 truncate text-[11px] text-charcoal/55 sm:text-xs">{{ __('marketplace.products.' . $product['farmer'], [], 'id') }} <span aria-hidden="true">·</span> {{ __('marketplace.products.' . $product['location'], [], 'id') }}</p>
        <div class="mt-3 flex flex-wrap items-baseline gap-x-1.5 gap-y-1">
            <span class="text-base font-extrabold text-forest sm:text-lg">{{ __('marketplace.products.currency_prefix', [], 'id') }} {{ number_format($product['price'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-charcoal/50 sm:text-xs">/ {{ __('marketplace.products.unit_kg', [], 'id') }}</span>
        </div>
    </div>
</article>
