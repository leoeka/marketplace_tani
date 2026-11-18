<x-export.lang-banner />
@php
    $exportLocales = config('export.locale_attributes', []);
    $activeLocale = app()->getLocale();
    $activeLabel = $exportLocales[$activeLocale]['label'] ?? strtoupper($activeLocale);

    $navLinks = [
        ['route' => 'export.home', 'label' => __('export.navigation.home')],
        ['route' => 'export.products.index', 'label' => __('export.navigation.products')],
        ['route' => 'export.certifications.index', 'label' => __('export.navigation.certifications')],
        ['route' => 'export.logistics.index', 'label' => __('export.navigation.logistics')],
    ];
@endphp
<header class="sticky top-0 z-40 border-b border-forest/10 bg-white">
    <nav aria-label="{{ __('export.navigation.label') }}" class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-8 lg:px-10">
        <a href="{{ route('export.home') }}" class="shrink-0 text-xl font-extrabold tracking-tight text-forest sm:text-2xl">{{ __('export.brand') }}</a>

        {{-- Menu desktop --}}
        <div class="hidden items-center gap-6 text-sm font-semibold text-charcoal/70 md:flex">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" class="transition hover:text-forest">{{ $link['label'] }}</a>
            @endforeach
            <a href="{{ route('export.rfq.create') }}" class="rounded-lg bg-forest px-4 py-2.5 font-bold text-white transition hover:bg-forest/90">{{ __('export.navigation.rfq') }}</a>
        </div>

        <div class="flex items-center gap-2">
            {{-- Pemilih bahasa: dropdown ringkas, tanpa JavaScript --}}
            <details class="group relative">
                <summary class="flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-lg border border-forest/20 px-3 text-sm font-bold text-forest transition hover:bg-forest/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-forest [&::-webkit-details-marker]:hidden">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9S14.5 18.3 12 21c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/></svg>
                    <span class="sr-only">{{ __('export.navigation.language') }}:</span>
                    <span>{{ $activeLabel }}</span>
                    <svg class="h-4 w-4 shrink-0 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </summary>
                <ul class="absolute end-0 top-full z-50 mt-2 max-h-80 w-56 overflow-y-auto rounded-xl border border-forest/15 bg-white py-1 shadow-lg">
                    @foreach ($exportLocales as $localeCode => $localeAttributes)
                        <li>
                            <a href="{{ route('export.locale', $localeCode) }}" lang="{{ $localeAttributes['lang'] }}" hreflang="{{ $localeAttributes['lang'] }}" dir="{{ $localeAttributes['dir'] }}" @if ($localeCode === $activeLocale) aria-current="true" @endif class="flex min-h-11 items-center justify-between gap-3 px-4 text-sm font-semibold {{ $localeCode === $activeLocale ? 'bg-forest/5 text-forest' : 'text-charcoal/75 hover:bg-forest/5 hover:text-forest' }}">
                                <span>{{ $localeAttributes['label'] }}</span>
                                @if ($localeCode === $activeLocale)
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m5 12 5 5 9-10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </details>

            {{-- Menu mobile: tombol hamburger, panel melebar penuh di bawah header --}}
            <details class="group md:hidden">
                <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-lg border border-forest/20 text-forest transition hover:bg-forest/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-forest [&::-webkit-details-marker]:hidden" aria-label="Menu">
                    <svg class="h-6 w-6 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>
                    <svg class="hidden h-6 w-6 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke-linecap="round"/></svg>
                </summary>
                <div class="absolute inset-x-0 top-full z-40 border-b border-forest/10 bg-white px-4 pb-4 pt-2 shadow-lg sm:px-8">
                    <ul class="divide-y divide-forest/10 text-base font-semibold text-charcoal/80">
                        @foreach ($navLinks as $link)
                            <li><a href="{{ route($link['route']) }}" class="flex min-h-12 items-center transition hover:text-forest">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                    <a href="{{ route('export.rfq.create') }}" class="mt-3 flex min-h-12 items-center justify-center rounded-lg bg-forest px-4 font-bold text-white transition hover:bg-forest/90">{{ __('export.navigation.rfq') }}</a>
                </div>
            </details>
        </div>
    </nav>
</header>