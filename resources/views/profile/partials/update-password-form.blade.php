<section class="rounded-3xl border border-forest/10 bg-white p-5 shadow-sm sm:p-8">
    <header class="mb-6">
        <h2 id="profile-security-heading" class="text-2xl font-extrabold tracking-tight text-charcoal">
            {{ __('marketplace.profile.security_title', [], 'id') }}
        </h2>

        <p class="mt-2 text-[15px] leading-6 text-charcoal/60">
            {{ __('auth-ui.update_password_description', [], 'id') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.current_password', [], 'id') }}</label>
            <x-password-input id="update_password_current_password" name="current_password" autocomplete="current-password" container-class="mt-2" class="text-[15px]" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.new_password', [], 'id') }}</label>
            <x-password-input id="update_password_password" name="password" autocomplete="new-password" container-class="mt-2" class="text-[15px]" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.confirm_password', [], 'id') }}</label>
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" container-class="mt-2" class="text-[15px]" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-forest px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                {{ __('auth-ui.save_changes', [], 'id') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-[15px] font-semibold text-green-800"
                    role="status"
                >{{ __('auth-ui.saved', [], 'id') }}</p>
            @endif
        </div>
    </form>
</section>
