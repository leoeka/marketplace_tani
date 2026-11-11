<x-export.layout :title="__('export.rfq.thank_you_title')">
    <section class="mx-auto flex min-h-[55vh] max-w-3xl flex-col items-center justify-center px-5 py-16 text-center sm:px-8">
        <span class="grid h-16 w-16 place-items-center rounded-full bg-[#e8f1e7] text-3xl text-forest" aria-hidden="true">✓</span>
        <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-forest sm:text-4xl">{{ __('export.rfq.thank_you_title') }}</h1>
        <p class="mt-4 max-w-xl text-base leading-7 text-charcoal/65">{{ __('export.rfq.thank_you_message') }}</p>
        <a href="{{ route('export.home') }}" class="mt-8 inline-flex rounded-lg bg-forest px-6 py-3 text-sm font-bold text-white transition hover:bg-forest/90">{{ __('export.rfq.back_to_home') }}</a>
    </section>
</x-export.layout>