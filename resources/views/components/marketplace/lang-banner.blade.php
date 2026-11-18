@php
    $suggested = $suggestedLocale ?? null;
    $attrs = $suggested ? config('export.locale_attributes.'.$suggested, []) : [];

    if ($suggested && $attrs) {
        if (\App\Support\MachineTranslator::groupNeedsMachine($suggested, 'marketplace')) {
            // Bahasa tanpa file sendiri: teks banner diterjemahkan dari English (kecil, disimpan di cache).
            $mt = app(\App\Support\MachineTranslator::class);
            $suggestText = $mt->text(__('marketplace.lang_suggest_generic', [], 'en'), $suggested, ['language' => $attrs['label']]);
            $switchText = $mt->text(__('marketplace.lang_switch', [], 'en'), $suggested);
        } else {
            $suggestText = __('marketplace.lang_suggest', [], $suggested);
            $switchText = __('marketplace.lang_switch', [], $suggested);
        }
    }
@endphp
@if ($suggested && $attrs)
    <div role="region" aria-label="Language suggestion" class="border-b border-forest/10 bg-white text-sm text-charcoal">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-2.5 sm:px-6">
            <p lang="{{ $attrs['lang'] }}" dir="{{ $attrs['dir'] }}">{{ $suggestText }}</p>
            <div class="flex items-center gap-2">
                <a href="{{ route('site.locale', $suggested) }}" lang="{{ $attrs['lang'] }}" dir="{{ $attrs['dir'] }}" class="rounded-full bg-forest px-4 py-1.5 font-bold text-white transition hover:bg-forest/90">{{ $switchText }}</a>
                <a href="{{ route('site.locale', config('export.default_locale', 'en')) }}" class="rounded-full border border-charcoal/20 px-4 py-1.5 font-bold transition hover:bg-cream">{{ __('marketplace.lang_keep') }}</a>
            </div>
        </div>
    </div>
@endif