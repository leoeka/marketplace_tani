@props(['farmer'])

@php
    $avatarTones = ['bg-[#E7EFD9] text-forest', 'bg-[#F7E7D2] text-[#8F5C2B]', 'bg-[#F4DFD8] text-[#984A3A]'];
@endphp

<article class="flex gap-4 rounded-3xl border border-forest/5 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:p-6">
    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl {{ $avatarTones[$farmer['tone']] ?? 'bg-cream text-forest' }} text-2xl font-extrabold" role="img" aria-label="{{ __('marketplace.farmers.avatar', [], 'id') }}: {{ __('marketplace.farmers.' . $farmer['name'], [], 'id') }}">
        {{ $farmer['initials'] }}
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold uppercase tracking-wide text-forest/60">{{ __('marketplace.farmers.' . $farmer['shop'], [], 'id') }}</p>
        <h3 class="mt-1 text-lg font-extrabold text-charcoal">{{ __('marketplace.farmers.' . $farmer['name'], [], 'id') }}</h3>
        <p class="mt-1 flex items-center gap-1 text-xs text-charcoal/55">
            <svg class="h-3.5 w-3.5 text-forest" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18s6-5.1 6-10a6 6 0 1 0-12 0c0 4.9 6 10 6 10Zm0-7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" clip-rule="evenodd" /></svg>
            {{ __('marketplace.farmers.' . $farmer['location'], [], 'id') }}
        </p>
        <p class="mt-3 text-sm leading-6 text-charcoal/65">{{ __('marketplace.farmers.' . $farmer['story'], [], 'id') }}</p>
    </div>
</article>
