<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ __('marketplace.meta_description', [], 'id') }}">
        <title>{{ __('marketplace.meta_title', [], 'id') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="bg-cream font-sans text-charcoal antialiased">
        <div class="min-h-screen overflow-hidden">
            <header class="relative z-30 px-3 pt-3 sm:px-6 sm:pt-5 lg:px-8">
                <x-marketplace.navbar />
            </header>

            <main>
                @if ($errors->any())
                    <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
                        <ul class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success') || session('error'))
                    <div class="mx-auto mt-5 max-w-7xl px-4 sm:px-6 lg:px-8">
                        <p class="rounded-xl {{ session('error') ? 'border border-red-200 bg-red-50 text-red-800' : 'border border-green-200 bg-green-50 text-green-800' }} px-4 py-3 text-sm font-semibold" role="{{ session('error') ? 'alert' : 'status' }}">{{ session('error') ?? session('success') }}</p>
                    </div>
                @endif
                {{ $slot }}
            </main>

            <x-marketplace.footer />
        </div>
    </body>
</html>
