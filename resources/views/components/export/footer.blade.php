<footer class="border-t border-forest/15 bg-[#f4f7f3]">
    <div class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-10 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
        <div>
            <p class="text-lg font-extrabold tracking-tight text-forest">{{ __('export.brand') }}</p>
            <p class="mt-1 text-sm text-charcoal/60">{{ __('export.footer.description') }}</p>
            <a href="{{ route('export.certifications.index') }}" class="mt-3 inline-flex text-sm font-semibold text-forest underline decoration-forest/30 underline-offset-4">{{ __('export.navigation.certifications') }}</a>
            <a href="{{ route('export.logistics.index') }}" class="ms-4 inline-flex text-sm font-semibold text-forest underline decoration-forest/30 underline-offset-4">{{ __('export.navigation.logistics') }}</a>
            <a href="{{ route('export.rfq.create') }}" class="ms-4 inline-flex text-sm font-semibold text-forest underline decoration-forest/30 underline-offset-4">{{ __('export.navigation.rfq') }}</a>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-forest underline decoration-forest/30 underline-offset-4 transition hover:decoration-forest">
            {{ __('export.footer.marketplace_link') }}
            <span aria-hidden="true">↗</span>
        </a>
    </div>
</footer>