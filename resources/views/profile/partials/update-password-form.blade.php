<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('auth-ui.update_password_title', [], 'id') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('auth-ui.update_password_description', [], 'id') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('auth-ui.current_password', [], 'id')" />
            <x-password-input id="update_password_current_password" name="current_password" autocomplete="current-password" container-class="mt-1" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('auth-ui.new_password', [], 'id')" />
            <x-password-input id="update_password_password" name="password" autocomplete="new-password" container-class="mt-1" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('auth-ui.confirm_password', [], 'id')" />
            <x-password-input id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password" container-class="mt-1" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('auth-ui.save_changes', [], 'id') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('auth-ui.saved', [], 'id') }}</p>
            @endif
        </div>
    </form>
</section>
