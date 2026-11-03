<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/60">{{ __('auth-ui.eyebrow', [], 'id') }}</p>
        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal">{{ __('auth-ui.login_title', [], 'id') }}</h2>
        <p class="mt-2 text-sm leading-6 text-charcoal/60">{{ __('auth-ui.login_description', [], 'id') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="grid gap-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.email', [], 'id') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm placeholder:text-charcoal/35 focus:border-forest focus:ring-forest">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.password', [], 'id') }}</label>
            <x-password-input id="password" name="password" autocomplete="current-password" required container-class="mt-2" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-charcoal/70">
                <input id="remember_me" name="remember" type="checkbox" class="rounded border-forest/25 text-forest shadow-sm focus:ring-forest">
                <span>{{ __('auth-ui.remember', [], 'id') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-forest underline decoration-harvest decoration-2 underline-offset-4 hover:text-forest/70" href="{{ route('password.request') }}">{{ __('auth-ui.forgot_password', [], 'id') }}</a>
            @endif
        </div>

        <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-forest px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-forest/15 transition hover:-translate-y-0.5 hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
            {{ __('auth-ui.login', [], 'id') }}
        </button>
    </form>

    <p class="mt-6 border-t border-forest/10 pt-5 text-center text-sm text-charcoal/60">
        {{ __('auth-ui.not_registered', [], 'id') }}
        <a href="{{ route('register') }}" class="font-bold text-forest underline decoration-harvest decoration-2 underline-offset-4">{{ __('auth-ui.register', [], 'id') }}</a>
    </p>
</x-guest-layout>
