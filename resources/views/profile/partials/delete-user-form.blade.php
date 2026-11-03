<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('auth-ui.delete_account', [], 'id') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('auth-ui.delete_account_warning', [], 'id') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('auth-ui.delete_account', [], 'id') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('auth-ui.delete_account_confirm_title', [], 'id') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('auth-ui.delete_account_confirm_description', [], 'id') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-password-input
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    container-class="mt-2 w-3/4"
                    placeholder="{{ __('auth-ui.password', [], 'id') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('auth-ui.cancel', [], 'id') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('auth-ui.delete_account', [], 'id') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
