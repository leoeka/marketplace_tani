@php
    $profileTitle = __('marketplace.profile.title');
@endphp

@if ($user->role === 'customer')
    <x-marketplace.layout>
        <section class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <header class="mb-7">
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-forest/65">
                    {{ __('marketplace.brand') }}</p>
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal sm:text-4xl">{{ $profileTitle }}</h1>
                <p class="mt-2 text-[15px] leading-6 text-charcoal/60">{{ __('marketplace.profile.description') }}
                </p>
            </header>

            @include('profile.partials.profile-sections')
            @include('profile.partials.location-consent')

        </section>
    </x-marketplace.layout>
@else
    @php
        $dashboardMenu = config('dashboard.' . $user->role . '_menu', []);
    @endphp

    <x-dashboard-layout :menu="$dashboardMenu" :title="$profileTitle">
        <p class="mb-7 text-[15px] leading-6 text-charcoal/60">{{ __('marketplace.profile.description') }}</p>
        @include('profile.partials.profile-sections')
        @include('profile.partials.location-consent')
    </x-dashboard-layout>
@endif