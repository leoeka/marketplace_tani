<x-guest-layout>
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-forest/60">{{ __('auth-ui.eyebrow', [], 'id') }}</p>
        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-charcoal">{{ __('auth-ui.verify_email_title', [], 'id') }}</h2>
        <p class="mt-2 text-sm leading-6 text-charcoal/60">{{ __('auth-ui.verify_email_description', [], 'id') }}</p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-5 rounded-2xl bg-forest/5 px-4 py-3 text-sm font-medium leading-6 text-forest">
            {{ __('auth-ui.verification_link_sent', [], 'id') }}
        </div>
    @endif

    <div class="grid gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-forest px-6 py-3 text-sm font-extrabold text-white shadow-md shadow-forest/15 transition hover:-translate-y-0.5 hover:bg-forest/90 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                {{ __('auth-ui.resend_verification_email', [], 'id') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-full border border-forest/20 px-6 py-2 text-sm font-bold text-forest transition hover:bg-forest/5 focus:outline-none focus:ring-2 focus:ring-forest focus:ring-offset-2">
                {{ __('auth-ui.logout', [], 'id') }}
            </button>
        </form>
    </div>
</x-guest-layout>
