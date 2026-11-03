@php
    $navigationLinks = [
        ['label' => 'home', 'target' => '#home'],
        ['label' => 'products', 'target' => '#products'],
        ['label' => 'preorder', 'target' => '#preorder'],
        ['label' => 'farmers', 'target' => '#farmers'],
        ['label' => 'market_prices', 'target' => '#market-prices'],
        ['label' => 'education', 'target' => '#education'],
    ];
@endphp

<nav x-data="{ open: false }" aria-label="{{ __('marketplace.nav.home', [], 'id') }}" class="mx-auto max-w-7xl rounded-2xl bg-white px-4 py-3 shadow-[0_12px_35px_-24px_rgba(38,48,43,0.5)] sm:px-6">
    <div class="flex min-h-12 items-center justify-between gap-3">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ __('marketplace.brand', [], 'id') }}">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-harvest text-forest">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 20c-4.3-2.4-6.4-6.6-5.8-12.5C10.8 7.8 14 10 15.4 14c.5-3.5 2-6 5-7.5.8 6.5-1.6 11-7.3 13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5 4c2.6.1 4.3 1 5.2 2.8M12 20v-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                </svg>
            </span>
            <span class="text-lg font-extrabold tracking-tight text-forest sm:text-xl">{{ __('marketplace.brand', [], 'id') }}<span class="text-harvest">.</span></span>
        </a>

        <div class="hidden items-center gap-5 xl:flex">
            @foreach ($navigationLinks as $link)
                <a href="{{ route('home') . $link['target'] }}" class="text-xs font-semibold text-charcoal/70 transition hover:text-forest 2xl:text-sm">
                    {{ __('marketplace.nav.' . $link['label'], [], 'id') }}
                </a>
            @endforeach
        </div>

        <div class="hidden items-center gap-2 sm:flex">
            <a href="{{ route('home') }}#products" aria-label="{{ __('marketplace.nav.search', [], 'id') }}" class="flex h-10 w-10 items-center justify-center rounded-full text-charcoal transition hover:bg-cream hover:text-forest">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8" /><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
            </a>
            <a href="{{ route('home') }}#products" aria-label="{{ __('marketplace.nav.cart', [], 'id') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-forest transition hover:bg-harvest">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3.5 4h2l2.2 11.2a2 2 0 0 0 2 1.6h8.1a2 2 0 0 0 1.9-1.4l1.5-6.1H7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /><circle cx="10" cy="20" r="1.2" fill="currentColor" /><circle cx="18" cy="20" r="1.2" fill="currentColor" /></svg>
            </a>

            @auth
                <details class="group relative ml-1">
                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full border border-forest/10 px-3 py-2 text-sm font-semibold text-charcoal transition hover:border-forest/30 hover:bg-cream">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-forest text-xs font-bold text-white">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                        <span class="max-w-28 truncate">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.2 7.4a.75.75 0 0 1 1.05.05L10 11.3l3.75-3.85a.75.75 0 1 1 1.08 1.05l-4.3 4.4a.75.75 0 0 1-1.08 0l-4.3-4.4a.75.75 0 0 1 .05-1.05Z" clip-rule="evenodd" /></svg>
                    </summary>
                    <div class="absolute right-0 top-full z-50 mt-2 w-56 rounded-2xl border border-forest/10 bg-white p-2 shadow-xl">
                        @if (Auth::user()->role === 'farmer')
                            <a href="{{ route('farmer.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-forest hover:bg-cream">{{ __('marketplace.nav.farmer_dashboard', [], 'id') }}</a>
                        @elseif (Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-forest hover:bg-cream">{{ __('marketplace.nav.admin_dashboard', [], 'id') }}</a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-semibold text-forest hover:bg-cream">{{ __('marketplace.nav.account', [], 'id') }}</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-charcoal/70 hover:bg-cream">{{ __('marketplace.nav.logout', [], 'id') }}</button>
                        </form>
                    </div>
                </details>
            @else
                <a href="{{ route('login') }}" class="ml-1 rounded-full px-4 py-2 text-sm font-bold text-forest transition hover:bg-cream">{{ __('marketplace.nav.login', [], 'id') }}</a>
                <a href="{{ route('register') }}" class="rounded-full bg-harvest px-4 py-2 text-sm font-bold text-forest shadow-sm transition hover:bg-yellow-400">{{ __('marketplace.nav.register', [], 'id') }}</a>
            @endauth
        </div>

        <button type="button" @click="open = !open" :aria-expanded="open" aria-label="{{ __('marketplace.nav.open_menu', [], 'id') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-cream text-forest sm:hidden">
            <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
            <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
        </button>
    </div>

    <div x-show="open" x-cloak class="mt-3 border-t border-forest/10 pb-2 pt-3 sm:hidden">
        <div class="grid gap-1">
            @foreach ($navigationLinks as $link)
                <a @click="open = false" href="{{ route('home') . $link['target'] }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-charcoal/80 hover:bg-cream hover:text-forest">
                    {{ __('marketplace.nav.' . $link['label'], [], 'id') }}
                </a>
            @endforeach
        </div>
        <div class="mt-3 flex items-center gap-2 border-t border-forest/10 pt-3">
            <a href="{{ route('home') }}#products" aria-label="{{ __('marketplace.nav.search', [], 'id') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-forest">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8" /><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
            </a>
            <a href="{{ route('home') }}#products" aria-label="{{ __('marketplace.nav.cart', [], 'id') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-cream text-forest">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3.5 4h2l2.2 11.2a2 2 0 0 0 2 1.6h8.1a2 2 0 0 0 1.9-1.4l1.5-6.1H7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /><circle cx="10" cy="20" r="1.2" fill="currentColor" /><circle cx="18" cy="20" r="1.2" fill="currentColor" /></svg>
            </a>
            @auth
                <div class="ml-auto flex flex-wrap items-center justify-end gap-2">
                    @if (Auth::user()->role === 'farmer')
                        <a href="{{ route('farmer.dashboard') }}" class="rounded-full bg-forest px-4 py-2 text-xs font-bold text-white">{{ __('marketplace.nav.farmer_dashboard', [], 'id') }}</a>
                    @elseif (Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full bg-forest px-4 py-2 text-xs font-bold text-white">{{ __('marketplace.nav.admin_dashboard', [], 'id') }}</a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="rounded-full bg-forest px-4 py-2 text-xs font-bold text-white">{{ __('marketplace.nav.account', [], 'id') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full px-3 py-2 text-xs font-bold text-charcoal">{{ __('marketplace.nav.logout', [], 'id') }}</button>
                    </form>
                </div>
            @else
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('login') }}" class="rounded-full px-3 py-2 text-sm font-bold text-forest">{{ __('marketplace.nav.login', [], 'id') }}</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-harvest px-4 py-2 text-sm font-bold text-forest">{{ __('marketplace.nav.register', [], 'id') }}</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
