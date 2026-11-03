<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/60">{{ __('auth-ui.eyebrow', [], 'id') }}</p>
        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal">{{ __('auth-ui.forgot_password_title', [], 'id') }}</h2>
        <p class="mt-2 text-sm leading-6 text-charcoal/60">{{ __('auth-ui.forgot_password_description', [], 'id') }}</p>
    </div>

    @if (session('status'))
        <p class="mb-5 rounded-2xl bg-forest/5 px-4 py-3 text-sm font-medium leading-6 text-forest">
            {{ __('auth-ui.reset_link_sent', [], 'id') }}
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="grid gap-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.email', [], 'id') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm placeholder:text-charcoal/35 focus:border-forest focus:ring-forest">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-forest px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-forest/15 transition hover:-translate-y-0.5 hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                {{ __('auth-ui.send_reset_link', [], 'id') }}
            </button>
        </div>
    </form>

    <p class="mt-6 border-t border-forest/10 pt-5 text-center text-sm text-charcoal/60">
        <a href="{{ route('login') }}" class="font-bold text-forest underline decoration-harvest decoration-2 underline-offset-4">{{ __('auth-ui.back_to_login', [], 'id') }}</a>
    </p>
</x-guest-layout>
