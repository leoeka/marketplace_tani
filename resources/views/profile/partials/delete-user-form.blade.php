<section class="space-y-6 rounded-3xl border border-red-200 border-l-4 border-l-red-600 bg-red-50/40 p-5 shadow-sm sm:p-8">
    <header>
        <h2 id="profile-delete-heading" class="text-2xl font-extrabold tracking-tight text-charcoal">
            {{ __('auth-ui.delete_account', [], 'id') }}
        </h2>

        <p class="mt-2 text-[15px] leading-6 text-charcoal/60">
            {{ __('auth-ui.delete_account_warning', [], 'id') }}
        </p>
    </header>

    <button type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex min-h-11 items-center justify-center rounded-xl bg-red-700 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2"
    >{{ __('auth-ui.delete_account', [], 'id') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-extrabold tracking-tight text-charcoal">
                {{ __('auth-ui.delete_account_confirm_title', [], 'id') }}
            </h2>

            <p class="mt-2 text-[15px] leading-6 text-charcoal/60">
                {{ __('auth-ui.delete_account_confirm_description', [], 'id') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">{{ __('auth-ui.password', [], 'id') }}</label>

                <x-password-input
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    container-class="mt-2 w-full"
                    class="text-[15px]"
                    :placeholder="__('auth-ui.password', [], 'id')"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-forest/20 bg-white px-5 py-3 text-sm font-bold text-forest transition hover:bg-cream focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                    {{ __('auth-ui.cancel', [], 'id') }}
                </button>

                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-red-700 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">
                    {{ __('auth-ui.delete_account', [], 'id') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
