<section class="rounded-3xl border border-forest/10 bg-white p-5 shadow-sm sm:p-8">
    @php
        /** @var \App\Models\User $profileAccount */
        $profileAccount = $user;
    @endphp
    <header class="mb-6">
        <h2 id="profile-information-heading" class="text-2xl font-extrabold tracking-tight text-charcoal">
            {{ __('marketplace.profile.information_title', [], 'id') }}
        </h2>

        <p class="mt-2 text-[15px] leading-6 text-charcoal/60">
            {{ __('marketplace.profile.information_description', [], 'id') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.name', [], 'id') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="mt-2 block w-full rounded-2xl border border-forest/15 bg-cream/50 px-4 py-3 text-[15px] text-charcoal shadow-sm placeholder:text-charcoal/35 focus:border-forest focus:ring-forest">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="block text-sm font-bold text-charcoal">{{ __('auth-ui.email', [], 'id') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="mt-2 block w-full rounded-2xl border border-forest/15 bg-cream/50 px-4 py-3 text-[15px] text-charcoal shadow-sm placeholder:text-charcoal/35 focus:border-forest focus:ring-forest">
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div>
                        <p class="mt-3 text-[15px] leading-6 text-amber-800">
                            {{ __('auth-ui.email_unverified', [], 'id') }}

                            <button form="send-verification" class="font-bold text-forest underline decoration-forest/40 underline-offset-4 hover:text-forest/80 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                                {{ __('auth-ui.resend_verification_email', [], 'id') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-[15px] font-semibold text-green-800" role="status">
                                {{ __('auth-ui.verification_link_sent', [], 'id') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if ($profileAccount->role === 'customer')
            <div>
                <label for="customer_type" class="block text-sm font-bold text-charcoal">{{ __('marketplace.profile.customer_type', [], 'id') }}</label>
                <select id="customer_type" name="customer_type" required class="mt-2 block w-full rounded-2xl border border-forest/15 bg-cream/50 px-4 py-3 text-[15px] text-charcoal shadow-sm focus:border-forest focus:ring-forest">
                    <option value="individual" @selected(old('customer_type', $profileAccount->customer_type) === 'individual')>{{ __('marketplace.profile.individual', [], 'id') }}</option>
                    <option value="business" @selected(old('customer_type', $profileAccount->customer_type) === 'business')>{{ __('marketplace.profile.business', [], 'id') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('customer_type')" />
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-forest px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                {{ __('auth-ui.save_changes', [], 'id') }}
            </button>

            @if (session('status') === 'profile-updated')
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
