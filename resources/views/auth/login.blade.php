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
            <div class="relative mt-2" x-data="{ showPassword: false }">
                <input id="password" name="password" x-bind:type="showPassword ? 'text' : 'password'" type="password" required autocomplete="current-password" class="block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 pr-12 text-sm text-charcoal shadow-sm placeholder:text-charcoal/35 focus:border-forest focus:ring-forest">
                <button type="button" x-on:click="showPassword = !showPassword" x-bind:aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" x-bind:aria-pressed="showPassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-charcoal/60 hover:text-forest focus:outline-none focus:ring-2 focus:ring-forest" >
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.036 11.68a1.012 1.012 0 0 0 0 .639C3.423 16.49 7.36 19.5 12 19.5c1.16 0 2.27-.198 3.3-.56M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.523 10.523 0 0 1-4.293 5.368M6.228 6.228 3 3m3.228 3.228 3.01 3.01m5.524 5.524 3.01 3.01M9.238 9.238a3 3 0 0 0 4.243 4.243" />
                    </svg>
                </button>
            </div>
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
