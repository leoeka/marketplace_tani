@props(['status'])

@php
    $statusTones = [
        'draft' => 'bg-stone-100 text-stone-700',
        'pending' => 'bg-amber-100 text-amber-900',
        'approved' => 'bg-green-100 text-green-900',
        'rejected' => 'bg-red-100 text-red-900',
    ];
    $tone = $statusTones[$status] ?? 'bg-stone-100 text-stone-700';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 rounded-full px-3 py-1 text-xs font-bold '.$tone]) }}>{{ __('farmer.statuses.'.$status, [], 'id') }}</span>