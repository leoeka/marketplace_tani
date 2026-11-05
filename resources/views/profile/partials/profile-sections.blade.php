@php
    $profileNameParts = preg_split('/\s+/', trim($user->name), -1, PREG_SPLIT_NO_EMPTY);
    $profileInitial = collect($profileNameParts)->take(2)->map(static fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    $activeSection = $errors->updatePassword->isNotEmpty()
        ? 'security'
        : ($errors->userDeletion->isNotEmpty() ? 'delete' : 'information');
@endphp

<div x-data="{ activeSection: @js($activeSection) }" class="grid min-w-0 gap-6 lg:grid-cols-[17rem_minmax(0,1fr)] lg:gap-8">
    <aside class="space-y-4 lg:sticky lg:top-28 lg:self-start">
        <section class="rounded-3xl border border-forest/10 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-forest text-xl font-extrabold uppercase text-white" aria-hidden="true">{{ $profileInitial }}</div>
                <div class="min-w-0">
                    <p class="truncate text-base font-extrabold text-charcoal">{{ $user->name }}</p>
                    <p class="mt-1 break-all text-[15px] leading-5 text-charcoal/60">{{ $user->email }}</p>
                </div>
            </div>

            @if ($user->role === 'customer')
                <p class="mt-5 inline-flex rounded-full bg-cream px-3 py-1.5 text-sm font-bold text-charcoal">
                    {{ __('marketplace.profile.'.$user->customer_type, [], 'id') }}
                </p>
            @endif
        </section>

        <nav class="rounded-3xl border border-forest/10 bg-white p-3 shadow-sm" aria-label="{{ __('marketplace.profile.section_navigation', [], 'id') }}">
            <div class="grid gap-1">
                <button type="button" x-on:click="activeSection = 'information'" x-bind:aria-current="activeSection === 'information' ? 'page' : null"
                    x-bind:class="activeSection === 'information' ? 'bg-forest text-white' : 'text-charcoal/75 hover:bg-cream'"
                    class="w-full rounded-xl px-4 py-3 text-left text-[15px] font-bold transition focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                    {{ __('marketplace.profile.information_title', [], 'id') }}
                </button>
                <button type="button" x-on:click="activeSection = 'security'" x-bind:aria-current="activeSection === 'security' ? 'page' : null"
                    x-bind:class="activeSection === 'security' ? 'bg-forest text-white' : 'text-charcoal/75 hover:bg-cream'"
                    class="w-full rounded-xl px-4 py-3 text-left text-[15px] font-bold transition focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                    {{ __('marketplace.profile.security_title', [], 'id') }}
                </button>
                <button type="button" x-on:click="activeSection = 'delete'" x-bind:aria-current="activeSection === 'delete' ? 'page' : null"
                    x-bind:class="activeSection === 'delete' ? 'bg-red-50 text-red-700' : 'text-charcoal/75 hover:bg-red-50 hover:text-red-700'"
                    class="w-full rounded-xl px-4 py-3 text-left text-[15px] font-bold transition focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">
                    {{ __('auth-ui.delete_account', [], 'id') }}
                </button>
            </div>
        </nav>
    </aside>

    <div class="min-w-0">
        <div x-cloak x-show="activeSection === 'information'" id="profile-information" aria-labelledby="profile-information-heading">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div x-cloak x-show="activeSection === 'security'" id="profile-security" aria-labelledby="profile-security-heading">
            @include('profile.partials.update-password-form')
        </div>
        <div x-cloak x-show="activeSection === 'delete'" id="profile-delete-account" aria-labelledby="profile-delete-heading">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>