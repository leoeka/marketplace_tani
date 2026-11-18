@php
    $suggested = $suggestedLocale ?? null;
    $attrs = $suggested ? config('export.locale_attributes.'.$suggested, []) : [];

    if ($suggested && $attrs) {
        if (\App\Support\MachineTranslator::isMachineLocale($suggested)) {
            // Bahasa otomatis: teks banner diterjemahkan dari English (kecil, disimpan di cache).
            $mt = app(\App\Support\MachineTranslator::class);
            $suggestText = $mt->text(__('export.lang_suggest_generic', [], 'en'), $suggested, ['language' => $attrs['label']]);
            $switchText = $mt->text(__('export.lang_switch', [], 'en'), $suggested);
        } else {
            $suggestText = __('export.lang_suggest', [], $suggested);
            $switchText = __('export.lang_switch', [], $suggested);
        }
    }
@endphp
@if ($suggested && $attrs)
    <div role="region" aria-label="Language suggestion" class="border-b border-forest/10 bg-forest/5 text-sm text-charcoal">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-2.5 sm:px-6">
            <p lang="{{ $attrs['lang'] }}" dir="{{ $attrs['dir'] }}">{{ $suggestText }}</p>
            <div class="flex items-center gap-2">
                <a href="{{ route('export.locale', $suggested) }}" lang="{{ $attrs['lang'] }}" dir="{{ $attrs['dir'] }}" class="rounded-lg bg-forest px-3 py-1.5 font-bold text-white transition hover:bg-forest/90">{{ $switchText }}</a>
                <a href="{{ route('export.locale', config('export.default_locale', 'en')) }}" class="rounded-lg border border-charcoal/20 px-3 py-1.5 font-bold transition hover:bg-white">{{ __('export.lang_keep') }}</a>
            </div>
        </div>
    </div>
@endif