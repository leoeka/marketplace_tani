@props(['title'])
<!DOCTYPE html>
<html lang="{{ config('export.locale_attributes.'.app()->getLocale().'.lang') }}" dir="{{ config('export.locale_attributes.'.app()->getLocale().'.dir') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ __('export.meta_description') }}">
        <title>{{ $title }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-sans text-charcoal antialiased">
        <div class="min-h-screen">
            <x-export.navbar />
            <main>
                {{ $slot }}
            </main>
            <x-export.footer />
        </div>
    </body>
</html>