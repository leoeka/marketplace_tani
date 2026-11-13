@php
    $exportLocales = config('export.locale_attributes', []);
    $activeLocale = app()->getLocale();
@endphp
<header class="border-b border-forest/10 bg-white">
    <div class="border-b border-forest/10 bg-[#f4f7f3]">
        <div class="mx-auto flex max-w-7xl items-center justify-end gap-3 px-5 py-1.5 text-xs font-semibold sm:px-8 lg:px-10">
            <span class="text-charcoal/50">{{ __('export.navigation.language') }}</span>
            @foreach ($exportLocales as $localeCode => $localeAttributes)
                <a href="{{ route('export.locale', $localeCode) }}" lang="{{ $localeAttributes['lang'] }}" hreflang="{{ $localeAttributes['lang'] }}" @if ($localeCode === $activeLocale) aria-current="true" @endif class="{{ $localeCode === $activeLocale ? 'text-forest underline underline-offset-4' : 'text-charcoal/60 transition hover:text-forest' }}">{{ $localeAttributes['label'] }}</a>
            @endforeach
        </div>
    </div>
    <nav aria-label="{{ __('export.navigation.label') }}" class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:px-8 sm:py-5 lg:px-10">
        <a href="{{ route('export.home') }}" class="text-xl font-extrabold tracking-tight text-forest sm:text-2xl">{{ __('export.brand') }}</a>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-semibold text-charcoal/70 sm:gap-6">
            <a href="{{ route('export.home') }}" class="transition hover:text-forest">{{ __('export.navigation.home') }}</a>
            <a href="{{ route('export.products.index') }}" class="transition hover:text-forest">{{ __('export.navigation.products') }}</a>
            <a href="{{ route('export.certifications.index') }}" class="transition hover:text-forest">{{ __('export.navigation.certifications') }}</a>
            <a href="{{ route('export.logistics.index') }}" class="transition hover:text-forest">{{ __('export.navigation.logistics') }}</a>
            <a href="{{ route('export.rfq.create') }}" class="rounded-lg bg-forest px-4 py-2.5 font-bold text-white transition hover:bg-forest/90">{{ __('export.navigation.rfq') }}</a>
        </div>
    </nav>
</header>