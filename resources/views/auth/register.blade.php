<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/60">{{ __('auth-ui.eyebrow', [], 'id') }}</p>
        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal">{{ __('auth-ui.register_title', [], 'id') }}</h2>
        <p class="mt-2 text-sm leading-6 text-charcoal/60">{{ __('auth-ui.register_description', [], 'id') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="grid gap-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.name', [], 'id') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm focus:border-forest focus:ring-forest">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.email', [], 'id') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm focus:border-forest focus:ring-forest">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="role" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.role', [], 'id') }}</label>
            <select id="role" name="role" required class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm focus:border-forest focus:ring-forest">
                <option value="customer" @selected(old('role', 'customer') === 'customer')>{{ __('auth-ui.customer', [], 'id') }}</option>
                <option value="farmer" @selected(old('role') === 'farmer')>{{ __('auth-ui.farmer', [], 'id') }}</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.password', [], 'id') }}</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm focus:border-forest focus:ring-forest">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.confirm_password', [], 'id') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 block w-full rounded-2xl border-forest/15 bg-cream/50 px-4 py-3 text-sm text-charcoal shadow-sm focus:border-forest focus:ring-forest">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="mt-1 inline-flex min-h-12 w-full items-center justify-center rounded-full bg-forest px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-forest/15 transition hover:-translate-y-0.5 hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
            {{ __('auth-ui.register', [], 'id') }}
        </button>
    </form>

    <p class="mt-5 border-t border-forest/10 pt-4 text-center text-sm text-charcoal/60">
        {{ __('auth-ui.already_registered', [], 'id') }}
        <a href="{{ route('login') }}" class="font-bold text-forest underline decoration-harvest decoration-2 underline-offset-4">{{ __('auth-ui.login', [], 'id') }}</a>
    </p>
</x-guest-layout>
