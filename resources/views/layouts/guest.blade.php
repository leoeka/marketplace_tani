<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ __('marketplace.brand', [], 'id') }} — {{ request()->routeIs('register') ? __('auth-ui.register', [], 'id') : __('auth-ui.login', [], 'id') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="bg-cream font-sans text-charcoal antialiased">
        <main class="min-h-screen lg:grid lg:grid-cols-[0.9fr_1.1fr]">
            <section class="relative isolate flex min-h-[18rem] flex-col justify-between overflow-hidden bg-forest px-6 py-7 text-white sm:px-10 sm:py-9 lg:min-h-screen lg:px-14 lg:py-12">
                <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-harvest/10 blur-2xl" aria-hidden="true"></div>
                <div class="absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-white/5 blur-2xl" aria-hidden="true"></div>
                <a href="{{ route('home') }}" class="relative z-10 inline-flex w-fit items-center gap-2.5 text-xl font-extrabold tracking-tight">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-harvest text-forest" aria-hidden="true">✦</span>
                    {{ __('marketplace.brand', [], 'id') }}<span class="text-harvest">.</span>
                </a>
                <div class="relative z-10 my-10 max-w-xl lg:my-auto lg:py-16">
                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-harvest">{{ __('auth-ui.eyebrow', [], 'id') }}</span>
                    <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">{{ __('auth-ui.brand_heading', [], 'id') }}</h1>
                    <p class="mt-4 max-w-md text-sm leading-7 text-white/70 sm:text-base">{{ __('auth-ui.brand_description', [], 'id') }}</p>
                    <svg class="mt-8 h-20 w-40 text-harvest/80" viewBox="0 0 160 80" fill="none" aria-hidden="true"><path d="M12 67C36 30 71 15 119 20M40 57c4-25-3-41-22-49 0 23 7 39 22 49ZM72 40c8-25 23-37 47-36-5 24-19 39-42 43ZM103 26c13-19 30-25 52-18-10 21-26 29-48 24" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 11c-2 4-2 8 0 12M119 4c-2 5-2 10 0 15M153 8c-2 4-4 8-7 11" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                </div>
                <p class="relative z-10 hidden text-xs text-white/45 lg:block">{{ __('marketplace.footer.tagline', [], 'id') }}</p>
            </section>

            <section class="flex items-center justify-center px-4 py-10 sm:px-8 lg:px-12 lg:py-16">
                <div class="w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-bold text-forest transition hover:text-forest/70">
                        <span aria-hidden="true">←</span> {{ __('auth-ui.back_home', [], 'id') }}
                    </a>
                    <div class="rounded-[2rem] border border-forest/5 bg-white p-6 shadow-[0_25px_70px_-45px_rgba(38,48,43,0.4)] sm:p-9">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
