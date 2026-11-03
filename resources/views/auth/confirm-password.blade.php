<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/60">{{ __('auth-ui.eyebrow', [], 'id') }}</p>
        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal">{{ __('auth-ui.confirm_password_title', [], 'id') }}</h2>
        <p class="mt-2 text-sm leading-6 text-charcoal/60">{{ __('auth-ui.confirm_password_description', [], 'id') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="grid gap-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.password', [], 'id') }}</label>
            <x-password-input id="password" name="password" required autocomplete="current-password" container-class="mt-2" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-forest px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-forest/15 transition hover:-translate-y-0.5 hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
            {{ __('auth-ui.confirm', [], 'id') }}
        </button>
    </form>
</x-guest-layout>
