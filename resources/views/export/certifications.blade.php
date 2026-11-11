<x-export.layout :title="__('export.certifications.meta_title')">
    <section class="bg-[#f4f7f3]">
        <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
            <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-forest/65">{{ __('export.certifications.eyebrow') }}</p>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-forest sm:text-4xl">{{ __('export.certifications.title') }}</h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-charcoal/65">{{ __('export.certifications.description') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16 lg:px-10">
        @if ($certificates->isEmpty())
            <p class="rounded-xl border border-dashed border-forest/20 bg-[#f8faf8] px-5 py-12 text-center text-sm text-charcoal/60">{{ __('export.certifications.empty') }}</p>
        @else
            <div class="grid gap-5 md:grid-cols-2">
                @foreach ($certificates as $certificate)
                    <article class="rounded-xl border border-forest/10 bg-white p-5 shadow-[0_12px_35px_-28px_rgba(27,57,38,0.55)] sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <h2 class="text-xl font-extrabold text-forest">{{ $certificate->name }}</h2>
                            <a href="{{ asset('storage/'.$certificate->file_path) }}" target="_blank" rel="noopener" class="rounded-lg border border-forest/20 px-3 py-2 text-sm font-bold text-forest hover:bg-[#f4f7f3]">{{ __('export.certifications.view_file') }}</a>
                        </div>
                        <div class="mt-5 border-t border-forest/10 pt-4">
                            <h3 class="text-xs font-bold uppercase tracking-wide text-charcoal/55">{{ __('export.certifications.products') }}</h3>
                            <ul class="mt-2 grid gap-2">
                                @foreach ($certificate->products as $product)
                                    <li><a href="{{ route('export.products.show', $product->slug) }}" class="text-sm font-semibold text-charcoal underline decoration-forest/25 underline-offset-4 hover:text-forest">{{ $product->exportDetails->name_en }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                @endforeach
            </div>
            <x-export.pagination :paginator="$certificates" />
        @endif
    </section>
</x-export.layout>