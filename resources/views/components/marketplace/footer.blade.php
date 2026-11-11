<footer class="relative bg-charcoal text-white">
    <svg class="absolute -top-px left-0 h-16 w-full" viewBox="0 0 1440 100" preserveAspectRatio="none" aria-hidden="true"><path d="M0 0h1440v34c-112 47-199 41-311 6-103-32-180-31-283 4-109 37-188 33-292-5C441 1 368 8 260 48 165 83 92 80 0 43V0Z" fill="#FAF7F0" /></svg>
    <div class="mx-auto grid max-w-7xl gap-10 px-5 pb-8 pt-28 sm:grid-cols-2 sm:px-8 sm:pt-32 lg:grid-cols-4 lg:gap-8 lg:px-10">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xl font-extrabold tracking-tight text-white">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-harvest text-forest" aria-hidden="true">✦</span>
                {{ __('marketplace.brand', [], 'id') }}<span class="text-harvest">.</span>
            </a>
            <p class="mt-4 max-w-xs text-sm leading-6 text-white/60">{{ __('marketplace.footer.tagline', [], 'id') }}</p>
            <p class="mt-7 text-xs text-white/40">© {{ date('Y') }} {{ __('marketplace.footer.copyright', [], 'id') }}</p>
        </div>

        <div>
            <h2 class="text-sm font-extrabold text-white">{{ __('marketplace.footer.explore', [], 'id') }}</h2>
            <ul class="mt-4 grid gap-3 text-sm text-white/60">
                <li><a href="{{ route('home') }}#products" class="transition hover:text-harvest">{{ __('marketplace.nav.products', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#preorder" class="transition hover:text-harvest">{{ __('marketplace.nav.preorder', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#farmers" class="transition hover:text-harvest">{{ __('marketplace.nav.farmers', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#education" class="transition hover:text-harvest">{{ __('marketplace.nav.education', [], 'id') }}</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-extrabold text-white">{{ __('marketplace.footer.support', [], 'id') }}</h2>
            <ul class="mt-4 grid gap-3 text-sm text-white/60">
                <li><a href="{{ route('home') }}#about" class="transition hover:text-harvest">{{ __('marketplace.footer.about', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#contact" class="transition hover:text-harvest">{{ __('marketplace.footer.contact', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#faq" class="transition hover:text-harvest">{{ __('marketplace.footer.faq', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#terms" class="transition hover:text-harvest">{{ __('marketplace.footer.terms', [], 'id') }}</a></li>
                <li><a href="{{ route('home') }}#privacy" class="transition hover:text-harvest">{{ __('marketplace.footer.privacy', [], 'id') }}</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-extrabold text-white">{{ __('marketplace.footer.export', [], 'id') }}</h2>
            <p class="mt-4 text-sm leading-6 text-white/60">{{ __('marketplace.footer.export_description', [], 'id') }}</p>
            <a href="{{ route('export.home') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-harvest px-4 py-2.5 text-sm font-bold text-forest transition hover:bg-yellow-400">
                {{ __('marketplace.footer.export', [], 'id') }} <span aria-hidden="true">↗</span>
            </a>
            <h3 class="mt-7 text-xs font-bold uppercase tracking-wider text-white/50">{{ __('marketplace.footer.social', [], 'id') }}</h3>
            <div class="mt-3 flex gap-2" aria-label="{{ __('marketplace.footer.social', [], 'id') }}">
                <a href="#social" aria-label="{{ __('marketplace.footer.instagram', [], 'id') }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 text-xs font-bold text-white transition hover:border-harvest hover:text-harvest">ig</a>
                <a href="#social" aria-label="{{ __('marketplace.footer.facebook', [], 'id') }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 text-xs font-bold text-white transition hover:border-harvest hover:text-harvest">f</a>
                <a href="#social" aria-label="{{ __('marketplace.footer.tiktok', [], 'id') }}" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 text-xs font-bold text-white transition hover:border-harvest hover:text-harvest">♪</a>
            </div>
        </div>
    </div>
</footer>
