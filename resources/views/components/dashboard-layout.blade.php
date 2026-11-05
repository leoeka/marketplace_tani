@props([
    'menu' => [],
    'title' => '',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ __('marketplace.brand', [], 'id') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans text-charcoal antialiased" x-data="{ sidebarOpen: false }">
    @php
        $dashboardUser = Auth::user();
        $shopProfile = $dashboardUser->role === 'farmer' ? $dashboardUser->farmerProfile : null;
        $shopUrl = $shopProfile ? route('marketplace.farmers.show', $shopProfile->slug) : route('home');
    @endphp
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-charcoal/50 lg:hidden" @click="sidebarOpen = false" @keydown.escape.window="sidebarOpen = false"></div>

    <aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-forest/10 bg-white transition-transform duration-200 lg:translate-x-0"
        :class="{ 'translate-x-0': sidebarOpen }" aria-label="{{ __('dashboard.sidebar_label', [], 'id') }}">
        <div class="flex h-20 items-center gap-3 border-b border-forest/10 px-6">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-forest text-xl text-white" aria-hidden="true">🌱</span>
            <div>
                <p class="font-extrabold tracking-tight text-forest">{{ __('marketplace.brand', [], 'id') }}</p>
                <p class="text-xs text-charcoal/55">{{ __('dashboard.workspace', [], 'id') }}</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition',
                        'bg-forest text-white shadow-sm' => request()->routeIs($item['active'] ?? $item['route']),
                        'text-charcoal/70 hover:bg-cream hover:text-forest' => ! request()->routeIs($item['active'] ?? $item['route']),
                    ])
                    @click="sidebarOpen = false">
                    <span class="text-lg" aria-hidden="true">{{ $item['icon'] }}</span>
                    <span>{{ __('dashboard.menu.'.$item['label'], [], 'id') }}</span>
                </a>
            @endforeach
        </nav>

        <div class="border-t border-forest/10 p-4">
            <div class="rounded-2xl bg-cream p-4">
                <p class="text-sm font-bold text-forest">{{ Auth::user()->name }}</p>
                <p class="mt-1 truncate text-xs text-charcoal/60">{{ Auth::user()->email }}</p>
                <a href="{{ route('profile.edit') }}" class="mt-3 inline-flex rounded-lg px-2 py-1 text-sm font-bold text-forest transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                    {{ __('dashboard.account', [], 'id') }}
                </a>
            </div>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-72">
        <header class="sticky top-0 z-30 border-b border-forest/10 bg-white/95 backdrop-blur">
            <div class="flex min-h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-10">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-forest/15 text-forest lg:hidden"
                        @click="sidebarOpen = true" aria-label="{{ __('dashboard.open_menu', [], 'id') }}" aria-controls="dashboard-sidebar">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-width="2" /></svg>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.12em] text-forest/60 sm:text-xs sm:tracking-[0.16em]">{{ __('dashboard.greeting', ['name' => Auth::user()->name], 'id') }}</p>
                        <h1 class="truncate text-lg font-extrabold text-charcoal sm:text-xl">{{ $title }}</h1>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <a href="{{ $shopUrl }}" class="hidden rounded-xl border border-forest/20 px-4 py-2.5 text-sm font-bold text-forest transition hover:bg-cream sm:inline-flex">
                        {{ __('dashboard.view_shop', [], 'id') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl bg-harvest px-4 py-2.5 text-sm font-bold text-charcoal transition hover:bg-amber-400">
                            {{ __('marketplace.nav.logout', [], 'id') }}
                        </button>
                    </form>
                </div>
            </div>
            <a href="{{ $shopUrl }}" class="mx-4 mb-3 inline-flex rounded-xl border border-forest/20 px-4 py-2 text-sm font-bold text-forest sm:hidden">
                {{ __('dashboard.view_shop', [], 'id') }}
            </a>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-10">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800" role="status">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800" role="alert">{{ session('error') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</body>
</html>