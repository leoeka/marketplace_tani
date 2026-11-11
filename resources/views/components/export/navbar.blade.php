<header class="border-b border-forest/10 bg-white">
    <nav aria-label="{{ __('export.navigation.label') }}" class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-5 sm:px-8 lg:px-10">
        <a href="{{ route('export.home') }}" class="text-xl font-extrabold tracking-tight text-forest sm:text-2xl">{{ __('export.brand') }}</a>
        <div class="flex items-center gap-6 text-sm font-semibold text-charcoal/70">
            <a href="{{ route('export.home') }}" class="transition hover:text-forest">{{ __('export.navigation.home') }}</a>
            <a href="{{ route('export.products.index') }}" class="transition hover:text-forest">{{ __('export.navigation.products') }}</a>
            <a href="{{ route('export.certifications.index') }}" class="transition hover:text-forest">{{ __('export.navigation.certifications') }}</a>
        </div>
    </nav>
</header>