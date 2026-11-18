@props(['align' => 'end'])
@php
    $current = app()->getLocale();
    $attributes_ = (array) config('export.locale_attributes', []);
    $currentLabel = $attributes_[$current]['label'] ?? strtoupper($current);
@endphp
<details class="group relative">
    <summary class="flex h-10 cursor-pointer list-none items-center gap-1.5 rounded-full border border-forest/10 px-3 text-sm font-semibold text-charcoal transition hover:border-forest/30 hover:bg-cream focus:outline-none focus-visible:ring-2 focus-visible:ring-forest [&::-webkit-details-marker]:hidden">
        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9S14.5 18.3 12 21c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/></svg>
        <span class="sr-only">{{ __('marketplace.nav.language') }}:</span>
        <span class="max-w-24 truncate">{{ $currentLabel }}</span>
        <svg class="h-4 w-4 shrink-0 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </summary>
    <ul class="absolute {{ $align === 'start' ? 'start-0' : 'end-0' }} top-full z-50 mt-2 max-h-80 w-56 overflow-y-auto rounded-2xl border border-forest/10 bg-white py-1 shadow-xl">
        @foreach ($attributes_ as $code => $attr)
            <li>
                <a href="{{ route('site.locale', $code) }}" lang="{{ $attr['lang'] }}" hreflang="{{ $attr['lang'] }}" dir="{{ $attr['dir'] }}"
                   @if ($code === $current) aria-current="true" @endif
                   class="flex min-h-10 items-center justify-between gap-3 px-4 text-sm font-semibold {{ $code === $current ? 'bg-cream text-forest' : 'text-charcoal/75 hover:bg-cream hover:text-forest' }}">
                    <span>{{ $attr['label'] }}</span>
                    @if ($code === $current)
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m5 12 5 5 9-10" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</details>